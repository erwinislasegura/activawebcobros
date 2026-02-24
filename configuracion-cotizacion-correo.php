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

$defaultSubject = 'Cotización {{codigo_cotizacion}} - {{municipalidad_nombre}}';
$defaultBody = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f8fafc;padding:16px;"><table width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;"><tr><td style="padding:18px 22px;background:#1D4ED8;color:#fff;"><strong>{{municipalidad_nombre}}</strong></td></tr><tr><td style="padding:18px 22px;color:#111827;"><p>Estimado/a <strong>{{cliente_nombre}}</strong>,</p><p>Te compartimos los servicios asociados en la cotización <strong>{{codigo_cotizacion}}</strong>.</p>{{detalle_servicios}}<p><strong>Total:</strong> {{total_cotizacion}}</p><p>{{nota_cotizacion}}</p></td></tr></table></body></html>';

$stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
$stmt->execute([$templateKey]);
$template = $stmt->fetch() ?: ['subject' => $defaultSubject, 'body_html' => $defaultBody];

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
        $stmtUpsert = db()->prepare(
            'INSERT INTO email_templates (template_key, subject, body_html)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE subject = VALUES(subject), body_html = VALUES(body_html)'
        );
        $stmtUpsert->execute([$templateKey, $subject, $bodyHtml]);
        $success = true;
        $template = ['subject' => $subject, 'body_html' => $bodyHtml];
    }
}

$municipalidad = get_municipalidad();
$previewData = [
    '{{municipalidad_nombre}}' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
    '{{cliente_nombre}}' => 'Comercial Demo SpA',
    '{{codigo_cotizacion}}' => 'COT-AB12CD34',
    '{{detalle_servicios}}' => '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;border-collapse:collapse;margin:12px 0;"><thead><tr><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Servicio</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Periodicidad</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Desc. %</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Total</th></tr></thead><tbody><tr><td style="padding:6px;border-bottom:1px solid #e5e7eb;">Hosting</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">Mensual</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">10,00%</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">$45.000,00</td></tr></tbody></table>',
    '{{nota_cotizacion}}' => 'Valores vigentes por 15 días.',
    '{{total_cotizacion}}' => '$45.000,00',
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
