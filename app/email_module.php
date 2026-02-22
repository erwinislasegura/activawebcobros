<?php

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

    $body = imap_fetchbody($imap, (string) $uid, '1', FT_UID);
    if ($body === '') {
        $body = imap_body($imap, (string) $uid, FT_UID);
    }

    $message = [
        'uid' => $uid,
        'subject' => isset($overview->subject) ? (imap_utf8((string) $overview->subject) ?: '(Sin asunto)') : '(Sin asunto)',
        'from' => isset($overview->from) ? imap_utf8((string) $overview->from) : '-',
        'to' => isset($overview->to) ? imap_utf8((string) $overview->to) : '-',
        'date' => $overview->date ?? '-',
        'seen' => !empty($overview->seen),
        'body' => $body,
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
