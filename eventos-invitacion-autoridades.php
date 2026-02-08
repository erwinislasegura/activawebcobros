<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$notice = '';
$eventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS event_authority_attendance (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id INT UNSIGNED NOT NULL,
            authority_id INT UNSIGNED NOT NULL,
            token VARCHAR(64) NOT NULL,
            status ENUM("pendiente", "confirmado", "rechazado") NOT NULL DEFAULT "pendiente",
            responded_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY event_authority_attendance_unique (event_id, authority_id),
            UNIQUE KEY event_authority_attendance_token_unique (token),
            CONSTRAINT event_authority_attendance_event_id_fk FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE,
            CONSTRAINT event_authority_attendance_authority_id_fk FOREIGN KEY (authority_id) REFERENCES authorities (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

$events = db()->query('SELECT id, titulo, descripcion, ubicacion, fecha_inicio, fecha_fin, tipo, estado FROM events ORDER BY fecha_inicio DESC')->fetchAll();
$selectedEvent = null;
$authorities = [];
$sentMap = [];

if ($eventId > 0) {
    $stmtEvent = db()->prepare(
        'SELECT e.*, u.nombre AS encargado_nombre, u.apellido AS encargado_apellido, u.correo AS encargado_correo, u.telefono AS encargado_telefono
         FROM events e
         LEFT JOIN users u ON u.id = e.encargado_id
         WHERE e.id = ?'
    );
    $stmtEvent->execute([$eventId]);
    $selectedEvent = $stmtEvent->fetch() ?: null;

    if ($selectedEvent) {
        $stmtAuthorities = db()->prepare(
            'SELECT a.id,
                    a.nombre,
                    a.tipo,
                    a.correo,
                    g.nombre AS grupo_nombre,
                    i.correo_enviado,
                    i.sent_at
             FROM event_authorities ea
             INNER JOIN authorities a ON a.id = ea.authority_id
             LEFT JOIN authority_groups g ON g.id = a.group_id
             LEFT JOIN event_authority_invitations i ON i.event_id = ea.event_id AND i.authority_id = ea.authority_id
             WHERE ea.event_id = ?
             ORDER BY COALESCE(g.nombre, ""), a.nombre'
        );
        $stmtAuthorities->execute([$eventId]);
        $authorities = $stmtAuthorities->fetchAll();
    }
}

$defaultSubject = 'Invitación institucional: {{evento_titulo}}';
$defaultBody = <<<HTML
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Invitación institucional</title>
  </head>
  <body style="margin:0;padding:0;background-color:#eef2f7;font-family:Arial,sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;background-color:#eef2f7;">
      <tr>
        <td align="center">
          <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 28px rgba(15,23,42,0.08);">
            <tr>
              <td style="background:linear-gradient(120deg,#0f4c81,#163a6b);padding:24px 32px;color:#ffffff;">
                <img src="{{municipalidad_logo}}" alt="Logo" style="height:28px;vertical-align:middle;">
                <span style="font-size:18px;font-weight:bold;margin-left:12px;vertical-align:middle;">{{municipalidad_nombre}}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:32px;">
                <p style="margin:0 0 12px;font-size:16px;">Estimado(a) {{destinatario_nombre}},</p>
                <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">
                  La Municipalidad de {{municipalidad_nombre}} le invita cordialmente al evento <strong>{{evento_titulo}}</strong>.
                </p>
                <div style="background-color:#f8fafc;border-radius:14px;padding:16px 20px;margin-bottom:18px;">
                  <p style="margin:0 0 6px;font-size:12px;color:#64748b;">Detalles del evento</p>
                  <p style="margin:0;font-size:15px;font-weight:bold;color:#0f172a;">{{evento_titulo}}</p>
                  <p style="margin:6px 0 0;font-size:13px;color:#475569;">{{evento_ubicacion}} · {{evento_tipo}}</p>
                  <p style="margin:6px 0 0;font-size:13px;color:#475569;">{{evento_fecha_inicio}} - {{evento_fecha_fin}}</p>
                  <p style="margin:6px 0 0;font-size:13px;color:#475569;">Público objetivo: {{evento_publico_objetivo}}</p>
                  <p style="margin:6px 0 0;font-size:13px;color:#475569;">Cupos disponibles: {{evento_cupos}}</p>
                  <p style="margin:10px 0 0;font-size:13px;color:#475569;">{{evento_descripcion}}</p>
                </div>
                <p style="margin:0 0 12px;font-size:13px;color:#475569;">
                  Contacto del evento: {{evento_encargado_nombre}} · {{evento_encargado_correo}} · {{evento_encargado_telefono}}
                </p>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0 10px 0;">
                  <tr>
                    <td align="center">
                      <a href="{{confirmacion_link}}"
                         style="background:#0f4c81;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:bold;display:inline-block;">
                        Confirmar participación
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" style="font-size:12px;color:#64748b;padding-top:10px;">
                      Este enlace es personal y válido únicamente para su invitación.
                    </td>
                  </tr>
                </table>
                <div style="margin:16px 0 0 0;padding:12px 14px;border:1px dashed #cbd5f5;border-radius:12px;background:#ffffff;">
                  <p style="margin:0 0 6px;font-size:12px;color:#64748b;">
                    Si el botón no funciona, copie y pegue este enlace en su navegador:
                  </p>
                  <p style="margin:0;word-break:break-all;font-size:13px;">
                    <a href="{{confirmacion_link}}" style="color:#0f4c81;text-decoration:underline;">
                      {{confirmacion_link}}
                    </a>
                  </p>
                </div>
                <p style="margin:0;font-size:12px;color:#94a3b8;">
                  Este mensaje fue generado automáticamente por el sistema municipal.
                </p>
              </td>
            </tr>
            <tr>
              <td style="background-color:#f8fafc;padding:16px 32px;text-align:center;font-size:12px;color:#94a3b8;">
                Municipalidad de {{municipalidad_nombre}} · Invitación oficial
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;

function render_invitation_template(string $template, array $data): string
{
    $replacements = [
        '{{municipalidad_nombre}}' => $data['municipalidad_nombre'] ?? '',
        '{{municipalidad_logo}}' => $data['municipalidad_logo'] ?? '',
        '{{destinatario_nombre}}' => $data['destinatario_nombre'] ?? '',
        '{{destinatario_cargo}}' => $data['destinatario_cargo'] ?? '',
        '{{evento_titulo}}' => $data['evento_titulo'] ?? '',
        '{{evento_descripcion}}' => $data['evento_descripcion'] ?? '',
        '{{evento_fecha_inicio}}' => $data['evento_fecha_inicio'] ?? '',
        '{{evento_fecha_fin}}' => $data['evento_fecha_fin'] ?? '',
        '{{evento_ubicacion}}' => $data['evento_ubicacion'] ?? '',
        '{{evento_tipo}}' => $data['evento_tipo'] ?? '',
        '{{evento_publico_objetivo}}' => $data['evento_publico_objetivo'] ?? '',
        '{{evento_cupos}}' => $data['evento_cupos'] ?? '',
        '{{evento_encargado_nombre}}' => $data['evento_encargado_nombre'] ?? '',
        '{{evento_encargado_correo}}' => $data['evento_encargado_correo'] ?? '',
        '{{evento_encargado_telefono}}' => $data['evento_encargado_telefono'] ?? '',
        '{{confirmacion_link}}' => $data['confirmacion_link'] ?? '',
    ];

    return strtr($template, $replacements);
}

try {
    $stmtTemplate = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
    $stmtTemplate->execute(['invitacion_autoridades']);
    $emailTemplate = $stmtTemplate->fetch() ?: null;
} catch (Exception $e) {
    $emailTemplate = null;
} catch (Error $e) {
    $emailTemplate = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
    $authorityIds = $_POST['authority_ids'] ?? [];
    $authorityIds = array_values(array_filter(array_map('intval', (array) $authorityIds)));

    if ($eventId <= 0) {
        $errors[] = 'Selecciona un evento válido.';
    }

    if (empty($authorityIds)) {
        $errors[] = 'Selecciona al menos una autoridad para enviar la invitación.';
    }

    if (empty($errors)) {
        $stmtEvent = db()->prepare(
            'SELECT e.*, u.nombre AS encargado_nombre, u.apellido AS encargado_apellido, u.correo AS encargado_correo, u.telefono AS encargado_telefono
             FROM events e
             LEFT JOIN users u ON u.id = e.encargado_id
             WHERE e.id = ?'
        );
        $stmtEvent->execute([$eventId]);
        $selectedEvent = $stmtEvent->fetch() ?: null;

        if (!$selectedEvent) {
            $errors[] = 'No se encontró el evento seleccionado.';
        }
    }

    if (empty($errors) && $selectedEvent) {
        $placeholders = implode(',', array_fill(0, count($authorityIds), '?'));
        $stmtAuthorities = db()->prepare(
            "SELECT a.id, a.nombre, a.tipo, a.correo, g.nombre AS grupo_nombre
             FROM authorities a
             LEFT JOIN authority_groups g ON g.id = a.group_id
             WHERE a.id IN ($placeholders)"
        );
        $stmtAuthorities->execute($authorityIds);
        $authoritiesToSend = $stmtAuthorities->fetchAll();

        if (empty($authoritiesToSend)) {
            $errors[] = 'No hay autoridades válidas con correo para enviar.';
        }
    }

    if (empty($errors) && $selectedEvent) {
        $municipalidad = get_municipalidad();
        $logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
        $logoUrl = preg_match('/^https?:\/\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');

        $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch();
        $fromEmail = $correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? null;
        $fromName = $correoConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? 'Municipalidad');

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        if ($fromEmail) {
            $headers .= 'From: ' . ($fromName ? $fromName . ' <' . $fromEmail . '>' : $fromEmail) . "\r\n";
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($authoritiesToSend as $authority) {
            $correo = trim($authority['correo'] ?? '');
            if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $failedCount++;
                continue;
            }

            $encargadoNombre = trim(($selectedEvent['encargado_nombre'] ?? '') . ' ' . ($selectedEvent['encargado_apellido'] ?? ''));
            $confirmToken = hash('sha256', $eventId . '|' . $authority['id'] . '|' . $correo);
            $templateData = [
                'municipalidad_nombre' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
                'municipalidad_logo' => htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'),
                'destinatario_nombre' => htmlspecialchars($authority['nombre'] ?? 'Autoridad', ENT_QUOTES, 'UTF-8'),
                'destinatario_cargo' => htmlspecialchars($authority['tipo'] ?? '', ENT_QUOTES, 'UTF-8'),
                'evento_titulo' => htmlspecialchars($selectedEvent['titulo'] ?? '', ENT_QUOTES, 'UTF-8'),
                'evento_descripcion' => nl2br(htmlspecialchars($selectedEvent['descripcion'] ?? '', ENT_QUOTES, 'UTF-8')),
                'evento_fecha_inicio' => htmlspecialchars($selectedEvent['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8'),
                'evento_fecha_fin' => htmlspecialchars($selectedEvent['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8'),
                'evento_ubicacion' => htmlspecialchars($selectedEvent['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8'),
                'evento_tipo' => htmlspecialchars($selectedEvent['tipo'] ?? '', ENT_QUOTES, 'UTF-8'),
                'evento_publico_objetivo' => htmlspecialchars($selectedEvent['publico_objetivo'] ?? 'Sin información', ENT_QUOTES, 'UTF-8'),
                'evento_cupos' => htmlspecialchars($selectedEvent['cupos'] ?? 'Sin cupos definidos', ENT_QUOTES, 'UTF-8'),
                'evento_encargado_nombre' => htmlspecialchars($encargadoNombre !== '' ? $encargadoNombre : 'Sin asignar', ENT_QUOTES, 'UTF-8'),
                'evento_encargado_correo' => htmlspecialchars($selectedEvent['encargado_correo'] ?? 'Sin correo', ENT_QUOTES, 'UTF-8'),
                'evento_encargado_telefono' => htmlspecialchars($selectedEvent['encargado_telefono'] ?? 'Sin teléfono', ENT_QUOTES, 'UTF-8'),
                'confirmacion_link' => base_url() . '/confirmar-asistencia.php?token=' . $confirmToken,
            ];

            $bodyHtml = render_invitation_template($emailTemplate['body_html'] ?? $defaultBody, $templateData);
            $subject = render_invitation_template($emailTemplate['subject'] ?? $defaultSubject, [
                'municipalidad_nombre' => $municipalidad['nombre'] ?? 'Municipalidad',
                'destinatario_nombre' => $authority['nombre'] ?? 'Autoridad',
                'evento_titulo' => $selectedEvent['titulo'] ?? '',
                'evento_fecha_inicio' => $selectedEvent['fecha_inicio'] ?? '',
                'evento_fecha_fin' => $selectedEvent['fecha_fin'] ?? '',
                'evento_ubicacion' => $selectedEvent['ubicacion'] ?? '',
                'evento_tipo' => $selectedEvent['tipo'] ?? '',
            ]);

            $mailSent = mail($correo, $subject, $bodyHtml, $headers);
            if ($mailSent) {
                $sentCount++;
                $stmtAttendance = db()->prepare(
                    'INSERT INTO event_authority_attendance (event_id, authority_id, token)
                     VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE token = VALUES(token)'
                );
                $stmtAttendance->execute([$eventId, $authority['id'], $confirmToken]);
                $stmtFind = db()->prepare('SELECT id FROM event_authority_invitations WHERE event_id = ? AND authority_id = ? LIMIT 1');
                $stmtFind->execute([$eventId, $authority['id']]);
                $existingId = $stmtFind->fetchColumn();
                if ($existingId) {
                    $stmtUpdate = db()->prepare('UPDATE event_authority_invitations SET destinatario_correo = ?, correo_enviado = 1, sent_at = NOW() WHERE id = ?');
                    $stmtUpdate->execute([$correo, $existingId]);
                } else {
                    $stmtInsert = db()->prepare('INSERT INTO event_authority_invitations (event_id, authority_id, destinatario_correo, correo_enviado, sent_at) VALUES (?, ?, ?, 1, NOW())');
                    $stmtInsert->execute([$eventId, $authority['id'], $correo]);
                }
            } else {
                $failedCount++;
            }
        }

        if ($sentCount > 0 && $failedCount === 0) {
            $notice = 'Invitaciones enviadas correctamente (' . $sentCount . ').';
        } elseif ($sentCount > 0) {
            $notice = 'Invitaciones enviadas: ' . $sentCount . '. No se pudieron enviar ' . $failedCount . '.';
        } else {
            $errors[] = 'No se pudo enviar ninguna invitación.';
        }

        $eventId = $selectedEvent['id'];
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Invitación autoridades'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = 'Eventos Municipales'; $title = 'Invitación autoridades'; include('partials/page-title.php'); ?>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) : ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($notice !== '') : ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Invitar autoridades por evento</h5>
                                    <p class="text-muted mb-0">Selecciona el evento y envía invitaciones personalizadas por correo.</p>
                                </div>
                                <button type="submit" form="invitacion-autoridades-form" class="btn btn-primary">Enviar invitaciones</button>
                            </div>
                            <div class="card-body">
                                <form id="invitacion-autoridades-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="send">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="event-id">Evento</label>
                                            <select id="event-id" name="event_id" class="form-select" onchange="window.location='eventos-invitacion-autoridades.php?event_id=' + this.value">
                                                <option value="">Selecciona un evento</option>
                                                <?php foreach ($events as $event) : ?>
                                                    <option value="<?php echo (int) $event['id']; ?>" <?php echo $eventId === (int) $event['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if ($selectedEvent) : ?>
                                        <div class="border rounded-3 p-3 bg-light mb-3">
                                            <div class="fw-semibold"><?php echo htmlspecialchars($selectedEvent['titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="text-muted small">
                                                <?php echo htmlspecialchars($selectedEvent['ubicacion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> ·
                                                <?php echo htmlspecialchars($selectedEvent['fecha_inicio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        </div>

                                        <?php if (empty($authorities)) : ?>
                                            <div class="alert alert-warning">El evento aún no tiene autoridades asociadas.</div>
                                        <?php else : ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-centered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:40px;">
                                                                <input type="checkbox" id="check-all">
                                                            </th>
                                                            <th>Autoridad</th>
                                                            <th>Grupo</th>
                                                            <th>Correo</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($authorities as $authority) : ?>
                                                            <?php
                                                            $correoValido = !empty($authority['correo']) && filter_var($authority['correo'], FILTER_VALIDATE_EMAIL);
                                                            $sent = (int) ($authority['correo_enviado'] ?? 0) === 1;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" name="authority_ids[]" value="<?php echo (int) $authority['id']; ?>" <?php echo $correoValido ? '' : 'disabled'; ?>>
                                                                </td>
                                                                <td>
                                                                    <div class="fw-semibold"><?php echo htmlspecialchars($authority['nombre'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                                    <div class="text-muted small"><?php echo htmlspecialchars($authority['tipo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($authority['grupo_nombre'] ?? 'Sin grupo', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                <td><?php echo htmlspecialchars($authority['correo'] ?? 'Sin correo', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                <td>
                                                                    <?php if ($sent) : ?>
                                                                        <span class="badge text-bg-success">Enviado</span>
                                                                    <?php elseif ($correoValido) : ?>
                                                                        <span class="badge text-bg-warning">Pendiente</span>
                                                                    <?php else : ?>
                                                                        <span class="badge text-bg-secondary">Sin correo</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

    <script>
        const checkAll = document.getElementById('check-all');
        if (checkAll) {
            checkAll.addEventListener('change', (event) => {
                document.querySelectorAll('input[name="authority_ids[]"]:not(:disabled)').forEach((checkbox) => {
                    checkbox.checked = event.target.checked;
                });
            });
        }
    </script>

</body>

</html>
