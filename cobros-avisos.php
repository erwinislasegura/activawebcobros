<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';
$successMessage = '';
$cobros = [];

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS email_templates (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            template_key VARCHAR(80) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            body_html MEDIUMTEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email_templates_key_unique (template_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

function ensure_column(string $table, string $column, string $definition): void
{
    $dbName = $GLOBALS['config']['db']['name'] ?? '';
    if ($dbName === '') {
        return;
    }
    $stmt = db()->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$dbName, $table, $column]);
    $exists = (int) $stmt->fetchColumn() > 0;
    if (!$exists) {
        db()->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
    }
}

try {
    ensure_column('cobros_servicios', 'aviso_1_enviado_at', 'DATETIME NULL');
    ensure_column('cobros_servicios', 'aviso_2_enviado_at', 'DATETIME NULL');
    ensure_column('cobros_servicios', 'aviso_3_enviado_at', 'DATETIME NULL');
} catch (Exception $e) {
} catch (Error $e) {
}

function build_aviso_template(string $primary, string $accent, string $titulo, string $mensaje): string
{
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$titulo}</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f8fafc" style="margin:0;padding:0;">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background-color:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb;">
          <tr>
            <td style="padding:0;background:{$primary};">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:16px 20px;">
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="vertical-align:middle;">
                          <img src="{{municipalidad_logo}}" alt="Logo" height="30" style="display:block;border:0;">
                        </td>
                        <td style="vertical-align:middle;padding-left:10px;color:#ffffff;font-weight:700;font-size:15px;">
                          {{municipalidad_nombre}}
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td align="right" style="padding:16px 20px;color:#e5e7eb;font-size:12px;white-space:nowrap;">
                    {$titulo}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="height:4px;background:{$accent};line-height:4px;font-size:0;">&nbsp;</td>
          </tr>
          <tr>
            <td style="padding:26px 24px 10px 24px;color:#111827;font-size:14px;line-height:1.65;">
              <p style="margin:0 0 12px 0;">Hola <strong>{{cliente_nombre}}</strong>,</p>
              <p style="margin:0 0 16px 0;color:#374151;">{$mensaje}</p>
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0 18px 0;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;">
                <tr>
                  <td style="padding:14px;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;line-height:1.6;color:#374151;">
                      <tr>
                        <td style="padding-top:6px;width:140px;color:#6B7280;"><strong>Servicio</strong></td>
                        <td style="padding-top:6px;">{{servicio_nombre}}</td>
                      </tr>
                      <tr>
                        <td style="padding-top:6px;color:#6B7280;"><strong>Monto</strong></td>
                        <td style="padding-top:6px;">{{monto}}</td>
                      </tr>
                      <tr>
                        <td style="padding-top:6px;color:#6B7280;"><strong>Fecha aviso</strong></td>
                        <td style="padding-top:6px;">{{fecha_aviso}}</td>
                      </tr>
                      <tr>
                        <td style="padding-top:6px;color:#6B7280;"><strong>Referencia</strong></td>
                        <td style="padding-top:6px;">{{referencia}}</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 12px 0;color:#4B5563;">
                Si tienes alguna consulta, puedes contactarnos respondiendo este correo.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:16px 24px 22px 24px;background:#ffffff;border-top:1px solid #eef2f7;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="font-size:12px;color:#6B7280;line-height:1.5;">
                    Mensaje automático de {{municipalidad_nombre}}.
                  </td>
                  <td align="right" style="font-size:12px;color:#6B7280;white-space:nowrap;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:999px;background:{$primary};vertical-align:middle;margin-right:6px;"></span>
                    {$titulo}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

$templateKeys = [
    'aviso_1' => 'Aviso 1',
    'aviso_2' => 'Aviso 2',
    'aviso_3' => 'Aviso 3',
];

$defaultTemplates = [
    'aviso_1' => [
        'subject' => 'Aviso 1: {{servicio_nombre}} - {{cliente_nombre}}',
        'body_html' => build_aviso_template('#1D4ED8', '#93C5FD', 'Aviso 1', 'Te recordamos el vencimiento del servicio.'),
    ],
    'aviso_2' => [
        'subject' => 'Aviso 2: {{servicio_nombre}} - {{cliente_nombre}}',
        'body_html' => build_aviso_template('#F59E0B', '#FCD34D', 'Aviso 2', 'Este es el segundo aviso de tu servicio.'),
    ],
    'aviso_3' => [
        'subject' => 'Aviso 3: {{servicio_nombre}} - {{cliente_nombre}}',
        'body_html' => build_aviso_template('#DC2626', '#FCA5A5', 'Aviso 3', 'Último aviso antes de tomar acciones adicionales.'),
    ],
];

function render_template(string $template, array $data): string
{
    return strtr($template, $data);
}

$municipalidad = get_municipalidad();
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$logoUrl = preg_match('/^https?:\/\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');

try {
    $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];
} catch (Exception $e) {
    $correoConfig = [];
} catch (Error $e) {
    $correoConfig = [];
}

$fromEmail = $correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? '';
$fromName = $correoConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? '');

try {
    $templates = [];
    foreach (array_keys($templateKeys) as $key) {
        $stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $templates[$key] = $stmt->fetch() ?: [];
        if (empty($templates[$key])) {
            $templates[$key] = $defaultTemplates[$key];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_aviso' && verify_csrf($_POST['csrf_token'] ?? null)) {
        $cobroId = (int) ($_POST['id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? '');
        $returnUrl = trim($_POST['return_url'] ?? 'cobros-avisos.php');
        $allowedReturn = ['cobros-avisos.php', 'cobros-servicios-registros.php'];

        if (!in_array($returnUrl, $allowedReturn, true)) {
            $returnUrl = 'cobros-avisos.php';
        }

        if ($cobroId <= 0 || !array_key_exists($tipo, $templateKeys)) {
            $errorMessage = 'No se pudo identificar el aviso a enviar.';
        } elseif ($fromEmail === '') {
            $errorMessage = 'Configura el correo IMAP de envío antes de enviar avisos.';
        } else {
            $stmtCobro = db()->prepare(
                'SELECT cs.id,
                        COALESCE(c.nombre, cs.cliente) AS cliente,
                        c.correo AS cliente_correo,
                        cs.referencia,
                        cs.monto,
                        cs.fecha_primer_aviso,
                        cs.fecha_segundo_aviso,
                        cs.fecha_tercer_aviso,
                        s.nombre AS servicio
                 FROM cobros_servicios cs
                 LEFT JOIN clientes c ON c.id = cs.cliente_id
                 JOIN servicios s ON s.id = cs.servicio_id
                 WHERE cs.id = ?
                 LIMIT 1'
            );
            $stmtCobro->execute([$cobroId]);
            $cobro = $stmtCobro->fetch();

            if (!$cobro) {
                $errorMessage = 'El cobro seleccionado no existe.';
            } elseif (empty($cobro['cliente_correo'])) {
                $errorMessage = 'El cliente no tiene correo configurado.';
            } else {
                $fechaAviso = null;
                $sentColumn = null;
                if ($tipo === 'aviso_1') {
                    $fechaAviso = $cobro['fecha_primer_aviso'];
                    $sentColumn = 'aviso_1_enviado_at';
                } elseif ($tipo === 'aviso_2') {
                    $fechaAviso = $cobro['fecha_segundo_aviso'];
                    $sentColumn = 'aviso_2_enviado_at';
                } elseif ($tipo === 'aviso_3') {
                    $fechaAviso = $cobro['fecha_tercer_aviso'];
                    $sentColumn = 'aviso_3_enviado_at';
                }

                if ($fechaAviso === null || $fechaAviso === '') {
                    $errorMessage = 'El aviso seleccionado no tiene fecha programada.';
                } else {
                    $template = $templates[$tipo] ?? $defaultTemplates[$tipo];
                    $data = [
                        '{{municipalidad_nombre}}' => $municipalidad['nombre'] ?? 'Empresa',
                        '{{municipalidad_logo}}' => $logoUrl,
                        '{{cliente_nombre}}' => (string) $cobro['cliente'],
                        '{{servicio_nombre}}' => (string) $cobro['servicio'],
                        '{{monto}}' => '$' . number_format((float) $cobro['monto'], 2, ',', '.'),
                        '{{fecha_aviso}}' => date('d/m/Y', strtotime((string) $fechaAviso)),
                        '{{referencia}}' => (string) ($cobro['referencia'] ?? ''),
                    ];
                    $subject = render_template($template['subject'], $data);
                    $bodyHtml = render_template($template['body_html'], $data);
                    $headers = [
                        'MIME-Version: 1.0',
                        'Content-type: text/html; charset=UTF-8',
                        'From: ' . ($fromName !== '' ? $fromName : $fromEmail) . ' <' . $fromEmail . '>',
                    ];

                    if (@mail($cobro['cliente_correo'], $subject, $bodyHtml, implode("\r\n", $headers))) {
                        if ($sentColumn !== null) {
                            $stmtUpdate = db()->prepare("UPDATE cobros_servicios SET {$sentColumn} = NOW() WHERE id = ?");
                            $stmtUpdate->execute([$cobroId]);
                        }
                        $successMessage = 'Aviso enviado correctamente.';
                    } else {
                        $errorMessage = 'No se pudo enviar el aviso.';
                    }
                }
            }
        }

        if ($errorMessage === '' && $successMessage !== '') {
            redirect($returnUrl);
        }
    }

    $cobros = db()->query(
        'SELECT cs.id,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                c.codigo AS cliente_codigo,
                c.color_hex AS cliente_color,
                c.correo AS cliente_correo,
                cs.referencia,
                cs.monto,
                cs.fecha_primer_aviso,
                cs.fecha_segundo_aviso,
                cs.fecha_tercer_aviso,
                cs.aviso_1_enviado_at,
                cs.aviso_2_enviado_at,
                cs.aviso_3_enviado_at,
                cs.estado,
                s.nombre AS servicio
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = 'No se pudieron cargar los avisos.';
} catch (Error $e) {
    $errorMessage = 'No se pudieron cargar los avisos.';
}

?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listado de avisos"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Cobros de servicios"; $title = "Listado de avisos"; include('partials/page-title.php'); ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php if ($successMessage !== '') : ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title mb-0">Avisos por cliente</h5>
                            <p class="text-muted mb-0">Gestiona y envía avisos asociados a cobros.</p>
                        </div>
                        <span class="badge text-bg-primary"><?php echo count($cobros); ?> registros</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Referencia</th>
                                        <th>Avisos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($cobros)) : ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No hay avisos registrados.</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($cobros as $cobro) : ?>
                                            <?php
                                            $correo = $cobro['cliente_correo'] ?? '';
                                            $cliente = (string) $cobro['cliente'];
                                            $servicio = (string) $cobro['servicio'];
                                            $referencia = (string) ($cobro['referencia'] ?? '');
                                            $avisos = [
                                                [
                                                    'key' => 'aviso_1',
                                                    'label' => 'Aviso 1',
                                                    'fecha' => $cobro['fecha_primer_aviso'] ?? null,
                                                    'sent' => $cobro['aviso_1_enviado_at'] ?? null,
                                                ],
                                                [
                                                    'key' => 'aviso_2',
                                                    'label' => 'Aviso 2',
                                                    'fecha' => $cobro['fecha_segundo_aviso'] ?? null,
                                                    'sent' => $cobro['aviso_2_enviado_at'] ?? null,
                                                ],
                                                [
                                                    'key' => 'aviso_3',
                                                    'label' => 'Aviso 3',
                                                    'fecha' => $cobro['fecha_tercer_aviso'] ?? null,
                                                    'sent' => $cobro['aviso_3_enviado_at'] ?? null,
                                                ],
                                            ];
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($cobro['cliente_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8'); ?>;">
                                                        <?php echo htmlspecialchars($cobro['cliente_codigo'] ?? 'SIN-COD', ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <?php echo htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($servicio, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($referencia, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <div class="d-flex flex-column gap-2">
                                                        <?php foreach ($avisos as $aviso) : ?>
                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                <span class="badge text-bg-light"><?php echo htmlspecialchars($aviso['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                                <span class="text-muted small">
                                                                    <?php echo $aviso['fecha'] ? htmlspecialchars(date('d/m/Y', strtotime($aviso['fecha'])), ENT_QUOTES, 'UTF-8') : 'Sin fecha'; ?>
                                                                </span>
                                                                <?php if (!empty($aviso['sent'])) : ?>
                                                                    <span class="badge text-bg-success">Enviado</span>
                                                                    <span class="text-muted small">
                                                                        <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($aviso['sent'])), ENT_QUOTES, 'UTF-8'); ?>
                                                                    </span>
                                                                <?php else : ?>
                                                                    <span class="badge text-bg-warning">Pendiente</span>
                                                                <?php endif; ?>
                                                                <form method="post" class="d-inline">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <input type="hidden" name="action" value="send_aviso">
                                                                    <input type="hidden" name="id" value="<?php echo (int) $cobro['id']; ?>">
                                                                    <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($aviso['key'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <?php
                                                                    $disabled = ($correo === '' || $correo === null || $aviso['fecha'] === null || $aviso['fecha'] === '' || $fromEmail === '');
                                                                    ?>
                                                                    <button type="submit" class="btn btn-sm btn-outline-primary" <?php echo $disabled ? 'disabled' : ''; ?>>
                                                                        <?php echo !empty($aviso['sent']) ? 'Reenviar' : 'Enviar'; ?>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Los botones de envío se habilitan si el cliente tiene correo registrado y el correo IMAP está configurado.</small>
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

</body>

</html>
