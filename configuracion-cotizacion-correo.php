<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = false;
$templateKey = 'cotizacion_cliente';

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

$defaultSubject = 'Cotización {{codigo_cotizacion}} · {{municipalidad_nombre}}';
$defaultBody = '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;"><table width="100%" cellpadding="0" cellspacing="0" style="padding:22px 12px;"><tr><td align="center"><table width="680" cellpadding="0" cellspacing="0" style="max-width:680px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;"><tr><td style="padding:20px 24px;background:#1d4ed8;color:#ffffff;"><div style="font-size:20px;font-weight:700;">Cotización de servicios</div><div style="font-size:13px;opacity:.95;">{{bajada_informativa}}</div></td></tr><tr><td style="padding:18px 24px;color:#111827;font-size:14px;line-height:1.6;"><p style="margin:0 0 10px 0;">Hola <strong>{{cliente_nombre}}</strong>, te compartimos el detalle de tu cotización <strong>{{codigo_cotizacion}}</strong>.</p><table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Datos cliente</div><div><strong>Contacto:</strong> {{cliente_contacto}}</div><div><strong>Correo:</strong> {{cliente_correo}}</div><div><strong>Dirección:</strong> {{cliente_direccion}}</div></td></tr></table><table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Detalle de la cotización</div>{{detalle_servicios}}<div style="margin-top:8px;"><strong>Total:</strong> {{total_cotizacion}}</div><div><strong>Válida por:</strong> {{validez_dias}} días (hasta {{fecha_validez}})</div></td></tr></table><table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Condiciones</div><div>{{nota_cotizacion}}</div></td></tr></table><p style="margin:12px 0 0 0;color:#4b5563;">Si tienes dudas, responde este correo y te ayudamos.</p></td></tr></table></td></tr></table></body></html>';

$template = ['subject' => $defaultSubject, 'body_html' => $defaultBody];

try {
    $stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
    $stmt->execute([$templateKey]);
    $tpl = $stmt->fetch();
    if (is_array($tpl)) {
        $template = [
            'subject' => (string) ($tpl['subject'] ?? $defaultSubject),
            'body_html' => (string) ($tpl['body_html'] ?? $defaultBody),
        ];
    }
} catch (Exception $e) {
} catch (Error $e) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? 'save';

    if ($action === 'restore') {
        $subject = $defaultSubject;
        $bodyHtml = $defaultBody;
    } else {
        $subject = trim($_POST['subject'] ?? '');
        $bodyHtml = trim($_POST['body_html'] ?? '');

        if ($subject === '' || $bodyHtml === '') {
            $errors[] = 'Completa el asunto y el cuerpo del correo.';
        }
    }

    if (empty($errors)) {
        try {
            $stmtUpsert = db()->prepare(
                'INSERT INTO email_templates (template_key, subject, body_html)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE subject = VALUES(subject), body_html = VALUES(body_html)'
            );
            $stmtUpsert->execute([$templateKey, $subject, $bodyHtml]);
            $success = true;
            $template = ['subject' => $subject, 'body_html' => $bodyHtml];
        } catch (Exception $e) {
            $errors[] = 'No se pudo guardar la plantilla. Verifica la conexión a base de datos.';
        } catch (Error $e) {
            $errors[] = 'No se pudo guardar la plantilla. Verifica la conexión a base de datos.';
        }
    }
}

$municipalidad = get_municipalidad();
$previewData = [
    '{{municipalidad_nombre}}' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
    '{{cliente_nombre}}' => 'Comercial Demo SpA',
    '{{codigo_cotizacion}}' => 'COT-AB12CD34',
    '{{detalle_servicios}}' => '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;border-collapse:collapse;margin:12px 0;"><thead><tr><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Servicio</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Periodicidad</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Desc. %</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Total</th></tr></thead><tbody><tr><td style="padding:6px;border-bottom:1px solid #e5e7eb;">Hosting</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">Mensual</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">10%</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">$45.000,00</td></tr></tbody></table>',
    '{{bajada_informativa}}' => 'Propuesta comercial informativa con condiciones y vigencia.',
    '{{nota_cotizacion}}' => 'Valores vigentes por 5 días. Incluye soporte básico y está sujeta a confirmación comercial.',
    '{{total_cotizacion}}' => '$45.000,00',
    '{{validez_dias}}' => '5',
    '{{fecha_validez}}' => date('d/m/Y', strtotime('+5 days')),
    '{{cliente_contacto}}' => '+56 9 1234 5678',
    '{{cliente_correo}}' => 'contacto@comercialdemo.cl',
    '{{cliente_direccion}}' => 'Av. Principal 123, Santiago',
];
$subjectPreview = strtr($template['subject'], $previewData);
$bodyPreview = strtr($template['body_html'], $previewData);
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = "Plantilla correo cotización"; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = "Mantenedores"; $title = "Plantilla correo cotización"; include('partials/page-title.php'); ?>

            <?php if ($success) : ?><div class="alert alert-success">Plantilla guardada correctamente.</div><?php endif; ?>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?><div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card"><div class="card-header"><h5 class="mb-0">Plantilla correo de cotización</h5></div>
                        <div class="card-body">
                            <form id="template-form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="mb-3"><label class="form-label">Asunto</label><input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($template['subject'] ?? $defaultSubject, ENT_QUOTES, 'UTF-8'); ?>"></div>
                                <div class="mb-3"><label class="form-label">Cuerpo (HTML)</label><textarea name="body_html" class="form-control" rows="16"><?php echo htmlspecialchars($template['body_html'] ?? $defaultBody, ENT_QUOTES, 'UTF-8'); ?></textarea></div>
                            </form>
                            <form id="restore-template-form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="action" value="restore">
                            </form>
                            <div class="d-flex gap-2">
                                <button type="submit" form="restore-template-form" class="btn btn-outline-secondary">Restaurar plantilla</button>
                                <button type="submit" form="template-form" class="btn btn-primary">Guardar plantilla</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card"><div class="card-header"><h5 class="mb-0">Vista previa</h5></div>
                        <div class="card-body">
                            <div class="mb-2"><strong>Asunto:</strong> <?php echo htmlspecialchars($subjectPreview, ENT_QUOTES, 'UTF-8'); ?></div>
                            <iframe title="Vista previa" style="width:100%;height:560px;border:1px solid #dee2e6;border-radius:8px;background:#fff;" srcdoc="<?php echo htmlspecialchars($bodyPreview, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
                        </div>
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
