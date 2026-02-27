<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = false;
$templateKey = 'eliminacion_servicio_suspendido';

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

$renderTemplate = static function (string $template, array $data): string {
    return strtr($template, $data);
};

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
            $errors[] = 'Completa asunto y HTML del correo.';
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
    '{{municipalidad_nombre}}' => htmlspecialchars((string) ($municipalidad['nombre'] ?? 'Institución'), ENT_QUOTES, 'UTF-8'),
    '{{cliente_nombre}}' => 'Comercial Ejemplo SpA',
    '{{servicio_nombre}}' => 'Hosting Corporativo + Correo',
    '{{motivo_suspension}}' => 'Servicio suspendido por deuda vencida sin regularización.',
    '{{detalle_suspension}}' => 'No hubo respuesta a los avisos previos de cobro y suspensión.',
    '{{fecha_notificacion}}' => date('d/m/Y H:i'),
];
$subjectPreview = $renderTemplate($template['subject'] ?? $defaultSubject, $previewData);
$bodyPreview = $renderTemplate($template['body_html'] ?? $defaultBody, $previewData);
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = "Correo eliminación de servicio"; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
    <style>.code-editor{font-family:Consolas,monospace;background:#1e1e1e;color:#d4d4d4;border-radius:10px;min-height:320px;}</style>
</head>
<body>
<div class="wrapper">
<?php include('partials/menu.php'); ?>
<div class="content-page"><div class="container-fluid">
<?php $subtitle = "Mantenedores"; $title = "Correo eliminación de servicio"; include('partials/page-title.php'); ?>

<?php if ($success) : ?><div class="alert alert-success">Plantilla guardada correctamente.</div><?php endif; ?>
<?php if (!empty($errors)) : ?><div class="alert alert-danger"><?php foreach ($errors as $error) : ?><div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?></div><?php endif; ?>

<div class="row">
  <div class="col-lg-7">
    <div class="card"><div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Configuración HTML</h5>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="submit" form="template-form" name="action" value="restore">Restaurar</button>
        <button class="btn btn-primary" type="submit" form="template-form" name="action" value="save">Guardar</button>
      </div>
    </div>
    <div class="card-body">
      <form id="template-form" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
        <div class="mb-3"><label class="form-label">Asunto</label><input class="form-control" name="subject" value="<?php echo htmlspecialchars((string) ($template['subject'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
        <div class="mb-3"><label class="form-label">HTML</label><textarea class="form-control code-editor" rows="18" name="body_html"><?php echo htmlspecialchars((string) ($template['body_html'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea></div>
      </form>
      <div class="alert alert-warning mb-0"><strong>Variables:</strong> {{municipalidad_nombre}}, {{cliente_nombre}}, {{servicio_nombre}}, {{motivo_suspension}}, {{detalle_suspension}}, {{fecha_notificacion}}</div>
    </div></div>
  </div>
  <div class="col-lg-5">
    <div class="card"><div class="card-header"><h5 class="mb-0">Vista previa</h5></div><div class="card-body">
      <div class="mb-2"><strong>Asunto:</strong> <?php echo htmlspecialchars($subjectPreview, ENT_QUOTES, 'UTF-8'); ?></div>
      <iframe title="Vista previa" style="width:100%;height:520px;border:1px solid #dee2e6;border-radius:8px;background:#fff;" srcdoc="<?php echo htmlspecialchars($bodyPreview, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
    </div></div>
  </div>
</div>

</div><?php include('partials/footer.php'); ?></div></div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
</body></html>
