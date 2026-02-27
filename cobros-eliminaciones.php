<?php
require __DIR__ . '/app/bootstrap.php';

$templateKey = 'eliminacion_servicio_suspendido';
$errorMessage = '';
$successMessage = '';
$municipalidad = get_municipalidad();

function render_html_template(string $template, array $data): string
{
    return strtr($template, $data);
}

function parse_recipient_emails(?string $raw): array
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return [];
    }

    $parts = preg_split('/[;,\s]+/', $raw) ?: [];
    $emails = [];
    foreach ($parts as $email) {
        $email = trim($email);
        if ($email === '') {
            continue;
        }
        $email = mb_strtolower($email, 'UTF-8');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        if (!in_array($email, $emails, true)) {
            $emails[] = $email;
        }
    }

    return $emails;
}

function enviar_correo_eliminacion(string $destinatario, string $asunto, string $cuerpoHtml, string $fromEmail, string $fromName): bool
{
    $destinatario = trim($destinatario);
    $fromEmail = trim($fromEmail);
    $fromName = trim($fromName);

    if ($destinatario === '' || $fromEmail === '' || !filter_var($destinatario, FILTER_VALIDATE_EMAIL) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $asunto = trim(str_replace(["\r", "\n"], ' ', $asunto));
    $asuntoEncoded = '=?UTF-8?B?' . base64_encode($asunto !== '' ? $asunto : 'Notificación de eliminación de servicio') . '?=';
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
    return @mail($destinatario, $asuntoEncoded, $cuerpoHtml, $headersString);
}

$defaultSubject = 'Aviso de eliminación definitiva: {{servicio_nombre}}';
$defaultBody = <<<'HTML'
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Eliminación de servicio</title></head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f8fafc"><tr><td align="center" style="padding:24px 12px;">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">
<tr><td style="padding:0;background:#111827;"><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="padding:16px 20px;color:#fff;font-weight:700;">{{municipalidad_nombre}}</td><td align="right" style="padding:16px 20px;color:#e5e7eb;font-size:12px;white-space:nowrap;">Aviso importante</td></tr></table></td></tr>
<tr><td style="height:4px;background:#EF4444;line-height:4px;font-size:0;">&nbsp;</td></tr>
<tr><td style="padding:24px;color:#1f2937;font-size:14px;line-height:1.65;">
<p style="margin:0 0 12px 0;">Estimado/a <strong>{{cliente_nombre}}</strong>,</p>
<p style="margin:0 0 14px 0;color:#374151;">Le informamos que el servicio <strong>{{servicio_nombre}}</strong>, actualmente en estado de suspensión, será <strong style="color:#b91c1c;">eliminado de forma definitiva</strong>.</p>
<table width="100%" cellpadding="0" cellspacing="0" style="margin:14px 0 18px 0;background:#fff5f5;border:1px solid #fecaca;border-radius:12px;"><tr><td style="padding:14px;">
<div style="margin-bottom:6px;"><strong>Motivo de suspensión:</strong> {{motivo_suspension}}</div>
<div style="margin-bottom:6px;"><strong>Detalle:</strong> {{detalle_suspension}}</div>
<div><strong>Fecha de eliminación informada:</strong> {{fecha_notificacion}}</div>
</td></tr></table>
<p style="margin:0 0 12px 0;color:#4B5563;">Si requiere revisar antecedentes o regularizar su situación, favor contactar al equipo de soporte y cobranzas a la brevedad.</p>
<p style="margin:0;">Atentamente,<br><strong>Departamento de Soporte y Servicios Digitales</strong><br>{{municipalidad_nombre}}</p>
</td></tr></table>
</td></tr></table>
</body></html>
HTML;

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

    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios_eliminaciones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            suspension_id INT NOT NULL,
            cliente_servicio_id INT NOT NULL,
            cobro_id INT DEFAULT NULL,
            correo_destinatario VARCHAR(180) NULL,
            correo_enviado_at DATETIME NULL,
            created_by INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_css_eliminacion (suspension_id),
            INDEX idx_cse_cliente_servicio (cliente_servicio_id),
            INDEX idx_cse_cobro (cobro_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar el módulo de eliminación de servicios.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar el módulo de eliminación de servicios.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'notify_eliminacion' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $suspensionId = (int) ($_POST['suspension_id'] ?? 0);

    if ($suspensionId <= 0) {
        $errorMessage = 'No se pudo identificar el servicio suspendido.';
    } else {
        try {
            $stmtDetalle = db()->prepare(
                'SELECT ss.id AS suspension_id,
                        ss.cliente_servicio_id,
                        ss.cobro_id,
                        ss.motivo,
                        ss.detalle,
                        c.nombre AS cliente_nombre,
                        c.correo AS cliente_correo,
                        s.nombre AS servicio_nombre
                 FROM clientes_servicios_suspensiones ss
                 JOIN clientes_servicios cs ON cs.id = ss.cliente_servicio_id
                 JOIN clientes c ON c.id = cs.cliente_id
                 JOIN servicios s ON s.id = cs.servicio_id
                 LEFT JOIN clientes_servicios_eliminaciones se ON se.suspension_id = ss.id
                 WHERE ss.id = ?
                   AND se.id IS NULL
                 LIMIT 1'
            );
            $stmtDetalle->execute([$suspensionId]);
            $detalle = $stmtDetalle->fetch();

            $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];
            $fromEmail = trim((string) ($correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? ''));
            $fromName = trim((string) ($correoConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? 'Soporte')));

            $stmtTemplate = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
            $stmtTemplate->execute([$templateKey]);
            $template = $stmtTemplate->fetch() ?: ['subject' => $defaultSubject, 'body_html' => $defaultBody];

            if (!$detalle) {
                $errorMessage = 'La suspensión no existe o ya fue notificada para eliminación.';
            } elseif ($fromEmail === '') {
                $errorMessage = 'Configura el correo remitente en Correo avisos antes de notificar.';
            } else {
                $recipientEmails = parse_recipient_emails((string) ($detalle['cliente_correo'] ?? ''));
                if (empty($recipientEmails)) {
                    $errorMessage = 'El cliente no tiene correos válidos configurados.';
                } else {
                    $data = [
                        '{{municipalidad_nombre}}' => htmlspecialchars((string) ($municipalidad['nombre'] ?? 'Institución'), ENT_QUOTES, 'UTF-8'),
                        '{{cliente_nombre}}' => htmlspecialchars((string) $detalle['cliente_nombre'], ENT_QUOTES, 'UTF-8'),
                        '{{servicio_nombre}}' => htmlspecialchars((string) $detalle['servicio_nombre'], ENT_QUOTES, 'UTF-8'),
                        '{{motivo_suspension}}' => nl2br(htmlspecialchars((string) ($detalle['motivo'] ?? 'No informado'), ENT_QUOTES, 'UTF-8')),
                        '{{detalle_suspension}}' => nl2br(htmlspecialchars((string) ($detalle['detalle'] ?? 'No informado'), ENT_QUOTES, 'UTF-8')),
                        '{{fecha_notificacion}}' => date('d/m/Y H:i'),
                    ];

                    $subject = render_html_template((string) ($template['subject'] ?? $defaultSubject), $data);
                    $bodyHtml = render_html_template((string) ($template['body_html'] ?? $defaultBody), $data);

                    $sent = true;
                    foreach ($recipientEmails as $recipientEmail) {
                        if (!enviar_correo_eliminacion($recipientEmail, $subject, $bodyHtml, $fromEmail, $fromName)) {
                            $sent = false;
                            break;
                        }
                    }

                    if (!$sent) {
                        $errorMessage = 'No se pudo enviar la notificación de eliminación.';
                    } else {
                        $stmtInsert = db()->prepare('INSERT INTO clientes_servicios_eliminaciones (suspension_id, cliente_servicio_id, cobro_id, correo_destinatario, correo_enviado_at, created_by) VALUES (?, ?, ?, ?, NOW(), ?)');
                        $stmtInsert->execute([
                            (int) $detalle['suspension_id'],
                            (int) $detalle['cliente_servicio_id'],
                            !empty($detalle['cobro_id']) ? (int) $detalle['cobro_id'] : null,
                            (string) ($detalle['cliente_correo'] ?? ''),
                            isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null,
                        ]);

                        $successMessage = 'Notificación de eliminación enviada correctamente.';
                    }
                }
            }
        } catch (Exception $e) {
            $errorMessage = 'No se pudo procesar la notificación de eliminación.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo procesar la notificación de eliminación.';
        }
    }
}

$suspendidosPendientes = [];
$notificaciones = [];
try {
    $suspendidosPendientes = db()->query(
        'SELECT ss.id AS suspension_id,
                ss.motivo,
                ss.detalle,
                ss.created_at AS suspension_created_at,
                c.codigo AS cliente_codigo,
                c.nombre AS cliente,
                c.correo AS cliente_correo,
                s.nombre AS servicio,
                cb.referencia,
                cb.monto
         FROM clientes_servicios_suspensiones ss
         JOIN clientes_servicios cs ON cs.id = ss.cliente_servicio_id
         JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         LEFT JOIN cobros_servicios cb ON cb.id = ss.cobro_id
         LEFT JOIN clientes_servicios_eliminaciones se ON se.suspension_id = ss.id
         WHERE se.id IS NULL
         ORDER BY ss.id DESC'
    )->fetchAll();

    $notificaciones = db()->query(
        'SELECT se.id,
                se.correo_destinatario,
                se.correo_enviado_at,
                se.created_at,
                c.codigo AS cliente_codigo,
                c.nombre AS cliente,
                s.nombre AS servicio
         FROM clientes_servicios_eliminaciones se
         JOIN clientes_servicios cs ON cs.id = se.cliente_servicio_id
         JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY se.id DESC
         LIMIT 100'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo cargar el módulo de eliminaciones.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo cargar el módulo de eliminaciones.';
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = "Notificación eliminación de servicios"; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = "Cobros"; $title = "Notificar eliminación de servicios suspendidos"; include('partials/page-title.php'); ?>

            <?php if ($successMessage !== '') : ?><div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if ($errorMessage !== '') : ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Servicios suspendidos pendientes de notificación</h5>
                    <span class="badge text-bg-warning"><?php echo count($suspendidosPendientes); ?> pendientes</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                                <th>Correo</th>
                                <th>Motivo</th>
                                <th>Suspendido</th>
                                <th class="text-end">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($suspendidosPendientes)) : ?>
                                <tr><td colspan="8" class="text-center text-muted">No hay servicios suspendidos pendientes de notificación.</td></tr>
                            <?php else : foreach ($suspendidosPendientes as $item) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string) ($item['cliente_codigo'] ?? '') . ' - ' . (string) $item['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $item['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($item['referencia'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo '$' . number_format((float) ($item['monto'] ?? 0), 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($item['cliente_correo'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars((string) ($item['motivo'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $item['suspension_created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end">
                                        <form method="post" class="d-inline" onsubmit="return confirm('¿Enviar notificación de eliminación para este servicio suspendido?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="action" value="notify_eliminacion">
                                            <input type="hidden" name="suspension_id" value="<?php echo (int) $item['suspension_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Notificar eliminación</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2">La plantilla usada para el correo se configura en <strong>Mantenedores → Plantilla eliminación servicio</strong>.</small>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Notificaciones enviadas</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Correo</th>
                                <th>Enviado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($notificaciones)) : ?>
                                <tr><td colspan="4" class="text-center text-muted">No hay notificaciones registradas.</td></tr>
                            <?php else : foreach ($notificaciones as $log) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string) ($log['cliente_codigo'] ?? '') . ' - ' . (string) $log['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $log['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($log['correo_destinatario'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo !empty($log['correo_enviado_at']) ? htmlspecialchars(date('d/m/Y H:i', strtotime((string) $log['correo_enviado_at'])), ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <?php include('partials/footer.php'); ?>
    </div>
</div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
</body>
</html>
