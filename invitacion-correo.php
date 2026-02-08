<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$notice = '';
$templateKey = 'invitacion_autoridades';

$defaultSubject = 'Invitación al evento: {{evento_titulo}}';
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
                  Junto con saludar, la Municipalidad de {{municipalidad_nombre}} le extiende una cordial invitación
                  para participar en el evento institucional <strong>{{evento_titulo}}</strong>.
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
                <p style="margin:0 0 12px;font-size:13px;color:#475569;">
                  Agradecemos confirmar su disponibilidad con su equipo de coordinación. Si tiene observaciones o requiere apoyo logístico,
                  puede responder directamente a este correo.
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
    $stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
    $stmt->execute([$templateKey]);
    $correoTemplate = $stmt->fetch() ?: [];
} catch (Exception $e) {
    $correoTemplate = [];
} catch (Error $e) {
    $correoTemplate = [];
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
            $errors[] = 'Completa el asunto y el HTML del correo de invitación.';
        }
    }

    if (empty($errors)) {
        $stmt = db()->prepare('SELECT id FROM email_templates WHERE template_key = ? LIMIT 1');
        $stmt->execute([$templateKey]);
        $templateId = $stmt->fetchColumn();

        if ($templateId) {
            $stmtUpdate = db()->prepare('UPDATE email_templates SET subject = ?, body_html = ? WHERE id = ?');
            $stmtUpdate->execute([$subject, $bodyHtml, $templateId]);
        } else {
            $stmtInsert = db()->prepare('INSERT INTO email_templates (template_key, subject, body_html) VALUES (?, ?, ?)');
            $stmtInsert->execute([$templateKey, $subject, $bodyHtml]);
        }

        $notice = $action === 'restore'
            ? 'Plantilla restaurada correctamente.'
            : 'Plantilla guardada correctamente.';
    }

    $correoTemplate = [
        'subject' => $subject,
        'body_html' => $bodyHtml,
    ];
}

$municipalidad = get_municipalidad();
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$logoUrl = preg_match('/^https?:\/\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');
$previewData = [
    'municipalidad_nombre' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
    'municipalidad_logo' => htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'),
    'destinatario_nombre' => 'Ana Martínez',
    'destinatario_cargo' => 'Directora de Gabinete',
    'evento_titulo' => 'Cuenta pública municipal 2024',
    'evento_descripcion' => 'Ceremonia oficial de rendición de cuentas y presentación de hitos comunales.',
    'evento_fecha_inicio' => '2024-04-10 10:00',
    'evento_fecha_fin' => '2024-04-10 12:00',
    'evento_ubicacion' => 'Teatro Municipal',
    'evento_tipo' => 'Ceremonia institucional',
    'evento_publico_objetivo' => 'Equipo directivo y ciudadanía invitada',
    'evento_cupos' => '150',
    'evento_encargado_nombre' => 'María López',
    'evento_encargado_correo' => 'maria.lopez@municipalidad.cl',
    'evento_encargado_telefono' => '+56 9 1234 5678',
    'confirmacion_link' => base_url() . '/confirmar-asistencia.php?token=ABC123',
];
$subjectPreview = render_invitation_template($correoTemplate['subject'] ?? $defaultSubject, $previewData);
$bodyPreview = render_invitation_template($correoTemplate['body_html'] ?? $defaultBody, $previewData);
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Invitación correo"; include('partials/title-meta.php'); ?>

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
            min-height: 360px;
        }

        .code-editor:focus {
            background-color: #1e1e1e;
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
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

                <?php $subtitle = "Mantenedores"; $title = "Invitación correo"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Correo de invitación institucional</h5>
                                    <p class="text-muted mb-0">Configura el HTML y el asunto del correo que se enviará a cada autoridad.</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" form="correo-invitacion-restore-form" class="btn btn-outline-secondary">Restaurar plantilla</button>
                                    <button type="submit" form="correo-invitacion-form" class="btn btn-primary">Guardar plantilla</button>
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

                                <?php if ($notice !== '') : ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>

                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <form id="correo-invitacion-form" method="post">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="mb-3">
                                                <label class="form-label" for="subject">Asunto del correo</label>
                                                <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($correoTemplate['subject'] ?? $defaultSubject, ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="form-text">Variables: {{municipalidad_nombre}}, {{destinatario_nombre}}, {{evento_titulo}}</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="body-html">HTML del correo</label>
                                                <textarea id="body-html" name="body_html" class="form-control code-editor" rows="18"><?php echo htmlspecialchars($correoTemplate['body_html'] ?? $defaultBody, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                <div class="form-text">Variables disponibles: {{municipalidad_nombre}}, {{municipalidad_logo}}, {{destinatario_nombre}}, {{destinatario_cargo}}, {{evento_titulo}}, {{evento_descripcion}}, {{evento_fecha_inicio}}, {{evento_fecha_fin}}, {{evento_ubicacion}}, {{evento_tipo}}, {{evento_publico_objetivo}}, {{evento_cupos}}, {{evento_encargado_nombre}}, {{evento_encargado_correo}}, {{evento_encargado_telefono}}, {{confirmacion_link}}</div>
                                            </div>
                                        </form>
                                        <form id="correo-invitacion-restore-form" method="post">
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

</body>

</html>
