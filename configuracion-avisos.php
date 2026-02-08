<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = false;
$templateKey = $_GET['template'] ?? 'aviso_1';

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
              <p style="margin:0 0 12px 0;">Estimado/a <strong>{{cliente_nombre}}</strong>,</p>
              <p style="margin:0 0 16px 0;color:#374151;">
                Junto con saludar, le informamos que su <strong>servicio digital contratado</strong> se encuentra próximo a su fecha de vencimiento.
              </p>
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
                Es importante considerar que la <strong>no renovación oportuna</strong> de este servicio puede afectar directamente la <strong>disponibilidad de su sitio web, correos corporativos y presencia en internet</strong>, lo que a su vez impacta la <strong>imagen, posicionamiento y continuidad operativa de su empresa frente a clientes y proveedores</strong>.
              </p>
              <p style="margin:0 0 12px 0;color:#4B5563;">
                Para evitar interrupciones y asegurar la correcta continuidad de sus servicios digitales, le recomendamos realizar el pago dentro del plazo indicado.
              </p>
              <p style="margin:0 0 12px 0;color:#4B5563;">
                Ante cualquier consulta, aclaración o si requiere apoyo con el proceso de renovación, puede responder directamente a este correo y con gusto lo asistiremos.
              </p>
              <p style="margin:0;color:#4B5563;">
                Agradecemos su atención y preferencia.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:16px 24px 22px 24px;background:#ffffff;border-top:1px solid #eef2f7;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="font-size:12px;color:#6B7280;line-height:1.5;">
                    Atentamente,<br><strong>Departamento de Soporte y Servicios Digitales</strong><br>{{municipalidad_nombre}}
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

$templateOptions = [
    'aviso_1' => [
        'label' => 'Aviso 1 (Azul)',
        'subject' => 'Aviso 1: {{servicio_nombre}} - {{cliente_nombre}}',
        'body_html' => build_aviso_template('#1D4ED8', '#93C5FD', 'Aviso 1', 'Aviso de vencimiento del servicio.'),
    ],
    'aviso_2' => [
        'label' => 'Aviso 2 (Amarillo)',
        'subject' => 'Aviso 2: {{servicio_nombre}} - {{cliente_nombre}}',
        'body_html' => build_aviso_template('#F59E0B', '#FCD34D', 'Aviso 2', 'Segundo aviso del servicio.'),
    ],
    'aviso_3' => [
        'label' => 'Aviso 3 (Rojo)',
        'subject' => 'Aviso 3: {{servicio_nombre}} - {{cliente_nombre}}',
        'body_html' => build_aviso_template('#DC2626', '#FCA5A5', 'Aviso 3', 'Último aviso antes de tomar acciones adicionales.'),
    ],
];

if (!isset($templateOptions[$templateKey])) {
    $templateKey = 'aviso_1';
}

$defaultSubject = $templateOptions[$templateKey]['subject'];
$defaultBody = $templateOptions[$templateKey]['body_html'];

$stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
$stmt->execute([$templateKey]);
$template = $stmt->fetch() ?: ['subject' => $defaultSubject, 'body_html' => $defaultBody];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? 'save';
    $templateKey = $_POST['template_key'] ?? $templateKey;

    if ($action === 'restore') {
        $subject = $templateOptions[$templateKey]['subject'] ?? $defaultSubject;
        $bodyHtml = $templateOptions[$templateKey]['body_html'] ?? $defaultBody;
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
    '{{cliente_nombre}}' => 'Cliente de prueba',
    '{{servicio_nombre}}' => 'Servicio municipal',
    '{{monto}}' => '$120.000',
    '{{fecha_aviso}}' => date('d/m/Y'),
    '{{referencia}}' => 'ABC1234',
];
$subjectPreview = strtr($template['subject'], $previewData);
$bodyPreview = strtr($template['body_html'], $previewData);
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Plantillas de avisos"; include('partials/title-meta.php'); ?>

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
            min-height: 260px;
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

                <?php $subtitle = "Configuración"; $title = "Plantillas de avisos"; include('partials/page-title.php'); ?>

                <?php if ($success) : ?>
                    <div class="alert alert-success">Plantilla guardada correctamente.</div>
                <?php endif; ?>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) : ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title mb-0">Plantillas de avisos</h5>
                            <p class="text-muted mb-0">Configura el diseño y texto de los avisos 1, 2 y 3.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <form method="get" class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0" for="template-key">Aviso</label>
                                <select id="template-key" name="template" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <?php foreach ($templateOptions as $key => $info) : ?>
                                        <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $key === $templateKey ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="template-form" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="template_key" value="<?php echo htmlspecialchars($templateKey, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="mb-3">
                                <label class="form-label" for="subject">Asunto</label>
                                <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($template['subject'] ?? $defaultSubject, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="body-html">Cuerpo (HTML)</label>
                                <textarea id="body-html" name="body_html" class="form-control code-editor" rows="14"><?php echo htmlspecialchars($template['body_html'] ?? $defaultBody, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </form>
                        <form id="restore-template-form" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="template_key" value="<?php echo htmlspecialchars($templateKey, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="restore">
                        </form>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" form="restore-template-form" class="btn btn-outline-secondary">Restaurar plantilla</button>
                            <button type="submit" form="template-form" class="btn btn-primary">Guardar plantilla</button>
                        </div>
                        <hr>
                        <h6 class="mb-3">Vista previa</h6>
                        <div class="border rounded bg-white p-3">
                            <div class="mb-2 text-muted small">Asunto: <?php echo htmlspecialchars($subjectPreview, ENT_QUOTES, 'UTF-8'); ?></div>
                            <div><?php echo $bodyPreview; ?></div>
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
