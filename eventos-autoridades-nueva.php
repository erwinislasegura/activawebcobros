<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$validationErrors = [];
$validationNotice = null;
$validationLink = null;
$whatsappLinks = [];
$emailPreview = null;
$events = db()->query('SELECT id, titulo, fecha_inicio, fecha_fin FROM events WHERE habilitado = 1 ORDER BY fecha_inicio DESC')->fetchAll();
$assignedEvents = db()->query(
    'SELECT e.id,
            e.titulo,
            COUNT(ea.authority_id) AS authority_count,
            (SELECT COUNT(*)
             FROM event_authority_requests r
             WHERE r.event_id = e.id AND r.correo_enviado = 1) AS sent_count,
            (SELECT COUNT(*)
             FROM event_authority_requests r
             WHERE r.event_id = e.id AND r.estado = "respondido") AS validated_count
     FROM events e
     INNER JOIN event_authorities ea ON ea.event_id = e.id
     WHERE e.habilitado = 1
     GROUP BY e.id
     ORDER BY e.fecha_inicio DESC'
)->fetchAll();
$authorities = db()->query(
    'SELECT a.id,
            a.nombre,
            a.tipo,
            g.id AS grupo_id,
            g.nombre AS grupo_nombre
     FROM authorities a
     LEFT JOIN authority_groups g ON g.id = a.group_id
     WHERE a.estado = 1
     ORDER BY COALESCE(g.nombre, ""), a.nombre'
)->fetchAll();
$users = db()->query('SELECT id, nombre, apellido, correo, telefono FROM users WHERE estado = 1 ORDER BY nombre, apellido')->fetchAll();
$selectedEventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;
$linkedAuthorities = [];
$emailTemplate = null;
$eventValidationLink = null;
$selectedEvent = null;
$authoritiesByGroup = [];
$displayAuthoritiesByGroup = [];
$saveNotice = null;
$editRequestId = isset($_GET['edit_request_id']) ? (int) $_GET['edit_request_id'] : 0;
$editRequest = null;
$assignedEventIds = array_map(static function ($event) {
    return (int) $event['id'];
}, $assignedEvents);
$availableEvents = array_filter($events, static function ($event) use ($assignedEventIds) {
    return !in_array((int) $event['id'], $assignedEventIds, true);
});
$selectedAuthoritiesCount = 0;

foreach ($authorities as $authority) {
    $groupId = $authority['grupo_id'] ? (int) $authority['grupo_id'] : 0;
    $groupName = $authority['grupo_nombre'] ?: 'Sin grupo';
    if (!isset($authoritiesByGroup[$groupId])) {
        $authoritiesByGroup[$groupId] = [
            'name' => $groupName,
            'items' => [],
        ];
    }
    $authoritiesByGroup[$groupId]['items'][] = $authority;
}

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

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS notificacion_whatsapp (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            phone_number_id VARCHAR(80) NOT NULL,
            access_token TEXT NOT NULL,
            numero_envio VARCHAR(30) DEFAULT NULL,
            country_code VARCHAR(6) DEFAULT NULL,
            template_name VARCHAR(120) DEFAULT NULL,
            template_language VARCHAR(10) DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

if ($selectedEventId > 0) {
    $stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$selectedEventId]);
    $selectedEvent = $stmt->fetch();
    if ($selectedEvent) {
        $selectedEvent['validation_token'] = ensure_event_validation_token($selectedEventId, $selectedEvent['validation_token'] ?? null);
        $eventValidationLink = base_url() . '/eventos-validacion.php?token=' . urlencode($selectedEvent['validation_token']);
    }

    $stmt = db()->prepare('SELECT authority_id FROM event_authorities WHERE event_id = ?');
    $stmt->execute([$selectedEventId]);
    $linkedAuthorities = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    $selectedAuthoritiesCount = count($linkedAuthorities);

}

foreach ($authoritiesByGroup as $groupId => $group) {
    if (!empty($group['items'])) {
        $displayAuthoritiesByGroup[$groupId] = [
            'name' => $group['name'],
            'items' => $group['items'],
        ];
    }
}

if ($selectedEventId > 0 && $editRequestId > 0) {
    $stmt = db()->prepare('SELECT * FROM event_authority_requests WHERE id = ? AND event_id = ?');
    $stmt->execute([$editRequestId, $selectedEventId]);
    $editRequest = $stmt->fetch() ?: null;
}

try {
    $stmt = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
    $stmt->execute(['validacion_autoridades']);
    $emailTemplate = $stmt->fetch() ?: null;
} catch (Exception $e) {
} catch (Error $e) {
}

function build_event_validation_email(array $municipalidad, array $event, array $authorities, string $validationUrl, ?string $recipientName): string
{
    $primaryColor = $municipalidad['color_primary'] ?? '#1565c0';
    $secondaryColor = $municipalidad['color_secondary'] ?? '#0d47a1';
    $logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
    $logoUrl = $logoPath;
    $safeRecipient = htmlspecialchars($recipientName ?: 'Equipo municipal', ENT_QUOTES, 'UTF-8');
    $safeTitle = htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8');
    $safeLocation = htmlspecialchars($event['ubicacion'], ENT_QUOTES, 'UTF-8');
    $safeType = htmlspecialchars($event['tipo'], ENT_QUOTES, 'UTF-8');
    $safeStart = htmlspecialchars($event['fecha_inicio'], ENT_QUOTES, 'UTF-8');
    $safeEnd = htmlspecialchars($event['fecha_fin'], ENT_QUOTES, 'UTF-8');
    $safeDescription = nl2br(htmlspecialchars($event['descripcion'], ENT_QUOTES, 'UTF-8'));
    $safeUrl = htmlspecialchars($validationUrl, ENT_QUOTES, 'UTF-8');

    $authorityItems = '';
    $groupedAuthorities = [];
    foreach ($authorities as $authority) {
        $groupId = $authority['grupo_id'] ? (int) $authority['grupo_id'] : 0;
        $groupName = $authority['grupo_nombre'] ?: 'Sin grupo';
        if (!isset($groupedAuthorities[$groupId])) {
            $groupedAuthorities[$groupId] = [
                'name' => $groupName,
                'items' => [],
            ];
        }
        $groupedAuthorities[$groupId]['items'][] = $authority;
    }
    foreach ($groupedAuthorities as $group) {
        if (empty($group['items'])) {
            continue;
        }
        $authorityItems .= '<li style="margin-top:8px;"><strong>' . htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') . '</strong><ul style="margin:6px 0 0 16px;">';
        foreach ($group['items'] as $authority) {
            $authorityItems .= '<li>' . htmlspecialchars($authority['nombre'], ENT_QUOTES, 'UTF-8') . ' · ' . htmlspecialchars($authority['tipo'], ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $authorityItems .= '</ul></li>';
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Validación de autoridades</title>
  </head>
  <body style="margin:0;padding:0;background-color:#f4f6fb;font-family:Arial,sans-serif;color:#1f2b3a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6fb;padding:32px 0;">
      <tr>
        <td align="center">
          <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,0.08);">
            <tr>
              <td style="background:linear-gradient(120deg, {$primaryColor}, {$secondaryColor});padding:24px 32px;color:#ffffff;">
                <img src="{$logoUrl}" alt="Logo" style="height:28px;vertical-align:middle;">
                <span style="font-size:18px;font-weight:bold;margin-left:12px;vertical-align:middle;">{$municipalidad['nombre']}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:32px;">
                <p style="margin:0 0 12px;font-size:16px;">Hola {$safeRecipient},</p>
                <p style="margin:0 0 20px;font-size:15px;line-height:1.5;">
                  Se requiere tu validación para confirmar qué autoridades asistirán al evento <strong>{$safeTitle}</strong>.
                </p>
                <div style="background-color:#f8fafc;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
                  <p style="margin:0 0 6px;font-size:13px;color:#64748b;">Detalles del evento</p>
                  <p style="margin:0;font-size:15px;font-weight:bold;color:#0f172a;">{$safeTitle}</p>
                  <p style="margin:6px 0 0;font-size:13px;color:#475569;">{$safeLocation} · {$safeType}</p>
                  <p style="margin:6px 0 0;font-size:13px;color:#475569;">{$safeStart} - {$safeEnd}</p>
                  <p style="margin:10px 0 0;font-size:13px;color:#475569;">{$safeDescription}</p>
                </div>
                <p style="margin:0 0 8px;font-size:13px;color:#64748b;">Autoridades preseleccionadas</p>
                <ul style="margin:0 0 20px;padding-left:20px;font-size:13px;color:#0f172a;line-height:1.4;">
                  {$authorityItems}
                </ul>
                <div style="text-align:center;margin:24px 0;">
                  <a href="{$safeUrl}" style="background-color:{$primaryColor};color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:999px;font-weight:bold;display:inline-block;">Validar autoridades</a>
                </div>
                <p style="margin:0;font-size:12px;color:#94a3b8;">
                  Si no puedes abrir el botón, copia y pega este enlace en tu navegador:<br>
                  <a href="{$safeUrl}" style="color:{$secondaryColor};word-break:break-all;">{$safeUrl}</a>
                </p>
              </td>
            </tr>
            <tr>
              <td style="background-color:#f8fafc;padding:16px 32px;text-align:center;font-size:12px;color:#94a3b8;">
                Correo automático del sistema municipal · {$municipalidad['nombre']}
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

function render_event_email_template(array $template, array $data): string
{
    $replacements = [
        '{{municipalidad_nombre}}' => $data['municipalidad_nombre'] ?? '',
        '{{municipalidad_logo}}' => $data['municipalidad_logo'] ?? '',
        '{{destinatario_nombre}}' => $data['destinatario_nombre'] ?? '',
        '{{evento_titulo}}' => $data['evento_titulo'] ?? '',
        '{{evento_descripcion}}' => $data['evento_descripcion'] ?? '',
        '{{evento_fecha_inicio}}' => $data['evento_fecha_inicio'] ?? '',
        '{{evento_fecha_fin}}' => $data['evento_fecha_fin'] ?? '',
        '{{evento_ubicacion}}' => $data['evento_ubicacion'] ?? '',
        '{{evento_tipo}}' => $data['evento_tipo'] ?? '',
        '{{autoridades_lista}}' => $data['autoridades_lista'] ?? '',
        '{{validation_link}}' => $data['validation_link'] ?? '',
    ];

    return strtr($template['body_html'] ?? '', $replacements);
}

function render_event_email_subject(string $subject, array $data): string
{
    $replacements = [
        '{{municipalidad_nombre}}' => $data['municipalidad_nombre'] ?? '',
        '{{destinatario_nombre}}' => $data['destinatario_nombre'] ?? '',
        '{{evento_titulo}}' => $data['evento_titulo'] ?? '',
        '{{evento_fecha_inicio}}' => $data['evento_fecha_inicio'] ?? '',
        '{{evento_fecha_fin}}' => $data['evento_fecha_fin'] ?? '',
        '{{evento_ubicacion}}' => $data['evento_ubicacion'] ?? '',
        '{{evento_tipo}}' => $data['evento_tipo'] ?? '',
    ];

    return strtr($subject, $replacements);
}

function normalize_whatsapp_phone(?string $phone, ?string $countryCode): ?string
{
    if ($phone === null) {
        return null;
    }
    $digits = preg_replace('/\D+/', '', $phone);
    if ($digits === '') {
        return null;
    }
    $countryCode = $countryCode ? preg_replace('/\D+/', '', $countryCode) : '';
    if ($countryCode !== '' && strpos($digits, $countryCode) !== 0) {
        $digits = ltrim($digits, '0');
        $digits = $countryCode . $digits;
    }
    return $digits;
}

function send_whatsapp_message(array $config, string $to, string $message, ?string &$error = null): bool
{
    $phoneNumberId = $config['phone_number_id'] ?? '';
    $accessToken = $config['access_token'] ?? '';
    if ($phoneNumberId === '' || $accessToken === '') {
        $error = 'Configuración de WhatsApp incompleta.';
        return false;
    }

    $url = 'https://graph.facebook.com/v17.0/' . $phoneNumberId . '/messages';
    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'text',
        'text' => [
            'preview_url' => true,
            'body' => $message,
        ],
    ];

    if (!empty($config['template_name']) && !empty($config['template_language'])) {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $config['template_name'],
                'language' => [
                    'code' => $config['template_language'],
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $message,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        $error = $curlError !== '' ? $curlError : 'Respuesta inválida de WhatsApp.';
        return false;
    }

    return true;
}

function build_whatsapp_link(string $phone, string $message): string
{
    return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? 'save_authorities';

    if ($action === 'save_authorities') {
        $eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
        $authorityIds = array_map('intval', $_POST['authorities'] ?? []);

        if ($eventId === 0) {
            $errors[] = 'Selecciona un evento válido.';
        }

        $didSubmitSave = isset($_POST['save_authorities']);
        if (empty($errors) && $didSubmitSave) {
            $stmtDelete = db()->prepare('DELETE FROM event_authorities WHERE event_id = ?');
            $stmtDelete->execute([$eventId]);

            if (!empty($authorityIds)) {
                $stmtInsert = db()->prepare('INSERT INTO event_authorities (event_id, authority_id) VALUES (?, ?)');
                foreach ($authorityIds as $authorityId) {
                    $stmtInsert->execute([$eventId, $authorityId]);
                }
            }

            redirect('eventos-autoridades.php?event_id=' . $eventId . '&saved=1');
        }
    }

    if ($action === 'update_request') {
        $requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        $eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
        $nombre = trim($_POST['destinatario_nombre'] ?? '');
        $correo = trim($_POST['destinatario_correo'] ?? '');
        $estado = $_POST['estado'] ?? 'pendiente';
        $correoEnviado = isset($_POST['correo_enviado']) && (int) $_POST['correo_enviado'] === 1 ? 1 : 0;

        if ($eventId === 0 || $requestId === 0) {
            $validationErrors[] = 'Selecciona una solicitud válida para editar.';
        }
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $validationErrors[] = 'El correo ingresado no es válido.';
        }
        if (!in_array($estado, ['pendiente', 'respondido'], true)) {
            $validationErrors[] = 'El estado seleccionado no es válido.';
        }

        if (empty($validationErrors)) {
            $respondedAt = null;
            if ($estado === 'respondido') {
                $respondedAt = date('Y-m-d H:i:s');
            }
            $stmt = db()->prepare(
                'UPDATE event_authority_requests
                 SET destinatario_nombre = ?, destinatario_correo = ?, estado = ?, correo_enviado = ?, responded_at = ?
                 WHERE id = ? AND event_id = ?'
            );
            $stmt->execute([
                $nombre !== '' ? $nombre : null,
                $correo !== '' ? $correo : null,
                $estado,
                $correoEnviado,
                $respondedAt,
                $requestId,
                $eventId,
            ]);
            redirect('eventos-autoridades.php?event_id=' . $eventId . '&updated=1');
        }
    }

    if ($action === 'send_validation') {
        $eventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;
        $recipientUserIds = array_map('intval', $_POST['recipient_user_ids'] ?? []);
        $deliveryChannel = $_POST['delivery_channel'] ?? 'email';

        if ($eventId === 0) {
            $validationErrors[] = 'Selecciona un evento válido.';
        }

        $recipients = [];
        $whatsappRecipients = [];
        if (!empty($recipientUserIds)) {
            $placeholders = implode(',', array_fill(0, count($recipientUserIds), '?'));
            $stmt = db()->prepare("SELECT nombre, apellido, correo, telefono FROM users WHERE id IN ($placeholders)");
            $stmt->execute($recipientUserIds);
            foreach ($stmt->fetchAll() as $user) {
                if (!empty($user['correo'])) {
                    $recipients[] = [
                        'nombre' => trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')),
                        'correo' => $user['correo'],
                    ];
                }
                if (!empty($user['telefono'])) {
                    $whatsappRecipients[] = [
                        'nombre' => trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')),
                        'telefono' => $user['telefono'],
                    ];
                }
            }
        }

        $needsEmail = in_array($deliveryChannel, ['email', 'both'], true);
        $needsWhatsapp = in_array($deliveryChannel, ['whatsapp', 'both'], true);
        $needsWhatsappLink = $deliveryChannel === 'whatsapp_link';

        if ($needsEmail && empty($recipients)) {
            $validationErrors[] = 'Selecciona al menos un usuario con correo válido para enviar la validación.';
        }
        if (($needsWhatsapp || $needsWhatsappLink) && empty($whatsappRecipients)) {
            $validationErrors[] = 'Selecciona al menos un usuario con teléfono para enviar WhatsApp.';
        }

        $event = null;
        $eventAuthorities = [];
        if (empty($validationErrors) && $eventId > 0) {
            $stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
            $stmt->execute([$eventId]);
            $event = $stmt->fetch();

            $stmt = db()->prepare(
                'SELECT a.id,
                        a.nombre,
                        a.tipo,
                        g.id AS grupo_id,
                        g.nombre AS grupo_nombre
                 FROM authorities a
                 INNER JOIN event_authorities ea ON ea.authority_id = a.id
                 LEFT JOIN authority_groups g ON g.id = a.group_id
                 WHERE ea.event_id = ?
                 ORDER BY COALESCE(g.nombre, ""), a.nombre'
            );
            $stmt->execute([$eventId]);
            $eventAuthorities = $stmt->fetchAll();

            if (!$event) {
                $validationErrors[] = 'No se encontró el evento seleccionado.';
            } elseif (empty($eventAuthorities)) {
                $validationErrors[] = 'El evento aún no tiene autoridades preseleccionadas.';
            }
        }

        if (empty($validationErrors) && $event) {
            $event['validation_token'] = ensure_event_validation_token($eventId, $event['validation_token'] ?? null);
            $validationUrl = base_url() . '/eventos-validacion.php?token=' . urlencode($event['validation_token']);

            $municipalidad = get_municipalidad();
            $logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
            $logoUrl = preg_match('/^https?:\\/\\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');
            $municipalidad['logo_path'] = $logoUrl;
            $subject = 'Validación de autoridades: ' . $event['titulo'];
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";

            $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch();
            $fromEmail = $correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? null;
            $fromName = $correoConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? 'Municipalidad');
            if ($fromEmail) {
                $headers .= 'From: ' . ($fromName ? $fromName . ' <' . $fromEmail . '>' : $fromEmail) . "\r\n";
            }

            $stmtRequest = db()->prepare('SELECT id FROM event_authority_requests WHERE event_id = ? AND token = ? LIMIT 1');
            $stmtRequest->execute([$eventId, $event['validation_token']]);
            $requestId = $stmtRequest->fetchColumn();
            if (!$requestId) {
                $stmtInsert = db()->prepare('INSERT INTO event_authority_requests (event_id, destinatario_nombre, destinatario_correo, token, correo_enviado) VALUES (?, ?, ?, ?, ?)');
                $placeholderEmail = $municipalidad['correo'] ?? 'validacion@municipalidad.local';
                $stmtInsert->execute([
                    $eventId,
                    'Enlace público',
                    $placeholderEmail,
                    $event['validation_token'],
                    0,
                ]);
            }

            $allSent = true;
            $anySent = false;
            $whatsappSent = true;
            $whatsappAny = false;

            $whatsappConfig = null;
            if ($needsWhatsapp || $needsWhatsappLink) {
                $whatsappConfig = db()->query('SELECT * FROM notificacion_whatsapp LIMIT 1')->fetch();
                if ($needsWhatsapp && (!$whatsappConfig || empty($whatsappConfig['phone_number_id']) || empty($whatsappConfig['access_token']))) {
                    $validationErrors[] = 'Configura WhatsApp Business API antes de enviar mensajes.';
                }
            }

            if (empty($validationErrors) && $needsEmail) {
                foreach ($recipients as $recipient) {
                    $emailPreview = build_event_validation_email($municipalidad, $event, $eventAuthorities, $validationUrl, $recipient['nombre'] ?? null);
                    if ($emailTemplate) {
                        $autoridadesLista = '';
                        $groupedAuthorities = [];
                        foreach ($eventAuthorities as $authority) {
                            $groupId = $authority['grupo_id'] ? (int) $authority['grupo_id'] : 0;
                            $groupName = $authority['grupo_nombre'] ?: 'Sin grupo';
                            if (!isset($groupedAuthorities[$groupId])) {
                                $groupedAuthorities[$groupId] = [
                                    'name' => $groupName,
                                    'items' => [],
                                ];
                            }
                            $groupedAuthorities[$groupId]['items'][] = $authority;
                        }
                        foreach ($groupedAuthorities as $group) {
                            if (empty($group['items'])) {
                                continue;
                            }
                            $autoridadesLista .= '<p style="margin:16px 0 8px;"><strong>' . htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') . '</strong></p><ul style="margin-top:0;">';
                            foreach ($group['items'] as $authority) {
                                $autoridadesLista .= '<li>' . htmlspecialchars($authority['nombre'], ENT_QUOTES, 'UTF-8') . ' · ' . htmlspecialchars($authority['tipo'], ENT_QUOTES, 'UTF-8') . '</li>';
                            }
                            $autoridadesLista .= '</ul>';
                        }
                        $logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
                        $logoUrl = preg_match('/^https?:\\/\\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');
                        $templateData = [
                            'municipalidad_nombre' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
                            'municipalidad_logo' => htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'),
                            'destinatario_nombre' => htmlspecialchars($recipient['nombre'] ?? 'Equipo municipal', ENT_QUOTES, 'UTF-8'),
                            'evento_titulo' => htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'),
                            'evento_descripcion' => nl2br(htmlspecialchars($event['descripcion'], ENT_QUOTES, 'UTF-8')),
                            'evento_fecha_inicio' => htmlspecialchars($event['fecha_inicio'], ENT_QUOTES, 'UTF-8'),
                            'evento_fecha_fin' => htmlspecialchars($event['fecha_fin'], ENT_QUOTES, 'UTF-8'),
                            'evento_ubicacion' => htmlspecialchars($event['ubicacion'], ENT_QUOTES, 'UTF-8'),
                            'evento_tipo' => htmlspecialchars($event['tipo'], ENT_QUOTES, 'UTF-8'),
                            'autoridades_lista' => $autoridadesLista,
                            'validation_link' => htmlspecialchars($validationUrl, ENT_QUOTES, 'UTF-8'),
                        ];
                        $subjectData = [
                            'municipalidad_nombre' => $municipalidad['nombre'] ?? 'Municipalidad',
                            'destinatario_nombre' => $recipient['nombre'] ?? 'Equipo municipal',
                            'evento_titulo' => $event['titulo'] ?? '',
                            'evento_fecha_inicio' => $event['fecha_inicio'] ?? '',
                            'evento_fecha_fin' => $event['fecha_fin'] ?? '',
                            'evento_ubicacion' => $event['ubicacion'] ?? '',
                            'evento_tipo' => $event['tipo'] ?? '',
                        ];
                        $emailPreview = render_event_email_template($emailTemplate, $templateData);
                        if (!empty($emailTemplate['subject'])) {
                            $subject = render_event_email_subject($emailTemplate['subject'], $subjectData);
                        }
                    }
                    $mailSent = mail($recipient['correo'], $subject, $emailPreview, $headers);
                    $anySent = $anySent || $mailSent;
                    $allSent = $allSent && $mailSent;
                }
            }

            if (empty($validationErrors) && $needsWhatsapp && $whatsappConfig) {
                foreach ($whatsappRecipients as $recipient) {
                    $normalizedPhone = normalize_whatsapp_phone($recipient['telefono'] ?? null, $whatsappConfig['country_code'] ?? null);
                    if ($normalizedPhone === null) {
                        $whatsappSent = false;
                        continue;
                    }
                    $message = 'Hola ' . ($recipient['nombre'] ?: 'equipo municipal') . '. '
                        . 'Por favor valida las autoridades del evento "' . ($event['titulo'] ?? '') . '". '
                        . 'Link: ' . $validationUrl;
                    $sendError = null;
                    $sent = send_whatsapp_message($whatsappConfig, $normalizedPhone, $message, $sendError);
                    $whatsappAny = $whatsappAny || $sent;
                    $whatsappSent = $whatsappSent && $sent;
                }
            }

            if (empty($validationErrors) && $needsWhatsappLink) {
                foreach ($whatsappRecipients as $recipient) {
                    $normalizedPhone = normalize_whatsapp_phone(
                        $recipient['telefono'] ?? null,
                        $whatsappConfig['country_code'] ?? null
                    );
                    if ($normalizedPhone === null) {
                        continue;
                    }
                    $message = 'Hola ' . ($recipient['nombre'] ?: 'equipo municipal') . '. '
                        . 'Por favor valida las autoridades del evento "' . ($event['titulo'] ?? '') . '". '
                        . 'Link: ' . $validationUrl;
                    $whatsappLinks[] = [
                        'nombre' => $recipient['nombre'] ?: 'Equipo municipal',
                        'telefono' => $normalizedPhone,
                        'link' => build_whatsapp_link($normalizedPhone, $message),
                    ];
                }
            }

            $stmtUpdate = db()->prepare('UPDATE event_authority_requests SET correo_enviado = ? WHERE event_id = ? AND token = ?');
            $stmtUpdate->execute([$anySent ? 1 : 0, $eventId, $event['validation_token']]);

            $validationLink = $validationUrl;
            if ($needsWhatsappLink && !empty($whatsappLinks)) {
                $validationNotice = 'Links de WhatsApp generados correctamente.';
            } elseif ($needsEmail && $allSent && !$needsWhatsapp) {
                $validationNotice = 'Correos de validación enviados correctamente.';
            } elseif ($needsWhatsapp && $whatsappSent && !$needsEmail) {
                $validationNotice = 'Mensajes de WhatsApp enviados correctamente.';
            } elseif ($needsEmail && $needsWhatsapp && $allSent && $whatsappSent) {
                $validationNotice = 'Correos y WhatsApp enviados correctamente.';
            } else {
                $validationErrors[] = 'Algunos envíos no se pudieron completar automáticamente. Comparte el enlace de validación manualmente si es necesario.';
            }
        }
    }
}

if (isset($_GET['saved']) && $_GET['saved'] === '1') {
    $saveNotice = 'Autoridades actualizadas correctamente.';
}
if (isset($_GET['updated']) && $_GET['updated'] === '1') {
    $validationNotice = 'La solicitud fue actualizada correctamente.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Autoridades por evento · Nueva vista"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Eventos Municipales"; $title = "Autoridades por evento · Nueva vista"; include('partials/page-title.php'); ?>

                <div class="go-muni-authorities">
                    <div class="gm-page-head card border-0 shadow-sm mb-3 gm-section">
                        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <h4 class="mb-1">Autoridades por evento</h4>
                                <p class="text-muted mb-0">Gestiona la asignación de autoridades y coordina validaciones externas.</p>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="gm-dirty-indicator text-muted small" id="dirty-indicator">Sin cambios pendientes</span>
                                <button type="submit" form="evento-autoridades-form" name="save_authorities" value="1" class="btn btn-primary" id="save-authorities-btn" disabled>Guardar cambios</button>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($errors)) : ?>
                        <div class="alert alert-danger gm-alert">
                            <?php foreach ($errors as $error) : ?>
                                <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <form id="evento-autoridades-form" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="action" value="save_authorities">
                                <div class="card border shadow-none mb-4 gm-card-compact gm-section">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                            <div>
                                                <div class="gm-step-label">Paso 1 · Seleccionar evento</div>
                                                <p class="text-muted mb-0">Elige un evento para asignar autoridades.</p>
                                            </div>
                                            <div class="form-check form-switch gm-switch">
                                                <input class="form-check-input" type="checkbox" id="toggle-assigned-events" <?php echo empty($assignedEvents) ? 'disabled' : ''; ?>>
                                                <label class="form-check-label" for="toggle-assigned-events">Mostrar también eventos con autoridades</label>
                                                <?php if (empty($assignedEvents)) : ?>
                                                    <span class="gm-switch-help" data-bs-toggle="tooltip" title="Requiere endpoint para mostrar eventos con autoridades.">Requiere endpoint</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <label class="form-label" for="evento-select">Evento</label>
                                        <?php if ($selectedEventId > 0 && in_array($selectedEventId, $assignedEventIds, true)) : ?>
                                            <input type="hidden" name="event_id" value="<?php echo (int) $selectedEventId; ?>">
                                        <?php else : ?>
                                            <select id="evento-select" name="event_id" class="form-select" aria-describedby="evento-help">
                                                <option value="">Selecciona un evento</option>
                                                <?php foreach ($availableEvents as $event) : ?>
                                                    <option value="<?php echo (int) $event['id']; ?>" data-start="<?php echo htmlspecialchars($event['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-end="<?php echo htmlspecialchars($event['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedEventId === (int) $event['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text" id="evento-help">Solo se muestran eventos sin autoridades asignadas.</div>
                                        <?php endif; ?>
                                        <div class="gm-event-status mt-2">
                                            <?php
                                            $eventAssigned = $selectedEventId > 0 && in_array($selectedEventId, $assignedEventIds, true);
                                            $eventBadgeClass = $eventAssigned ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary';
                                            $eventBadgeText = $eventAssigned ? 'Con autoridades asignadas' : 'Sin autoridades asignadas';
                                            ?>
                                            <span class="badge <?php echo $eventBadgeClass; ?>" id="event-status-badge"><?php echo $eventBadgeText; ?></span>
                                            <div class="gm-event-summary mt-2" id="event-summary">
                                                <?php if ($selectedEventId > 0 && $selectedEvent) : ?>
                                                    <div class="gm-event-title"><?php echo htmlspecialchars($selectedEvent['titulo'] ?? 'Evento seleccionado', ENT_QUOTES, 'UTF-8'); ?></div>
                                                    <div class="gm-event-meta text-muted small">
                                                        <?php if (!empty($selectedEvent['fecha_inicio'])) : ?>
                                                            <?php echo htmlspecialchars($selectedEvent['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?>
                                                            <?php if (!empty($selectedEvent['fecha_fin'])) : ?>
                                                                · <?php echo htmlspecialchars($selectedEvent['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?>
                                                            <?php endif; ?>
                                                        <?php else : ?>
                                                            Fecha pendiente
                                                        <?php endif; ?>
                                                        · Autoridades marcadas: <?php echo $selectedAuthoritiesCount; ?>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="gm-event-title">Selecciona un evento para ver el resumen.</div>
                                                    <div class="gm-event-meta text-muted small">Fecha pendiente</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="gm-empty-state mt-2 <?php echo $selectedEventId > 0 ? 'd-none' : ''; ?>" id="event-empty-state">
                                            <i class="ti ti-calendar-event"></i>
                                            <div>
                                                <div class="fw-semibold">Aún no hay evento seleccionado</div>
                                                <div class="text-muted small">Selecciona un evento para comenzar la asignación.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border shadow-none gm-card-compact gm-section">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                            <div>
                                                <div class="gm-step-label">Paso 2 · Seleccionar autoridades</div>
                                                <p class="text-muted mb-0">Filtra por cargo o grupo para asignar rápidamente.</p>
                                            </div>
                                            <div class="gm-count-badge">
                                                Seleccionadas: <span id="selected-count"><?php echo $selectedAuthoritiesCount; ?></span>
                                            </div>
                                        </div>
                                        <div class="row g-3 align-items-end">
                                            <div class="col-lg-6">
                                                <label class="form-label" for="authority-search">Buscar autoridad</label>
                                                <input type="search" class="form-control" id="authority-search" placeholder="Buscar por nombre o cargo">
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label">Filtros rápidos</label>
                                                <div class="gm-chip-group" id="authority-filters"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                            <button type="button" class="btn btn-outline-primary" id="select-all-authorities">Seleccionar todas (filtradas)</button>
                                            <button type="button" class="btn btn-outline-secondary" id="clear-all-authorities">Limpiar selección (filtradas)</button>
                                            <span class="text-muted small" id="filtered-count"></span>
                                        </div>
                                        <div class="gm-authorities-list" id="authority-list"></div>
                                        <div class="gm-empty-state d-none" id="authority-empty-state">
                                            <i class="ti ti-users-off"></i>
                                            <div>
                                                <div class="fw-semibold">No hay resultados</div>
                                                <div class="text-muted small">Ajusta los filtros o la búsqueda.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-12">
                            <div class="card border shadow-none mb-4 gm-card-compact gm-section">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <div class="gm-step-label">Paso 3 · Enviar validación (link)</div>
                                            <p class="text-muted mb-0">Envía enlace de validación a los responsables.</p>
                                        </div>
                                    </div>

                                    <?php if (!empty($validationErrors)) : ?>
                                        <div class="alert alert-danger gm-alert">
                                            <?php foreach ($validationErrors as $error) : ?>
                                                <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($validationNotice) : ?>
                                        <div class="alert alert-success gm-alert">
                                            <?php echo htmlspecialchars($validationNotice, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($editRequest) : ?>
                                        <div class="card border gm-inner-card mb-4 gm-section">
                                            <div class="card-body">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                                    <h6 class="mb-0">Editar solicitud reciente</h6>
                                                    <a class="btn btn-sm btn-outline-secondary" href="eventos-autoridades.php?event_id=<?php echo (int) $selectedEventId; ?>">Cancelar</a>
                                                </div>
                                                <form method="post" class="row g-3">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="action" value="update_request">
                                                    <input type="hidden" name="event_id" value="<?php echo (int) $selectedEventId; ?>">
                                                    <input type="hidden" name="request_id" value="<?php echo (int) $editRequest['id']; ?>">
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="edit-destinatario">Destinatario</label>
                                                        <input id="edit-destinatario" type="text" name="destinatario_nombre" class="form-control" value="<?php echo htmlspecialchars($editRequest['destinatario_nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="edit-correo">Correo</label>
                                                        <input id="edit-correo" type="email" name="destinatario_correo" class="form-control" value="<?php echo htmlspecialchars($editRequest['destinatario_correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="edit-estado">Estado</label>
                                                        <select id="edit-estado" name="estado" class="form-select">
                                                            <option value="pendiente" <?php echo ($editRequest['estado'] ?? '') === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                            <option value="respondido" <?php echo ($editRequest['estado'] ?? '') === 'respondido' ? 'selected' : ''; ?>>Respondido</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="edit-correo-enviado">Correo enviado</label>
                                                        <select id="edit-correo-enviado" name="correo_enviado" class="form-select">
                                                            <option value="0" <?php echo (int) ($editRequest['correo_enviado'] ?? 0) === 0 ? 'selected' : ''; ?>>Pendiente</option>
                                                            <option value="1" <?php echo (int) ($editRequest['correo_enviado'] ?? 0) === 1 ? 'selected' : ''; ?>>Enviado</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <form id="evento-validacion-form" method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="action" value="send_validation">
                                        <input type="hidden" name="event_id" value="<?php echo (int) $selectedEventId; ?>">

                                        <div class="gm-validation-step mb-3">
                                            <div class="gm-step-title">1. Evento seleccionado</div>
                                            <div class="gm-event-pill <?php echo $selectedEventId === 0 ? 'gm-warning' : ''; ?>" id="validation-event-pill">
                                                <?php if ($selectedEventId === 0) : ?>
                                                    <i class="ti ti-alert-circle"></i>
                                                    Selecciona un evento para habilitar el envío.
                                                <?php else : ?>
                                                    <?php
                                                    $selectedEventTitle = '';
                                                    foreach ($events as $event) {
                                                        if ((int) $event['id'] === $selectedEventId) {
                                                            $selectedEventTitle = $event['titulo'];
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                    <i class="ti ti-check"></i>
                                                    <?php echo htmlspecialchars($selectedEventTitle, ENT_QUOTES, 'UTF-8'); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="gm-validation-step mb-3">
                                            <label class="gm-step-title" for="recipient-users">2. Destinatarios</label>
                                            <select id="recipient-users" name="recipient_user_ids[]" class="form-select" multiple size="6" aria-describedby="recipient-help">
                                                <?php foreach ($users as $user) : ?>
                                                    <option value="<?php echo (int) $user['id']; ?>">
                                                        <?php echo htmlspecialchars(trim($user['nombre'] . ' ' . $user['apellido']), ENT_QUOTES, 'UTF-8'); ?>
                                                        (<?php echo htmlspecialchars($user['correo'], ENT_QUOTES, 'UTF-8'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text" id="recipient-help">Selecciona uno o más usuarios para enviar el enlace público del evento.</div>
                                            <div class="gm-selected-users mt-2" id="selected-recipients"></div>
                                        </div>

                                        <div class="gm-validation-step mb-3">
                                            <div class="gm-step-title mb-2">3. Canal de envío</div>
                                            <div class="gm-segmented-control" role="radiogroup" aria-label="Canal de envío">
                                                <input type="radio" class="btn-check" name="delivery_channel" id="channel-email" value="email" checked>
                                                <label class="btn btn-outline-primary" for="channel-email">Correo</label>

                                                <input type="radio" class="btn-check" name="delivery_channel" id="channel-whatsapp" value="whatsapp">
                                                <label class="btn btn-outline-primary" for="channel-whatsapp">WhatsApp</label>

                                                <input type="radio" class="btn-check" name="delivery_channel" id="channel-both" value="both">
                                                <label class="btn btn-outline-primary" for="channel-both">Ambos</label>

                                                <input type="radio" class="btn-check" name="delivery_channel" id="channel-whatsapp-link" value="whatsapp_link">
                                                <label class="btn btn-outline-primary" for="channel-whatsapp-link">WhatsApp link directo</label>
                                            </div>
                                            <div class="form-text">WhatsApp requiere teléfono en usuarios y configuración previa.</div>
                                        </div>

                                        <div class="gm-validation-step">
                                            <button type="submit" class="btn btn-primary w-100" id="send-validation-btn" disabled>
                                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                                <span class="gm-send-label">Enviar enlace</span>
                                            </button>
                                            <div class="gm-inline-note mt-2" id="send-validation-note">Selecciona un evento y destinatarios para continuar.</div>
                                        </div>
                                    </form>

                                    <?php if ($validationLink || $eventValidationLink) : ?>
                                        <div class="mt-4">
                                            <label class="form-label">Enlace de validación</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($validationLink ?: $eventValidationLink, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($whatsappLinks)) : ?>
                                        <div class="mt-4">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                                <label class="form-label mb-0">Links de WhatsApp</label>
                                                <span class="badge text-bg-success">Link directo</span>
                                            </div>
                                            <div class="list-group">
                                                <?php foreach ($whatsappLinks as $linkData) : ?>
                                                    <a class="list-group-item list-group-item-action d-flex align-items-center gap-3" href="<?php echo htmlspecialchars($linkData['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                                        <span class="badge rounded-pill text-bg-success"><i class="ti ti-brand-whatsapp"></i></span>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold"><?php echo htmlspecialchars($linkData['nombre'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($linkData['telefono'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                        </div>
                                                        <span class="btn btn-sm btn-success">Abrir WhatsApp</span>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="form-text mt-2">Los enlaces abren WhatsApp Web o la app instalada con el mensaje prellenado.</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card border shadow-none mb-4 gm-accordion-card gm-card-compact gm-section">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="mb-0">Eventos con autoridades</h6>
                                        <span class="badge bg-light text-muted" id="assigned-events-count"><?php echo count($assignedEvents); ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <input type="search" class="form-control" id="assigned-events-search" placeholder="Buscar evento">
                                    </div>
                                    <?php if (!empty($assignedEvents)) : ?>
                                        <div class="list-group list-group-flush gm-scroll" id="assigned-events-list">
                                            <?php foreach ($assignedEvents as $assignedEvent) : ?>
                                                <div class="list-group-item d-flex align-items-center justify-content-between px-0" data-title="<?php echo htmlspecialchars($assignedEvent['titulo'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <div class="text-truncate">
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($assignedEvent['titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                            <span class="badge bg-secondary-subtle text-secondary"><?php echo (int) $assignedEvent['authority_count']; ?> autoridades</span>
                                                            <?php if ((int) $assignedEvent['sent_count'] > 0) : ?>
                                                                <span class="badge bg-info-subtle text-info">Validación enviada</span>
                                                            <?php else : ?>
                                                                <span class="badge bg-light text-muted">Sin validación enviada</span>
                                                            <?php endif; ?>
                                                            <?php if ((int) $assignedEvent['validated_count'] > 0) : ?>
                                                                <span class="badge bg-success-subtle text-success">Autoridades validadas</span>
                                                            <?php else : ?>
                                                                <span class="badge bg-warning-subtle text-warning">Validación pendiente</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <a class="btn btn-sm btn-outline-primary" href="eventos-autoridades-nueva.php?event_id=<?php echo (int) $assignedEvent['id']; ?>">Editar</a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="gm-empty-state">
                                            <i class="ti ti-calendar-off"></i>
                                            <div>
                                                <div class="fw-semibold">Sin eventos asignados</div>
                                                <div class="text-muted small">Crea la primera asignación desde la columna principal.</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="gm-mobile-save d-lg-none">
                        <button type="submit" form="evento-autoridades-form" name="save_authorities" value="1" class="btn btn-primary w-100" id="save-authorities-btn-mobile" disabled>Guardar cambios</button>
                    </div>

                    <?php if ($saveNotice) : ?>
                        <div class="toast-container position-fixed bottom-0 end-0 p-3">
                            <div class="toast align-items-center text-bg-success border-0" id="save-toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        <?php echo htmlspecialchars($saveNotice, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                $authoritiesPayload = [];
                foreach ($displayAuthoritiesByGroup as $groupId => $group) {
                    foreach ($group['items'] as $authority) {
                        $authorityId = (int) $authority['id'];
                        $authoritiesPayload[] = [
                            'id' => $authorityId,
                            'name' => $authority['nombre'],
                            'role' => $authority['tipo'],
                            'groupId' => (int) $groupId,
                            'groupName' => $group['name'],
                            'checked' => in_array($authorityId, $linkedAuthorities, true),
                        ];
                    }
                }
                $eventsPayload = [];
                foreach ($events as $event) {
                    $eventId = (int) $event['id'];
                    $eventsPayload[] = [
                        'id' => $eventId,
                        'title' => $event['titulo'],
                        'start' => $event['fecha_inicio'] ?? null,
                        'end' => $event['fecha_fin'] ?? null,
                        'hasAuthorities' => in_array($eventId, $assignedEventIds, true),
                    ];
                }
                ?>
                <script type="application/json" id="authorities-data"><?php echo json_encode($authoritiesPayload, JSON_UNESCAPED_UNICODE); ?></script>
                <script type="application/json" id="events-data"><?php echo json_encode($eventsPayload, JSON_UNESCAPED_UNICODE); ?></script>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <div class="modal fade" id="event-change-modal" tabindex="-1" aria-labelledby="event-change-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="event-change-title">Cambiar evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    Perderás los cambios no guardados en las autoridades actuales. ¿Deseas continuar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-event-change">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include('partials/customizer.php'); ?>

    <style>
        .go-muni-authorities {
            --gm-border: #e2e8f0;
            --gm-muted: #64748b;
            --gm-bg: #f8fafc;
            --gm-primary: #0d6efd;
        }

        .go-muni-authorities .gm-card-compact .card-body {
            padding: 18px;
        }

        .go-muni-authorities .gm-step-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
        }

        .go-muni-authorities .gm-step-label + p {
            margin-bottom: 0;
        }

        .go-muni-authorities .gm-page-head {
            position: sticky;
            top: 76px;
            z-index: 10;
        }

        .go-muni-authorities .gm-dirty-indicator {
            background: #eef2ff;
            color: #4338ca;
            padding: 6px 10px;
            border-radius: 999px;
        }

        .go-muni-authorities .gm-switch {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .go-muni-authorities .gm-switch-help {
            font-size: 12px;
            color: #94a3b8;
        }

        .go-muni-authorities .gm-event-summary {
            background: var(--gm-bg);
            border-radius: 12px;
            padding: 12px 16px;
        }

        .go-muni-authorities .gm-event-status .badge {
            font-weight: 600;
        }

        .go-muni-authorities .gm-empty-state {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            border: 1px dashed var(--gm-border);
            border-radius: 12px;
            color: var(--gm-muted);
        }

        .go-muni-authorities .gm-empty-state i {
            font-size: 20px;
        }

        .go-muni-authorities .gm-count-badge {
            background: #e0f2fe;
            color: #0369a1;
            border-radius: 999px;
            padding: 6px 12px;
            font-weight: 600;
        }

        .go-muni-authorities .gm-chip-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .go-muni-authorities .gm-chip {
            border: 1px solid var(--gm-border);
            background: #fff;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 13px;
            color: #1f2937;
        }

        .go-muni-authorities .gm-chip.active {
            border-color: var(--gm-primary);
            color: var(--gm-primary);
            background: #eff6ff;
        }

        .go-muni-authorities .gm-authority-group {
            margin-bottom: 24px;
        }

        .go-muni-authorities .gm-authorities-list {
            margin-top: 16px;
        }

        .go-muni-authorities .gm-group-title {
            font-size: 13px;
            text-transform: uppercase;
            color: var(--gm-muted);
            letter-spacing: 0.04em;
        }

        .go-muni-authorities .gm-group-count {
            font-size: 12px;
            color: var(--gm-muted);
        }

        .go-muni-authorities .gm-authority-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 8px;
        }

        .go-muni-authorities .gm-authority-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid var(--gm-border);
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .go-muni-authorities .gm-authority-item:hover {
            border-color: #cbd5f5;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        }

        .go-muni-authorities .gm-authority-item:focus-within {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }

        .go-muni-authorities .gm-authority-name {
            font-weight: 600;
            font-size: 13px;
        }

        .go-muni-authorities .gm-authority-role {
            font-size: 11px;
            color: var(--gm-muted);
        }

        .go-muni-authorities .gm-segmented-control {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .go-muni-authorities .gm-selected-users {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .go-muni-authorities .gm-selected-users .gm-pill {
            background: #f1f5f9;
            color: #1e293b;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
        }

        .go-muni-authorities .gm-validation-step {
            border-bottom: 1px solid var(--gm-border);
            padding-bottom: 16px;
        }

        .go-muni-authorities .gm-validation-step:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .go-muni-authorities .gm-step-title {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .go-muni-authorities .gm-event-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            color: #047857;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
        }

        .go-muni-authorities .gm-event-pill.gm-warning {
            background: #fff7ed;
            color: #b45309;
        }

        .go-muni-authorities .gm-inline-note {
            font-size: 12px;
            color: var(--gm-muted);
        }

        .go-muni-authorities .gm-scroll {
            max-height: 320px;
            overflow: auto;
        }

        .go-muni-authorities .gm-table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }

        .go-muni-authorities .gm-mobile-save {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px;
            border-top: 1px solid var(--gm-border);
            margin-top: 24px;
        }

        @media (max-width: 991px) {
            .go-muni-authorities .gm-page-head {
                position: static;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const authoritiesData = JSON.parse(document.getElementById('authorities-data')?.textContent || '[]');
            const eventsData = JSON.parse(document.getElementById('events-data')?.textContent || '[]');
            const authorityList = document.getElementById('authority-list');
            const authorityFilters = document.getElementById('authority-filters');
            const authoritySearch = document.getElementById('authority-search');
            const selectedCountEl = document.getElementById('selected-count');
            const filteredCountEl = document.getElementById('filtered-count');
            const emptyStateEl = document.getElementById('authority-empty-state');
            const selectAllBtn = document.getElementById('select-all-authorities');
            const clearAllBtn = document.getElementById('clear-all-authorities');
            const eventSelect = document.getElementById('evento-select');
            const toggleAssigned = document.getElementById('toggle-assigned-events');
            const eventBadge = document.getElementById('event-status-badge');
            const eventSummary = document.getElementById('event-summary');
            const eventEmptyState = document.getElementById('event-empty-state');
            const saveButton = document.getElementById('save-authorities-btn');
            const saveButtonMobile = document.getElementById('save-authorities-btn-mobile');
            const dirtyIndicator = document.getElementById('dirty-indicator');
            const assignedSearch = document.getElementById('assigned-events-search');
            const assignedList = document.getElementById('assigned-events-list');
            const assignedCount = document.getElementById('assigned-events-count');
            const recipientSelect = document.getElementById('recipient-users');
            const recipientsContainer = document.getElementById('selected-recipients');
            const sendValidationBtn = document.getElementById('send-validation-btn');
            const sendValidationNote = document.getElementById('send-validation-note');
            const validationForm = document.getElementById('evento-validacion-form');
            const eventIdInput = validationForm?.querySelector('input[name="event_id"]');
            const confirmEventChange = document.getElementById('confirm-event-change');
            const changeModalEl = document.getElementById('event-change-modal');
            const saveToastEl = document.getElementById('save-toast');

            let searchTerm = '';
            let activeGroup = 'all';
            let pendingEventId = null;
            let previousEventId = eventSelect?.value || '';

            const selectedIds = new Set(
                authoritiesData.filter((authority) => authority.checked).map((authority) => String(authority.id))
            );
            const initialSelected = new Set(selectedIds);

            const normalize = (value) => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            const groupMap = new Map();
            authoritiesData.forEach((authority) => {
                if (!groupMap.has(authority.groupId)) {
                    groupMap.set(authority.groupId, { id: authority.groupId, name: authority.groupName });
                }
            });

            const updateDirtyState = () => {
                const isDirty =
                    initialSelected.size !== selectedIds.size ||
                    Array.from(initialSelected).some((id) => !selectedIds.has(id));
                if (saveButton) {
                    saveButton.disabled = !isDirty || !(eventSelect?.value || eventIdInput?.value);
                }
                if (saveButtonMobile) {
                    saveButtonMobile.disabled = !isDirty || !(eventSelect?.value || eventIdInput?.value);
                }
                if (dirtyIndicator) {
                    dirtyIndicator.textContent = isDirty ? 'Cambios pendientes' : 'Sin cambios pendientes';
                    dirtyIndicator.classList.toggle('text-muted', !isDirty);
                }
                return isDirty;
            };

            const updateSelectedCount = () => {
                if (selectedCountEl) {
                    selectedCountEl.textContent = selectedIds.size;
                }
            };

            const getFilteredAuthorities = () => {
                const term = searchTerm ? normalize(searchTerm) : '';
                return authoritiesData.filter((authority) => {
                    const matchesGroup = activeGroup === 'all' || authority.groupId === activeGroup;
                    const matchesTerm =
                        !term ||
                        normalize(authority.name).includes(term) ||
                        normalize(authority.role).includes(term);
                    return matchesGroup && matchesTerm;
                });
            };

            const renderFilters = () => {
                if (!authorityFilters) {
                    return;
                }
                const fragment = document.createDocumentFragment();
                const allButton = document.createElement('button');
                allButton.type = 'button';
                allButton.className = `gm-chip ${activeGroup === 'all' ? 'active' : ''}`;
                allButton.textContent = 'Todos';
                allButton.dataset.group = 'all';
                fragment.appendChild(allButton);

                groupMap.forEach((group) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `gm-chip ${activeGroup === group.id ? 'active' : ''}`;
                    button.textContent = group.name;
                    button.dataset.group = String(group.id);
                    fragment.appendChild(button);
                });

                authorityFilters.innerHTML = '';
                authorityFilters.appendChild(fragment);
            };

            const renderAuthorities = () => {
                if (!authorityList) {
                    return;
                }
                const filtered = getFilteredAuthorities();
                const grouped = new Map();

                filtered.forEach((authority) => {
                    if (!grouped.has(authority.groupId)) {
                        grouped.set(authority.groupId, []);
                    }
                    grouped.get(authority.groupId).push(authority);
                });

                const fragment = document.createDocumentFragment();
                grouped.forEach((items, groupId) => {
                    const groupInfo = groupMap.get(groupId);
                    if (!groupInfo) {
                        return;
                    }
                    const groupWrapper = document.createElement('div');
                    groupWrapper.className = 'gm-authority-group';

                    const header = document.createElement('div');
                    header.className = 'd-flex align-items-center justify-content-between mb-2';

                    const title = document.createElement('div');
                    title.className = 'gm-group-title';
                    title.textContent = groupInfo.name;

                    const totalGroup = authoritiesData.filter((authority) => authority.groupId === groupId).length;
                    const selectedGroup = authoritiesData.filter(
                        (authority) => authority.groupId === groupId && selectedIds.has(String(authority.id))
                    ).length;
                    const counter = document.createElement('div');
                    counter.className = 'gm-group-count';
                    counter.textContent = `${selectedGroup}/${totalGroup}`;

                    header.appendChild(title);
                    header.appendChild(counter);
                    groupWrapper.appendChild(header);

                    const grid = document.createElement('div');
                    grid.className = 'gm-authority-grid';

                    items.forEach((authority) => {
                        const label = document.createElement('label');
                        label.className = 'gm-authority-item';
                        label.setAttribute('for', `auth-${authority.id}`);

                        const input = document.createElement('input');
                        input.type = 'checkbox';
                        input.name = 'authorities[]';
                        input.value = authority.id;
                        input.id = `auth-${authority.id}`;
                        input.className = 'form-check-input m-0';
                        input.checked = selectedIds.has(String(authority.id));

                        const info = document.createElement('div');
                        info.className = 'd-flex flex-column';

                        const name = document.createElement('span');
                        name.className = 'gm-authority-name';
                        name.textContent = authority.name;

                        const role = document.createElement('span');
                        role.className = 'gm-authority-role';
                        role.textContent = authority.role;

                        info.appendChild(name);
                        info.appendChild(role);

                        label.appendChild(input);
                        label.appendChild(info);
                        grid.appendChild(label);
                    });

                    groupWrapper.appendChild(grid);
                    fragment.appendChild(groupWrapper);
                });

                authorityList.innerHTML = '';
                authorityList.appendChild(fragment);

                if (filteredCountEl) {
                    filteredCountEl.textContent = `Mostrando ${filtered.length} de ${authoritiesData.length} autoridades`;
                }

                if (emptyStateEl) {
                    emptyStateEl.classList.toggle('d-none', filtered.length > 0);
                }
            };

            const updateEventSummary = (eventId) => {
                if (!eventSummary || !eventBadge) {
                    return;
                }
                const selectedEvent = eventsData.find((event) => String(event.id) === String(eventId));
                if (!selectedEvent || !eventId) {
                    eventBadge.textContent = 'Sin autoridades asignadas';
                    eventBadge.className = 'badge bg-secondary-subtle text-secondary';
                    eventSummary.querySelector('.gm-event-title').textContent = 'Selecciona un evento para ver el resumen.';
                    eventSummary.querySelector('.gm-event-meta').textContent = 'Fecha pendiente';
                    if (eventEmptyState) {
                        eventEmptyState.classList.remove('d-none');
                    }
                    return;
                }
                const hasAuthorities = selectedEvent.hasAuthorities;
                eventBadge.textContent = hasAuthorities ? 'Con autoridades asignadas' : 'Sin autoridades asignadas';
                eventBadge.className = hasAuthorities ? 'badge bg-warning-subtle text-warning' : 'badge bg-secondary-subtle text-secondary';
                eventSummary.querySelector('.gm-event-title').textContent = selectedEvent.title;
                const dateText = selectedEvent.start ? `${selectedEvent.start}${selectedEvent.end ? ` · ${selectedEvent.end}` : ''}` : 'Fecha pendiente';
                eventSummary.querySelector('.gm-event-meta').textContent = dateText;
                if (eventEmptyState) {
                    eventEmptyState.classList.add('d-none');
                }
            };

            const rebuildEventOptions = (showAssigned) => {
                if (!eventSelect) {
                    return;
                }
                const fragment = document.createDocumentFragment();
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Selecciona un evento';
                fragment.appendChild(placeholder);

                eventsData.forEach((event) => {
                    if (!showAssigned && event.hasAuthorities) {
                        return;
                    }
                    const option = document.createElement('option');
                    option.value = event.id;
                    option.textContent = event.title;
                    option.dataset.start = event.start || '';
                    option.dataset.end = event.end || '';
                    option.dataset.hasAuthorities = event.hasAuthorities ? '1' : '0';
                    if (String(event.id) === String(eventSelect.value)) {
                        option.selected = true;
                    }
                    fragment.appendChild(option);
                });

                eventSelect.innerHTML = '';
                eventSelect.appendChild(fragment);
                updateEventSummary(eventSelect.value);
            };

            const updateRecipients = () => {
                if (!recipientSelect || !recipientsContainer) {
                    return [];
                }
                const selectedOptions = Array.from(recipientSelect.selectedOptions).map((option) => option.textContent.trim());
                const fragment = document.createDocumentFragment();
                selectedOptions.forEach((name) => {
                    const pill = document.createElement('span');
                    pill.className = 'gm-pill';
                    pill.textContent = name;
                    fragment.appendChild(pill);
                });
                recipientsContainer.innerHTML = '';
                recipientsContainer.appendChild(fragment);
                return selectedOptions;
            };

            const updateSendButton = () => {
                if (!sendValidationBtn || !eventIdInput || !recipientSelect) {
                    return;
                }
                const hasEvent = Number(eventIdInput.value) > 0;
                const hasRecipients = recipientSelect.selectedOptions.length > 0;
                sendValidationBtn.disabled = !(hasEvent && hasRecipients);
                if (sendValidationNote) {
                    sendValidationNote.textContent = hasEvent && hasRecipients
                        ? 'Listo para enviar la validación.'
                        : 'Selecciona un evento y destinatarios para continuar.';
                }
            };

            const updateAssignedEvents = () => {
                if (!assignedList || !assignedSearch) {
                    return;
                }
                const term = normalize(assignedSearch.value || '');
                let visible = 0;
                assignedList.querySelectorAll('.list-group-item').forEach((item) => {
                    const title = normalize(item.dataset.title || '');
                    const matches = !term || title.includes(term);
                    item.classList.toggle('d-none', !matches);
                    if (matches) {
                        visible += 1;
                    }
                });
                if (assignedCount) {
                    assignedCount.textContent = visible;
                }
            };

            if (authorityFilters) {
                authorityFilters.addEventListener('click', (event) => {
                    const button = event.target.closest('button[data-group]');
                    if (!button) {
                        return;
                    }
                    const groupValue = button.dataset.group;
                    activeGroup = groupValue === 'all' ? 'all' : Number(groupValue);
                    renderFilters();
                    renderAuthorities();
                });
            }

            if (authoritySearch) {
                let searchTimeout;
                authoritySearch.addEventListener('input', (event) => {
                    const value = event.target.value;
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchTerm = value;
                        renderAuthorities();
                    }, 150);
                });
            }

            if (authorityList) {
                authorityList.addEventListener('change', (event) => {
                    if (event.target instanceof HTMLInputElement && event.target.name === 'authorities[]') {
                        const id = event.target.value;
                        if (event.target.checked) {
                            selectedIds.add(id);
                        } else {
                            selectedIds.delete(id);
                        }
                        updateSelectedCount();
                        updateDirtyState();
                        renderAuthorities();
                    }
                });
            }

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', () => {
                    getFilteredAuthorities().forEach((authority) => {
                        selectedIds.add(String(authority.id));
                    });
                    updateSelectedCount();
                    updateDirtyState();
                    renderAuthorities();
                });
            }

            if (clearAllBtn) {
                clearAllBtn.addEventListener('click', () => {
                    getFilteredAuthorities().forEach((authority) => {
                        selectedIds.delete(String(authority.id));
                    });
                    updateSelectedCount();
                    updateDirtyState();
                    renderAuthorities();
                });
            }

            if (eventSelect) {
                eventSelect.addEventListener('change', () => {
                    const selectedId = eventSelect.value;
                    const isDirty = updateDirtyState();
                    if (!selectedId) {
                        updateEventSummary('');
                        return;
                    }
                    if (isDirty && changeModalEl && confirmEventChange) {
                        pendingEventId = selectedId;
                        eventSelect.value = previousEventId;
                        const modal = bootstrap.Modal.getOrCreateInstance(changeModalEl);
                        modal.show();
                        return;
                    }
                    window.location.href = `eventos-autoridades.php?event_id=${encodeURIComponent(selectedId)}`;
                });
            }

            if (confirmEventChange) {
                confirmEventChange.addEventListener('click', () => {
                    if (pendingEventId) {
                        window.location.href = `eventos-autoridades.php?event_id=${encodeURIComponent(pendingEventId)}`;
                    }
                });
            }

            if (toggleAssigned) {
                toggleAssigned.addEventListener('change', () => {
                    rebuildEventOptions(toggleAssigned.checked);
                });
            }

            if (recipientSelect) {
                recipientSelect.addEventListener('change', () => {
                    updateRecipients();
                    updateSendButton();
                });
            }

            if (validationForm) {
                validationForm.addEventListener('submit', () => {
                    if (!sendValidationBtn) {
                        return;
                    }
                    const spinner = sendValidationBtn.querySelector('.spinner-border');
                    const label = sendValidationBtn.querySelector('.gm-send-label');
                    sendValidationBtn.disabled = true;
                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                    if (label) {
                        label.textContent = 'Enviando...';
                    }
                });
            }

            if (assignedSearch) {
                assignedSearch.addEventListener('input', updateAssignedEvents);
            }

            if (saveToastEl) {
                const toast = bootstrap.Toast.getOrCreateInstance(saveToastEl);
                toast.show();
            }

            if (toggleAssigned && toggleAssigned.disabled) {
                toggleAssigned.closest('.gm-switch')?.classList.add('text-muted');
            }

            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((trigger) => {
                new bootstrap.Tooltip(trigger);
            });

            updateSelectedCount();
            renderFilters();
            renderAuthorities();
            updateEventSummary(eventSelect?.value || eventIdInput?.value || '');
            updateDirtyState();
            updateRecipients();
            updateSendButton();
            updateAssignedEvents();
        });
    </script>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
