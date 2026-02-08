<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';
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
    $templates = [];
    foreach (array_keys($templateKeys) as $key) {
        $stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $templates[$key] = $stmt->fetch() ?: [];
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

function build_aviso_mailto(?string $correo, string $subject, string $bodyHtml): string
{
    if ($correo === null || $correo === '') {
        return '#';
    }
    $subjectEncoded = rawurlencode($subject);
    $plainBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $bodyHtml)));
    $bodyEncoded = rawurlencode($plainBody);
    return 'mailto:' . rawurlencode($correo) . '?subject=' . $subjectEncoded . '&body=' . $bodyEncoded;
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
                                        <th>Primer aviso</th>
                                        <th>Segundo aviso</th>
                                        <th>Tercer aviso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($cobros)) : ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay avisos registrados.</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($cobros as $cobro) : ?>
                                            <?php
                                            $correo = $cobro['cliente_correo'] ?? '';
                                            $cliente = (string) $cobro['cliente'];
                                            $servicio = (string) $cobro['servicio'];
                                            $referencia = (string) ($cobro['referencia'] ?? '');
                                            $primer = $cobro['fecha_primer_aviso'] ? date('d/m/Y', strtotime($cobro['fecha_primer_aviso'])) : '-';
                                            $segundo = $cobro['fecha_segundo_aviso'] ? date('d/m/Y', strtotime($cobro['fecha_segundo_aviso'])) : '-';
                                            $tercero = $cobro['fecha_tercer_aviso'] ? date('d/m/Y', strtotime($cobro['fecha_tercer_aviso'])) : '-';
                                            $templateData = [
                                                '{{municipalidad_nombre}}' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
                                                '{{municipalidad_logo}}' => htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'),
                                                '{{cliente_nombre}}' => htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'),
                                                '{{servicio_nombre}}' => htmlspecialchars($servicio, ENT_QUOTES, 'UTF-8'),
                                                '{{monto}}' => htmlspecialchars('$' . number_format((float) $cobro['monto'], 2, ',', '.'), ENT_QUOTES, 'UTF-8'),
                                                '{{referencia}}' => htmlspecialchars($referencia !== '' ? $referencia : '-', ENT_QUOTES, 'UTF-8'),
                                                '{{fecha_aviso}}' => '{{fecha_aviso}}',
                                            ];
                                            $templatesData = [];
                                            foreach (array_keys($templateKeys) as $key) {
                                                $subjectTemplate = $templates[$key]['subject'] ?? $defaultTemplates[$key]['subject'];
                                                $bodyTemplate = $templates[$key]['body_html'] ?? $defaultTemplates[$key]['body_html'];
                                                $templatesData[$key] = [
                                                    'subject' => render_template($subjectTemplate, $templateData),
                                                    'body_html' => render_template($bodyTemplate, $templateData),
                                                ];
                                            }
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
                                                <td><?php echo htmlspecialchars($primer, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($segundo, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($tercero, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="text-nowrap">
                                                    <?php
                                                    $primerSubject = str_replace('{{fecha_aviso}}', $primer, $templatesData['aviso_1']['subject']);
                                                    $primerBody = str_replace('{{fecha_aviso}}', $primer, $templatesData['aviso_1']['body_html']);
                                                    $segundoSubject = str_replace('{{fecha_aviso}}', $segundo, $templatesData['aviso_2']['subject']);
                                                    $segundoBody = str_replace('{{fecha_aviso}}', $segundo, $templatesData['aviso_2']['body_html']);
                                                    $terceroSubject = str_replace('{{fecha_aviso}}', $tercero, $templatesData['aviso_3']['subject']);
                                                    $terceroBody = str_replace('{{fecha_aviso}}', $tercero, $templatesData['aviso_3']['body_html']);
                                                    $primerUrl = $cobro['fecha_primer_aviso'] ? build_aviso_mailto($correo, $primerSubject, $primerBody) : '#';
                                                    $segundoUrl = $cobro['fecha_segundo_aviso'] ? build_aviso_mailto($correo, $segundoSubject, $segundoBody) : '#';
                                                    $terceroUrl = $cobro['fecha_tercer_aviso'] ? build_aviso_mailto($correo, $terceroSubject, $terceroBody) : '#';
                                                    $disabled = ($correo === '' || $correo === null);
                                                    ?>
                                                    <a class="btn btn-sm btn-outline-primary <?php echo $disabled ? 'disabled' : ''; ?>" href="<?php echo htmlspecialchars($primerUrl, ENT_QUOTES, 'UTF-8'); ?>">Aviso 1</a>
                                                    <a class="btn btn-sm btn-outline-warning <?php echo $disabled ? 'disabled' : ''; ?>" href="<?php echo htmlspecialchars($segundoUrl, ENT_QUOTES, 'UTF-8'); ?>">Aviso 2</a>
                                                    <a class="btn btn-sm btn-outline-danger <?php echo $disabled ? 'disabled' : ''; ?>" href="<?php echo htmlspecialchars($terceroUrl, ENT_QUOTES, 'UTF-8'); ?>">Aviso 3</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Los botones de envío se habilitan si el cliente tiene correo registrado.</small>
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
