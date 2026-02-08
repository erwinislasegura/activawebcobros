<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = false;
$templateKey = 'validacion_autoridades';

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

$defaultSubject = 'Validación de autoridades: {{evento_titulo}}';
$defaultBody = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Validación de autoridades</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6fb;font-family:Arial,Helvetica,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    Confirmación de autoridades para {{evento_titulo}}.
  </div>

  <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f4f6fb" style="margin:0;padding:0;">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background-color:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e6ebf2;">
          <tr>
            <td style="padding:0;background:#007DC6;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="padding:16px 20px;">
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="vertical-align:middle;">
                          <img src="{{municipalidad_logo}}" alt="Logo municipalidad" height="30" style="display:block;border:0;outline:none;text-decoration:none;">
                        </td>
                        <td style="vertical-align:middle;padding-left:10px;color:#ffffff;font-weight:700;font-size:15px;letter-spacing:0.2px;">
                          {{municipalidad_nombre}}
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td align="right" style="padding:16px 20px;color:#dbeeff;font-size:12px;white-space:nowrap;">
                    Validación de autoridades
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="height:4px;background:#FCB017;line-height:4px;font-size:0;">&nbsp;</td>
          </tr>
          <tr>
            <td style="padding:26px 24px 10px 24px;color:#1f2a37;font-size:14px;line-height:1.65;">
              <p style="margin:0 0 12px 0;">
                Hola <strong style="color:#111827;">{{destinatario_nombre}}</strong>,
              </p>
              <p style="margin:0 0 14px 0;color:#374151;">
                Te invitamos a confirmar las autoridades asistentes al evento
                <strong style="color:#111827;">{{evento_titulo}}</strong>.
              </p>
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0 18px 0;background:#f8fafc;border:1px solid #e6ebf2;border-radius:12px;">
                <tr>
                  <td style="padding:14px 14px 6px 14px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="font-size:12px;color:#6A7880;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;">
                          Detalles del evento
                        </td>
                        <td align="right" style="font-size:12px;color:#6A7880;">
                          <span style="display:inline-block;width:8px;height:8px;border-radius:999px;background:#8EC53F;vertical-align:middle;margin-right:6px;"></span>
                          Información oficial
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 14px 14px 14px;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;line-height:1.6;color:#374151;">
                      <tr>
                        <td style="padding-top:10px;width:110px;color:#6A7880;"><strong>Fecha</strong></td>
                        <td style="padding-top:10px;">{{evento_fecha_inicio}} - {{evento_fecha_fin}}</td>
                      </tr>
                      <tr>
                        <td style="padding-top:8px;width:110px;color:#6A7880;"><strong>Lugar</strong></td>
                        <td style="padding-top:8px;">{{evento_ubicacion}}</td>
                      </tr>
                      <tr>
                        <td style="padding-top:8px;width:110px;color:#6A7880;"><strong>Tipo</strong></td>
                        <td style="padding-top:8px;">{{evento_tipo}}</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 8px 0;">
                <strong style="color:#111827;">Descripción</strong>
              </p>
              <div style="margin:0 0 16px 0;color:#374151;">
                {{evento_descripcion}}
              </div>
              <p style="margin:0 0 8px 0;">
                <strong style="color:#111827;">Autoridades preseleccionadas</strong>
              </p>
              <ul style="margin:0 0 18px 0;padding-left:18px;color:#374151;">
                {{autoridades_lista}}
              </ul>
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0 10px 0;">
                <tr>
                  <td align="center" style="padding:8px 0 6px 0;">
                    <a href="{{validation_link}}"
                       style="background:#FCB017;color:#1f2a37;text-decoration:none;
                              padding:12px 22px;border-radius:12px;font-weight:700;
                              display:inline-block;border:1px solid #CD7B16;">
                      Confirmar asistencia
                    </a>
                  </td>
                </tr>
                <tr>
                  <td align="center" style="font-size:12px;color:#6A7880;padding-top:8px;">
                    Este enlace es personal. Evita reenviarlo a terceros.
                  </td>
                </tr>
              </table>
              <div style="margin:18px 0 0 0;padding:14px;border:1px dashed #cdd6e3;border-radius:12px;background:#ffffff;">
                <p style="margin:0 0 6px 0;font-size:12px;color:#6A7880;">
                  Si el botón no funciona, copia y pega este enlace en tu navegador:
                </p>
                <p style="margin:0;word-break:break-all;font-size:13px;">
                  <a href="{{validation_link}}" style="color:#007DC6;text-decoration:underline;">
                    {{validation_link}}
                  </a>
                </p>
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:16px 24px 22px 24px;background:#ffffff;border-top:1px solid #eef2f7;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="font-size:12px;color:#6A7880;line-height:1.5;">
                    Este correo fue enviado por <strong>{{municipalidad_nombre}}</strong>.
                    Si no reconoces esta solicitud, puedes ignorar este mensaje.
                  </td>
                  <td align="right" style="font-size:12px;color:#6A7880;white-space:nowrap;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:999px;background:#007DC6;vertical-align:middle;margin-right:6px;"></span>
                    Notificación institucional
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

$renderEmailTemplate = static function (string $template, array $data): string {
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
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$logoUrl = preg_match('/^https?:\/\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');
$previewData = [
    '{{municipalidad_nombre}}' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
    '{{municipalidad_logo}}' => htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'),
    '{{destinatario_nombre}}' => 'Ana Martínez',
    '{{evento_titulo}}' => 'Cuenta pública municipal 2024',
    '{{evento_descripcion}}' => 'Ceremonia oficial de rendición de cuentas y presentación de hitos comunales.',
    '{{evento_fecha_inicio}}' => '2024-04-10 10:00',
    '{{evento_fecha_fin}}' => '2024-04-10 12:00',
    '{{evento_ubicacion}}' => 'Teatro Municipal',
    '{{evento_tipo}}' => 'Ceremonia institucional',
    '{{autoridades_lista}}' => '<li>Alcaldesa</li><li>Director de Finanzas</li><li>Directora de Gabinete</li>',
    '{{validation_link}}' => 'https://municipalidad.cl/validacion/ABC123',
];
$subjectPreview = $renderEmailTemplate($template['subject'] ?? $defaultSubject, $previewData);
$bodyPreview = $renderEmailTemplate($template['body_html'] ?? $defaultBody, $previewData);
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Correo validación externa"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
    <style>
        .code-editor {
            font-family: "Fira Code", "Consolas", "Courier New", monospace;
            background-color: #1e1e1e;
            color: #d4d4d4;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 14px 16px;
            line-height: 1.55;
            min-height: 320px;
        }

        .code-editor:focus {
            background-color: #1e1e1e;
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">

                <?php $subtitle = "Mantenedores"; $title = "Correo validación externa"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Correo de validación externa</h5>
                                    <p class="text-muted mb-0">Configura el correo HTML que se enviará con el enlace de validación externa.</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" form="restore-template-form" class="btn btn-outline-secondary">Restaurar plantilla</button>
                                    <button type="submit" form="template-form" class="btn btn-primary">Guardar configuración</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($success) : ?>
                                    <div class="alert alert-success">Configuración guardada correctamente.</div>
                                <?php endif; ?>

                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <form id="template-form" method="post">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="action" value="save">
                                            <div class="mb-3">
                                                <label class="form-label" for="email-subject">Asunto del correo</label>
                                                <input type="text" id="email-subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($template['subject'], ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="email-body">Cuerpo HTML</label>
                                                <textarea id="email-body" name="body_html" class="form-control code-editor" rows="16"><?php echo htmlspecialchars($template['body_html'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                <div class="form-text">Puedes usar HTML completo con estilos en línea.</div>
                                            </div>
                                        </form>
                                        <form id="restore-template-form" method="post">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="action" value="restore">
                                        </form>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6 class="text-muted text-uppercase fs-12">Vista previa</h6>
                                        <div class="border rounded-3 p-3 bg-light">
                                            <div class="mb-2"><strong>Asunto:</strong> <?php echo htmlspecialchars($subjectPreview, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="bg-white rounded-3 p-3" style="max-height:480px;overflow:auto;">
                                                <?php echo $bodyPreview; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-transparent">
                                            <h5 class="card-title mb-0">Variables disponibles</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <ul class="list-group">
                                                        <li class="list-group-item"><strong>{{municipalidad_nombre}}</strong> · Nombre de la municipalidad</li>
                                                        <li class="list-group-item"><strong>{{municipalidad_logo}}</strong> · URL del logo municipal</li>
                                                        <li class="list-group-item"><strong>{{destinatario_nombre}}</strong> · Nombre del destinatario</li>
                                                        <li class="list-group-item"><strong>{{evento_titulo}}</strong> · Título del evento</li>
                                                        <li class="list-group-item"><strong>{{evento_descripcion}}</strong> · Descripción del evento</li>
                                                        <li class="list-group-item"><strong>{{evento_fecha_inicio}}</strong> · Fecha de inicio</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-group">
                                                        <li class="list-group-item"><strong>{{evento_fecha_fin}}</strong> · Fecha de término</li>
                                                        <li class="list-group-item"><strong>{{evento_ubicacion}}</strong> · Ubicación del evento</li>
                                                        <li class="list-group-item"><strong>{{evento_tipo}}</strong> · Tipo de evento</li>
                                                        <li class="list-group-item"><strong>{{autoridades_lista}}</strong> · Lista HTML &lt;li&gt; de autoridades</li>
                                                        <li class="list-group-item"><strong>{{validation_link}}</strong> · Enlace público de validación</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
