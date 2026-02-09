<?php

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
              <table cellpadding="0" cellspacing="0" style="margin:18px 0;">
                <tr>
                  <td>
                    <a href="{{link_boton_pago}}" style="background:{$primary};color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:999px;display:inline-block;font-size:13px;font-weight:600;">
                      Pagar ahora
                    </a>
                  </td>
                </tr>
              </table>
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

function get_aviso_template_options(): array
{
    return [
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
}
