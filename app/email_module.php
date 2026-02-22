<?php

function email_get_account_config(): array
{
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

function email_fetch_messages(array $config, string $folder, int $limit = 20): array
{
    if (!function_exists('imap_open')) {
        return ['emails' => [], 'warning' => 'La extensión IMAP no está habilitada en PHP.'];
    }

    if (($config['in_email'] ?? '') === '' || ($config['in_password'] ?? '') === '' || ($config['in_host'] ?? '') === '') {
        return ['emails' => [], 'warning' => 'Falta configurar el buzón de entrada.'];
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
        return ['emails' => [], 'warning' => 'No se pudo conectar al buzón o carpeta solicitada.'];
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

function email_local_list(string $box): array
{
    $stmt = db()->prepare('SELECT id, recipient, subject, body_html, status, created_at FROM email_messages WHERE box = ? ORDER BY id DESC LIMIT 50');
    $stmt->execute([$box]);
    return $stmt->fetchAll() ?: [];
}

function email_local_store(string $box, string $recipient, string $subject, string $body, string $status): void
{
    $stmt = db()->prepare('INSERT INTO email_messages (box, recipient, subject, body_html, status) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$box, $recipient !== '' ? $recipient : null, $subject, $body, $status]);
}
