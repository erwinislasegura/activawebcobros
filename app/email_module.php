<?php


function email_sanitize_html(string $html): string
{
    $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><a><blockquote><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><th><td><span><div><img><hr>';
    $clean = strip_tags($html, $allowed);
    $clean = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $clean) ?? $clean;
    return $clean;
}

function email_decode_part(string $content, int $encoding): string
{
    if ($encoding === 3) {
        $decoded = base64_decode($content, true);
        return $decoded !== false ? $decoded : $content;
    }

    if ($encoding === 4) {
        return quoted_printable_decode($content);
    }

    return $content;
}

function email_get_part_parameter(object $structure, string $name): ?string
{
    $name = strtolower($name);

    $sources = [];
    if (!empty($structure->parameters) && is_array($structure->parameters)) {
        $sources = array_merge($sources, $structure->parameters);
    }
    if (!empty($structure->dparameters) && is_array($structure->dparameters)) {
        $sources = array_merge($sources, $structure->dparameters);
    }

    foreach ($sources as $param) {
        $attr = strtolower((string) ($param->attribute ?? ''));
        if ($attr === $name) {
            return (string) ($param->value ?? '');
        }
    }

    return null;
}

function email_extract_parts($imap, int $uid, object $structure, string $prefix = ''): array
{
    $items = [];

    $isMultipart = isset($structure->type) && (int) $structure->type === 1 && !empty($structure->parts);
    if ($isMultipart) {
        foreach ($structure->parts as $index => $part) {
            $childNumber = $prefix === '' ? (string) ($index + 1) : $prefix . '.' . ($index + 1);
            $items = array_merge($items, email_extract_parts($imap, $uid, $part, $childNumber));
        }
        return $items;
    }

    $partNumber = $prefix !== '' ? $prefix : '1';
    $raw = imap_fetchbody($imap, (string) $uid, $partNumber, FT_UID);
    if ($raw === '' && $prefix === '') {
        $raw = imap_body($imap, (string) $uid, FT_UID);
    }

    $encoding = isset($structure->encoding) ? (int) $structure->encoding : 0;
    $decoded = email_decode_part($raw, $encoding);

    $primaryTypes = [
        0 => 'text',
        1 => 'multipart',
        2 => 'message',
        3 => 'application',
        4 => 'audio',
        5 => 'image',
        6 => 'video',
        7 => 'other',
    ];

    $primary = $primaryTypes[(int) ($structure->type ?? 0)] ?? 'other';
    $subtype = strtolower((string) ($structure->subtype ?? 'plain'));
    $mime = $primary . '/' . $subtype;

    $contentId = (string) ($structure->id ?? '');
    $contentId = trim($contentId, '<> 	
');

    $filename = email_get_part_parameter($structure, 'filename')
        ?? email_get_part_parameter($structure, 'name')
        ?? null;

    $disposition = strtolower((string) ($structure->disposition ?? ''));

    $items[] = [
        'mime' => $mime,
        'content' => $decoded,
        'content_id' => $contentId,
        'filename' => $filename,
        'disposition' => $disposition,
    ];

    return $items;
}

function email_enrich_html_with_inline_images(string $html, array $parts): string
{
    foreach ($parts as $part) {
        if (str_starts_with($part['mime'], 'image/') && !empty($part['content_id']) && $part['content'] !== '') {
            $dataUri = 'data:' . $part['mime'] . ';base64,' . base64_encode($part['content']);
            $cid = preg_quote((string) $part['content_id'], '/');
            $html = preg_replace('/src=["\']cid:' . $cid . '["\']/i', 'src="' . $dataUri . '"', $html) ?? $html;
        }
    }

    return $html;
}

function email_extract_attachments(array $parts): array
{
    $attachments = [];
    foreach ($parts as $part) {
        $isAttachment = ($part['disposition'] ?? '') === 'attachment' || !empty($part['filename']);
        if ($isAttachment && !str_starts_with((string) $part['mime'], 'text/')) {
            $attachments[] = [
                'filename' => $part['filename'] ?: 'adjunto',
                'mime' => $part['mime'],
            ];
        }
    }

    return $attachments;
}

function email_select_best_body(array $parts): array
{
    foreach ($parts as $part) {
        if (($part['mime'] ?? '') === 'text/html' && trim((string) $part['content']) !== '') {
            $html = email_enrich_html_with_inline_images((string) $part['content'], $parts);
            return ['html' => email_sanitize_html($html), 'is_html' => true];
        }
    }

    foreach ($parts as $part) {
        if (($part['mime'] ?? '') === 'text/plain' && trim((string) $part['content']) !== '') {
            return ['html' => nl2br(htmlspecialchars((string) $part['content'], ENT_QUOTES, 'UTF-8')), 'is_html' => false];
        }
    }

    return ['html' => '<p>Sin contenido</p>', 'is_html' => false];
}

function email_ensure_tables(): void
{
    try {
        db()->exec("CREATE TABLE IF NOT EXISTS email_accounts (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            in_email VARCHAR(150) NOT NULL,
            in_password VARCHAR(255) NOT NULL,
            in_host VARCHAR(150) NOT NULL,
            in_port INT UNSIGNED NOT NULL DEFAULT 993,
            in_security VARCHAR(20) NOT NULL DEFAULT 'ssl',
            out_email VARCHAR(150) NOT NULL,
            out_name VARCHAR(150) DEFAULT NULL,
            out_password VARCHAR(255) NOT NULL,
            out_host VARCHAR(150) NOT NULL,
            out_port INT UNSIGNED NOT NULL DEFAULT 587,
            out_security VARCHAR(20) NOT NULL DEFAULT 'tls',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        db()->exec("CREATE TABLE IF NOT EXISTS email_messages (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            box VARCHAR(30) NOT NULL,
            recipient VARCHAR(150) DEFAULT NULL,
            subject VARCHAR(255) NOT NULL,
            body_html MEDIUMTEXT NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'draft',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_email_messages_box (box)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (Throwable $e) {
    }
}

function email_get_account_config(): array
{
    email_ensure_tables();
    $stmt = db()->query('SELECT * FROM email_accounts ORDER BY id ASC LIMIT 1');
    $row = $stmt->fetch();
    if ($row) {
        return $row;
    }

    $legacy = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];

    return [
        'in_email' => $legacy['correo_imap'] ?? '',
        'in_password' => $legacy['password_imap'] ?? '',
        'in_host' => $legacy['host_imap'] ?? '',
        'in_port' => $legacy['puerto_imap'] ?? 993,
        'in_security' => $legacy['seguridad_imap'] ?? 'ssl',
        'out_email' => $legacy['from_correo'] ?? ($legacy['correo_imap'] ?? ''),
        'out_name' => $legacy['from_nombre'] ?? '',
        'out_password' => $legacy['password_imap'] ?? '',
        'out_host' => '',
        'out_port' => 587,
        'out_security' => 'tls',
    ];
}

function email_save_config(array $config): void
{
    email_ensure_tables();
    $id = db()->query('SELECT id FROM email_accounts ORDER BY id ASC LIMIT 1')->fetchColumn();
    $params = [
        $config['in_email'],
        $config['in_password'],
        $config['in_host'],
        (int) $config['in_port'],
        $config['in_security'],
        $config['out_email'],
        $config['out_name'] !== '' ? $config['out_name'] : null,
        $config['out_password'],
        $config['out_host'],
        (int) $config['out_port'],
        $config['out_security'],
    ];

    if ($id) {
        $stmt = db()->prepare('UPDATE email_accounts SET in_email=?, in_password=?, in_host=?, in_port=?, in_security=?, out_email=?, out_name=?, out_password=?, out_host=?, out_port=?, out_security=? WHERE id=?');
        $params[] = $id;
        $stmt->execute($params);
        return;
    }

    $stmt = db()->prepare('INSERT INTO email_accounts (in_email, in_password, in_host, in_port, in_security, out_email, out_name, out_password, out_host, out_port, out_security) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute($params);
}

function email_get_folder_path(string $folder): string
{
    if ($folder === 'sent') {
        return 'Sent';
    }
    if ($folder === 'spam') {
        return 'Spam';
    }
    return 'INBOX';
}

function email_imap_connection(array $config, string $folder)
{
    if (!function_exists('imap_open')) {
        return [null, 'La extensión IMAP no está habilitada en PHP.'];
    }

    if (($config['in_email'] ?? '') === '' || ($config['in_password'] ?? '') === '' || ($config['in_host'] ?? '') === '') {
        return [null, 'Falta configurar el buzón de entrada.'];
    }

    $security = strtolower((string) ($config['in_security'] ?? 'ssl'));
    $flags = '/imap';
    if ($security === 'ssl') {
        $flags .= '/ssl';
    } elseif ($security === 'tls') {
        $flags .= '/tls';
    } else {
        $flags .= '/notls';
    }

    $mailbox = '{' . $config['in_host'] . ':' . (int) ($config['in_port'] ?? 993) . $flags . '}' . email_get_folder_path($folder);
    $imap = @imap_open($mailbox, (string) $config['in_email'], (string) $config['in_password']);
    if ($imap === false) {
        return [null, 'No se pudo conectar al buzón o carpeta solicitada.'];
    }

    return [$imap, null];
}

function email_fetch_messages(array $config, string $folder, int $limit = 30): array
{
    [$imap, $warning] = email_imap_connection($config, $folder);
    if (!$imap) {
        return ['emails' => [], 'warning' => $warning];
    }

    $uids = imap_search($imap, 'ALL', SE_UID) ?: [];
    rsort($uids);
    $uids = array_slice($uids, 0, $limit);

    $emails = [];
    foreach ($uids as $uid) {
        $overviewList = imap_fetch_overview($imap, (string) $uid, FT_UID);
        $overview = $overviewList[0] ?? null;
        if (!$overview) {
            continue;
        }
        $emails[] = [
            'uid' => $uid,
            'subject' => isset($overview->subject) ? (imap_utf8((string) $overview->subject) ?: '(Sin asunto)') : '(Sin asunto)',
            'from' => isset($overview->from) ? imap_utf8((string) $overview->from) : '-',
            'to' => isset($overview->to) ? imap_utf8((string) $overview->to) : '-',
            'date' => $overview->date ?? '-',
            'seen' => !empty($overview->seen),
        ];
    }

    imap_close($imap);

    return ['emails' => $emails, 'warning' => null];
}

function email_fetch_message_detail(array $config, string $folder, int $uid): array
{
    [$imap, $warning] = email_imap_connection($config, $folder);
    if (!$imap) {
        return ['message' => null, 'warning' => $warning];
    }

    $overviewList = imap_fetch_overview($imap, (string) $uid, FT_UID);
    $overview = $overviewList[0] ?? null;
    if (!$overview) {
        imap_close($imap);
        return ['message' => null, 'warning' => 'No se encontró el correo seleccionado.'];
    }

    $structure = imap_fetchstructure($imap, (string) $uid, FT_UID);
    $parts = [];
    if ($structure) {
        $parts = email_extract_parts($imap, $uid, $structure);
    }

    if (empty($parts)) {
        $fallbackBody = imap_body($imap, (string) $uid, FT_UID);
        $parts[] = [
            'mime' => 'text/plain',
            'content' => $fallbackBody !== '' ? $fallbackBody : 'Sin contenido',
        ];
    }

    $selectedBody = email_select_best_body($parts);

    $message = [
        'uid' => $uid,
        'subject' => isset($overview->subject) ? (imap_utf8((string) $overview->subject) ?: '(Sin asunto)') : '(Sin asunto)',
        'from' => isset($overview->from) ? imap_utf8((string) $overview->from) : '-',
        'to' => isset($overview->to) ? imap_utf8((string) $overview->to) : '-',
        'date' => $overview->date ?? '-',
        'seen' => !empty($overview->seen),
        'body_html' => $selectedBody['html'],
        'body_is_html' => $selectedBody['is_html'],
        'attachments' => email_extract_attachments($parts),
    ];

    imap_close($imap);

    return ['message' => $message, 'warning' => null];
}

function email_mark_seen(array $config, string $folder, int $uid, bool $seen): ?string
{
    [$imap, $warning] = email_imap_connection($config, $folder);
    if (!$imap) {
        return $warning;
    }

    if ($seen) {
        @imap_setflag_full($imap, (string) $uid, '\\Seen', ST_UID);
    } else {
        @imap_clearflag_full($imap, (string) $uid, '\\Seen', ST_UID);
    }

    imap_close($imap);
    return null;
}

function email_local_list(string $box): array
{
    email_ensure_tables();
    $stmt = db()->prepare('SELECT id, recipient, subject, body_html, status, created_at FROM email_messages WHERE box = ? ORDER BY id DESC LIMIT 50');
    $stmt->execute([$box]);
    return $stmt->fetchAll() ?: [];
}

function email_local_store(string $box, string $recipient, string $subject, string $body, string $status): void
{
    email_ensure_tables();
    $stmt = db()->prepare('INSERT INTO email_messages (box, recipient, subject, body_html, status) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$box, $recipient !== '' ? $recipient : null, $subject, $body, $status]);
}

function email_local_get(int $id): ?array
{
    email_ensure_tables();
    $stmt = db()->prepare('SELECT id, box, recipient, subject, body_html, status, created_at FROM email_messages WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}
