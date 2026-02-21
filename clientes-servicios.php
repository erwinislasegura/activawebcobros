<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';
$clienteFiltroId = (int) ($_GET['cliente_id'] ?? 0);

$templateKey = 'suspension_servicio_urgente';

function render_html_template(string $template, array $data): string
{
    return strtr($template, $data);
}

function append_suspension_payment_button(string $bodyHtml, string $link): string
{
    if ($link === '') {
        return $bodyHtml;
    }

    if (str_contains($bodyHtml, $link) || str_contains($bodyHtml, '{{link_boton_pago}}') || str_contains($bodyHtml, 'Pagar ahora')) {
        return $bodyHtml;
    }

    $safeLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
    $buttonHtml = <<<HTML
<table width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;">
        <tr>
          <td style="padding:14px;">
            <div style="font-size:14px;font-weight:700;color:#111827;margin-bottom:6px;">Paga aquí!</div>
            <a href="{$safeLink}" style="background:#16A34A;color:#ffffff;text-decoration:none;padding:14px 18px;border-radius:999px;display:block;width:100%;box-sizing:border-box;font-size:13px;font-weight:600;text-align:center;">
              Pagar ahora
            </a>
            <div style="padding-top:8px;font-size:12px;color:#6B7280;line-height:1.5;">
              Pago seguro y protegido: este enlace pertenece al sistema oficial de cobros y protege sus datos con cifrado. Si tiene dudas, puede responder este correo para validar el pago antes de realizarlo.
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML;

    if (str_contains($bodyHtml, '<p>Atentamente,')) {
        return str_replace('<p>Atentamente,', $buttonHtml . "\n<p>Atentamente,", $bodyHtml);
    }

    if (str_contains($bodyHtml, '</body>')) {
        return str_replace('</body>', $buttonHtml . "\n</body>", $bodyHtml);
    }

    $closingCellPattern = '/<\/td>\s*<\/tr>\s*<\/table>/i';
    if (preg_match($closingCellPattern, $bodyHtml) === 1) {
        return preg_replace($closingCellPattern, "\n{$buttonHtml}\n</td></tr></table>", $bodyHtml, 1);
    }

    return $bodyHtml . "\n" . $buttonHtml;
}

function enviar_correo_suspension(string $destinatario, string $asunto, string $cuerpoHtml, string $fromEmail, string $fromName): bool
{
    $destinatario = trim($destinatario);
    $fromEmail = trim($fromEmail);
    $fromName = trim($fromName);

    if ($destinatario === '' || $fromEmail === '' || !filter_var($destinatario, FILTER_VALIDATE_EMAIL) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $asunto = trim(str_replace(["\r", "\n"], ' ', $asunto));
    $asuntoEncoded = '=?UTF-8?B?' . base64_encode($asunto !== '' ? $asunto : 'Aviso de suspensión de servicio') . '?=';

    $displayName = $fromName !== '' ? mb_encode_mimeheader($fromName, 'UTF-8') : $fromEmail;

    $messageId = sprintf('<%s.%s@%s>', time(), bin2hex(random_bytes(6)), preg_replace('/[^a-z0-9.-]/i', '', (string) (parse_url(base_url(), PHP_URL_HOST) ?: 'localhost')));

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'Date: ' . date(DATE_RFC2822),
        'Message-ID: ' . $messageId,
        'From: ' . $displayName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $fromEmail,
        'Return-Path: ' . $fromEmail,
        'X-Mailer: PHP/' . phpversion(),
        'X-Auto-Response-Suppress: OOF, AutoReply',
    ];

    $headersString = implode("\r\n", $headers);
    $extraParams = '-f ' . escapeshellarg($fromEmail);

    $enviado = @mail($destinatario, $asuntoEncoded, $cuerpoHtml, $headersString, $extraParams);
    if (!$enviado) {
        $enviado = @mail($destinatario, $asuntoEncoded, $cuerpoHtml, $headersString);
    }

    return $enviado;
}


$defaultSubject = 'Regularización de {{servicio_nombre}} para continuidad operativa';
$defaultBody = <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Suspensión de servicio</title></head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f8fafc"><tr><td align="center" style="padding:24px 12px;">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb;">
<tr><td style="padding:0;background:#DC2626;"><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="padding:16px 20px;color:#fff;font-weight:700;">{{municipalidad_nombre}}</td><td align="right" style="padding:16px 20px;color:#fee2e2;font-size:12px;white-space:nowrap;">Suspensión urgente</td></tr></table></td></tr>
<tr><td style="height:4px;background:#FCA5A5;line-height:4px;font-size:0;">&nbsp;</td></tr>
<tr><td style="padding:24px;color:#1f2937;font-size:14px;line-height:1.65;">
<p style="margin:0 0 12px 0;">Estimado/a <strong>{{cliente_nombre}}</strong>,</p>
<p style="margin:0 0 14px 0;color:#374151;">Le informamos la <strong style="color:#b91c1c;">suspensión inmediata</strong> del servicio <strong>{{servicio_nombre}}</strong> debido a pago pendiente.</p>
<table width="100%" cellpadding="0" cellspacing="0" style="margin:14px 0 18px 0;background:#fff5f5;border:1px solid #fecaca;border-radius:12px;"><tr><td style="padding:14px;">
<div style="margin-bottom:6px;"><strong>Motivo:</strong> {{motivo_suspension}}</div>
<div style="margin-bottom:6px;"><strong>Detalle:</strong> {{detalle_suspension}}</div>
<div><strong>Monto pendiente:</strong> {{monto_pendiente}}</div>
</td></tr></table>
<p style="margin:0 0 12px 0;color:#4B5563;">Queremos ayudarle a restablecer su operación cuanto antes. Mantener este saldo pendiente puede provocar interrupciones en su sitio web, correos corporativos y canales de contacto con clientes, afectando su continuidad comercial, confianza de usuarios y posicionamiento digital.</p>
<p style="margin:0 0 12px 0;color:#4B5563;">Para evitar pérdidas de visibilidad y mantener sus servicios activos, le recomendamos regularizar hoy mismo mediante el botón de pago seguro incluido en este correo. Una vez acreditado el pago, su caso podrá ser priorizado para reactivación en el menor tiempo posible.</p>
<p>Atentamente,<br><strong>Departamento de Soporte y Servicios Digitales</strong><br>{{municipalidad_nombre}}</p>
</td></tr>
</table>
</td></tr></table>
</body>
</html>
HTML;

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            servicio_id INT NOT NULL,
            motivo TEXT NULL,
            info_importante TEXT NULL,
            correo_enviado_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_cliente_servicio (cliente_id, servicio_id),
            INDEX idx_clientes_servicios_cliente (cliente_id),
            INDEX idx_clientes_servicios_servicio (servicio_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios_suspensiones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_servicio_id INT NOT NULL,
            cobro_id INT DEFAULT NULL,
            motivo TEXT NOT NULL,
            detalle TEXT NULL,
            correo_destinatario VARCHAR(180) NULL,
            correo_enviado_at DATETIME NULL,
            created_by INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_css_cliente_servicio (cliente_servicio_id),
            INDEX idx_css_cobro (cobro_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

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
    $errorMessage = 'No se pudo preparar el módulo de suspensión de servicios.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar el módulo de suspensión de servicios.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_suspension' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $suspensionId = (int) ($_POST['suspension_id'] ?? 0);
    $clienteFiltroId = (int) ($_POST['cliente_id'] ?? 0);

    if ($suspensionId <= 0) {
        $errorMessage = 'No se pudo identificar el registro de suspensión a eliminar.';
    } else {
        try {
            $stmtDelete = db()->prepare('DELETE FROM clientes_servicios_suspensiones WHERE id = ?');
            $stmtDelete->execute([$suspensionId]);
            $redirectUrl = 'clientes-servicios.php?deleted=1';
            if ($clienteFiltroId > 0) {
                $redirectUrl .= '&cliente_id=' . $clienteFiltroId;
            }
            redirect($redirectUrl);
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el registro de suspensión.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar el registro de suspensión.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'suspender' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $clienteServicioId = (int) ($_POST['cliente_servicio_id'] ?? 0);
    $cobroId = (int) ($_POST['cobro_id'] ?? 0);
    $motivo = trim($_POST['motivo'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');
    $clienteFiltroId = (int) ($_POST['cliente_id'] ?? 0);

    if ($clienteServicioId <= 0) {
        $errors[] = 'No se identificó el servicio asociado al cliente.';
    }
    if ($motivo === '') {
        $errors[] = 'Debes indicar el motivo de suspensión.';
    }

    if (empty($errors)) {
        try {
            $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];
            $fromEmail = trim((string) ($correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? ''));
            $fromName = trim((string) ($correoConfig['from_nombre'] ?? ''));

            $stmtTemplate = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
            $stmtTemplate->execute([$templateKey]);
            $template = $stmtTemplate->fetch() ?: ['subject' => $defaultSubject, 'body_html' => $defaultBody];

            $stmtDetalle = db()->prepare(
                'SELECT cs.id AS cliente_servicio_id,
                        c.id AS cliente_id,
                        c.nombre AS cliente_nombre,
                        c.correo AS cliente_correo,
                        s.nombre AS servicio_nombre,
                        s.link_boton_pago,
                        COALESCE(cb.monto, s.monto) AS monto_pendiente,
                        cb.id AS cobro_id,
                        cb.estado AS cobro_estado
                 FROM clientes_servicios cs
                 JOIN clientes c ON c.id = cs.cliente_id
                 JOIN servicios s ON s.id = cs.servicio_id
                 LEFT JOIN cobros_servicios cb ON cb.id = ?
                 WHERE cs.id = ?
                 LIMIT 1'
            );
            $stmtDetalle->execute([$cobroId > 0 ? $cobroId : null, $clienteServicioId]);
            $detalleServicio = $stmtDetalle->fetch();

            if (!$detalleServicio) {
                $errorMessage = 'No se encontró el servicio para suspensión.';
            } elseif (($detalleServicio['cliente_correo'] ?? '') === '') {
                $errorMessage = 'El cliente no tiene correo configurado para notificación.';
            } elseif ($fromEmail === '') {
                $errorMessage = 'Configura el correo remitente en Correo de envío antes de suspender.';
            } else {
                $municipalidad = get_municipalidad();
                $nombreMunicipalidad = (string) ($municipalidad['nombre'] ?? 'Nuestra institución');
                if ($fromName === '') {
                    $fromName = $nombreMunicipalidad;
                }

                $data = [
                    '{{municipalidad_nombre}}' => htmlspecialchars($nombreMunicipalidad, ENT_QUOTES, 'UTF-8'),
                    '{{cliente_nombre}}' => htmlspecialchars((string) $detalleServicio['cliente_nombre'], ENT_QUOTES, 'UTF-8'),
                    '{{servicio_nombre}}' => htmlspecialchars((string) $detalleServicio['servicio_nombre'], ENT_QUOTES, 'UTF-8'),
                    '{{motivo_suspension}}' => nl2br(htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8')),
                    '{{detalle_suspension}}' => nl2br(htmlspecialchars($detalle !== '' ? $detalle : 'No informado.', ENT_QUOTES, 'UTF-8')),
                    '{{monto_pendiente}}' => '$' . number_format((float) ($detalleServicio['monto_pendiente'] ?? 0), 2, ',', '.'),
                    '{{link_boton_pago}}' => htmlspecialchars((string) ($detalleServicio['link_boton_pago'] ?? ''), ENT_QUOTES, 'UTF-8'),
                ];

                $subject = render_html_template((string) $template['subject'], $data);
                $bodyHtml = render_html_template((string) $template['body_html'], $data);
                $bodyHtml = append_suspension_payment_button($bodyHtml, (string) ($detalleServicio['link_boton_pago'] ?? ''));

                db()->beginTransaction();
                $stmtInsert = db()->prepare('INSERT INTO clientes_servicios_suspensiones (cliente_servicio_id, cobro_id, motivo, detalle, correo_destinatario, created_by) VALUES (?, ?, ?, ?, ?, ?)');
                $stmtInsert->execute([
                    (int) $detalleServicio['cliente_servicio_id'],
                    $cobroId > 0 ? $cobroId : null,
                    $motivo,
                    $detalle !== '' ? $detalle : null,
                    (string) $detalleServicio['cliente_correo'],
                    isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null,
                ]);

                $enviado = enviar_correo_suspension((string) $detalleServicio['cliente_correo'], $subject, $bodyHtml, $fromEmail, $fromName);
                if (!$enviado) {
                    db()->rollBack();
                    $errorMessage = 'No se pudo enviar el correo urgente de suspensión. Revisa correo remitente, destinatario y configuración del servidor de correo.';
                } else {
                    $nuevoId = (int) db()->lastInsertId();
                    $stmtUpdate = db()->prepare('UPDATE clientes_servicios_suspensiones SET correo_enviado_at = NOW() WHERE id = ?');
                    $stmtUpdate->execute([$nuevoId]);
                    db()->commit();
                    redirect('clientes-servicios.php?success=1&cliente_id=' . (int) $detalleServicio['cliente_id']);
                }
            }
        } catch (Exception $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $errorMessage = 'No se pudo completar la suspensión del servicio.';
        } catch (Error $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $errorMessage = 'No se pudo completar la suspensión del servicio.';
        }
    }
}

$clientes = [];
$serviciosPendientes = [];
$suspensiones = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre FROM clientes WHERE estado = 1 ORDER BY nombre')->fetchAll();

    $sqlPendientes = 'SELECT cs.id AS cliente_servicio_id,
                             c.id AS cliente_id,
                             c.codigo AS cliente_codigo,
                             c.nombre AS cliente,
                             c.correo AS cliente_correo,
                             s.nombre AS servicio,
                             cb.id AS cobro_id,
                             cb.monto,
                             cb.estado,
                             cb.fecha_cobro,
                             cb.created_at
                      FROM clientes_servicios cs
                      JOIN clientes c ON c.id = cs.cliente_id
                      JOIN servicios s ON s.id = cs.servicio_id
                      JOIN cobros_servicios cb ON cb.cliente_id = cs.cliente_id AND cb.servicio_id = cs.servicio_id
                      WHERE LOWER(TRIM(cb.estado)) <> "pagado"';

    if ($clienteFiltroId > 0) {
        $sqlPendientes .= ' AND c.id = ' . (int) $clienteFiltroId;
    }

    $sqlPendientes .= ' ORDER BY cb.id DESC';
    $serviciosPendientes = db()->query($sqlPendientes)->fetchAll();

    $sqlSuspensiones = 'SELECT ss.id,
                               c.codigo AS cliente_codigo,
                               c.nombre AS cliente,
                               s.nombre AS servicio,
                               ss.motivo,
                               ss.detalle,
                               ss.correo_destinatario,
                               ss.correo_enviado_at,
                               ss.created_at
                        FROM clientes_servicios_suspensiones ss
                        JOIN clientes_servicios cs ON cs.id = ss.cliente_servicio_id
                        JOIN clientes c ON c.id = cs.cliente_id
                        JOIN servicios s ON s.id = cs.servicio_id';
    if ($clienteFiltroId > 0) {
        $sqlSuspensiones .= ' WHERE c.id = ' . (int) $clienteFiltroId;
    }
    $sqlSuspensiones .= ' ORDER BY ss.id DESC LIMIT 100';
    $suspensiones = db()->query($sqlSuspensiones)->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo cargar el módulo de suspensión.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo cargar el módulo de suspensión.';
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = "Suspensión de servicios"; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = "Clientes"; $title = "Suspender servicios"; include('partials/page-title.php'); ?>

            <?php if ($success === '1') : ?><div class="alert alert-success">Servicio suspendido y notificación urgente enviada.</div><?php endif; ?>
            <?php if (($_GET['deleted'] ?? '') === '1') : ?><div class="alert alert-success">Registro de suspensión eliminado correctamente.</div><?php endif; ?>
            <?php if ($errorMessage !== '') : ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="get" class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Filtrar por cliente</label>
                            <select name="cliente_id" class="form-select">
                                <option value="0">Todos los clientes</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo (int) $cliente['id']; ?>" <?php echo $clienteFiltroId === (int) $cliente['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(($cliente['codigo'] ?? '') . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Aplicar</button></div>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Servicios asociados no pagados</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Cliente</th><th>Servicio</th><th>Cobro</th><th>Monto</th><th>Motivo suspensión</th><th>Detalle</th><th>Acción</th></tr></thead>
                        <tbody>
                        <?php if (empty($serviciosPendientes)) : ?>
                            <tr><td colspan="7" class="text-center text-muted">No hay servicios asociados con cobros pendientes.</td></tr>
                        <?php else : foreach ($serviciosPendientes as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($row['cliente_codigo'] ?? '') . ' - ' . $row['cliente'], ENT_QUOTES, 'UTF-8'); ?><br><small><?php echo htmlspecialchars($row['cliente_correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small></td>
                                <td><?php echo htmlspecialchars($row['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>#<?php echo (int) $row['cobro_id']; ?><br><small><?php echo htmlspecialchars((string) ($row['estado'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small></td>
                                <td>$<?php echo number_format((float) $row['monto'], 2, ',', '.'); ?></td>
                                <td colspan="3">
                                    <form method="post" class="row g-2 js-suspension-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="action" value="suspender">
                                        <input type="hidden" name="cliente_servicio_id" value="<?php echo (int) $row['cliente_servicio_id']; ?>">
                                        <input type="hidden" name="cobro_id" value="<?php echo (int) $row['cobro_id']; ?>">
                                        <input type="hidden" name="cliente_id" value="<?php echo (int) $row['cliente_id']; ?>">
                                        <div class="col-md-3">
                                            <select class="form-select form-select-sm js-plantilla-suspension" aria-label="Plantillas de suspensión">
                                                <option value="">Plantilla rápida</option>
                                                <option value="mora" data-motivo="Facturas vencidas sin regularización." data-detalle="Suspensión preventiva por mora. Sitio y correo podrían quedar fuera de servicio hasta acreditar pago.">Mora</option>
                                                <option value="recordatorio" data-motivo="No pago luego de avisos previos." data-detalle="Sin regularización tras múltiples avisos. Se recomienda pago inmediato para evitar pérdida de continuidad digital.">Sin respuesta</option>
                                                <option value="reactivacion" data-motivo="Pendiente para reactivación." data-detalle="Regularizando hoy se gestiona reactivación prioritaria de servicios y correo corporativo.">Reactivación</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4"><input type="text" name="motivo" class="form-control form-control-sm js-motivo" placeholder="Motivo (obligatorio)" required></div>
                                        <div class="col-md-3"><input type="text" name="detalle" class="form-control form-control-sm js-detalle" placeholder="Detalle importante (web/correo sin servicio)"></div>
                                        <div class="col-md-2"><button class="btn btn-danger btn-sm w-100" type="submit">Suspender</button></div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Registro de suspensiones</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Cliente</th><th>Servicio</th><th>Motivo</th><th>Detalle</th><th>Correo</th><th>Fecha</th><th>Acción</th></tr></thead>
                        <tbody>
                        <?php if (empty($suspensiones)) : ?>
                            <tr><td colspan="7" class="text-center text-muted">Sin registros de suspensión.</td></tr>
                        <?php else : foreach ($suspensiones as $suspension) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($suspension['cliente_codigo'] ?? '') . ' - ' . $suspension['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($suspension['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo nl2br(htmlspecialchars((string) $suspension['motivo'], ENT_QUOTES, 'UTF-8')); ?></td>
                                <td><?php echo nl2br(htmlspecialchars((string) ($suspension['detalle'] ?? '-'), ENT_QUOTES, 'UTF-8')); ?></td>
                                <td><?php echo htmlspecialchars((string) ($suspension['correo_destinatario'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?><br><small><?php echo !empty($suspension['correo_enviado_at']) ? htmlspecialchars(date('d/m/Y H:i', strtotime((string) $suspension['correo_enviado_at'])), ENT_QUOTES, 'UTF-8') : 'No enviado'; ?></small></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $suspension['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('¿Eliminar este registro de suspensión?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="action" value="delete_suspension">
                                        <input type="hidden" name="suspension_id" value="<?php echo (int) $suspension['id']; ?>">
                                        <input type="hidden" name="cliente_id" value="<?php echo (int) $clienteFiltroId; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <?php include('partials/footer.php'); ?>
    </div>
</div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
<script>
document.querySelectorAll('.js-suspension-form').forEach((form) => {
    const selector = form.querySelector('.js-plantilla-suspension');
    const motivo = form.querySelector('.js-motivo');
    const detalle = form.querySelector('.js-detalle');

    if (!selector || !motivo || !detalle) return;

    selector.addEventListener('change', () => {
        const option = selector.options[selector.selectedIndex];
        const motivoText = option.getAttribute('data-motivo') || '';
        const detalleText = option.getAttribute('data-detalle') || '';

        if (motivoText !== '') motivo.value = motivoText;
        if (detalleText !== '') detalle.value = detalleText;
    });
});
</script>
</body>
</html>
