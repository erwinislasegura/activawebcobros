<?php

declare(strict_types=1);

function mailer_parse_recipients(?string $raw): array
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return [];
    }

    $parts = preg_split('/[;,\s]+/', $raw) ?: [];
    $emails = [];

    foreach ($parts as $email) {
        $email = mb_strtolower(trim((string) $email), 'UTF-8');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        if (!in_array($email, $emails, true)) {
            $emails[] = $email;
        }
    }

    return $emails;
}

function mailer_send_html(array $recipients, string $subject, string $bodyHtml, string $fromEmail, string $fromName = ''): bool
{
    $fromEmail = trim($fromEmail);
    $fromName = trim($fromName);

    if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $cleanRecipients = [];
    foreach ($recipients as $recipient) {
        $recipient = mb_strtolower(trim((string) $recipient), 'UTF-8');
        if ($recipient !== '' && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $cleanRecipients[] = $recipient;
        }
    }

    $cleanRecipients = array_values(array_unique($cleanRecipients));
    if (empty($cleanRecipients)) {
        return false;
    }

    $safeSubject = trim(str_replace(["\r", "\n"], ' ', $subject));
    $subjectEncoded = '=?UTF-8?B?' . base64_encode($safeSubject !== '' ? $safeSubject : 'Notificación') . '?=';
    $displayName = $fromName !== '' ? mb_encode_mimeheader($fromName, 'UTF-8') : $fromEmail;

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'Date: ' . date(DATE_RFC2822),
        'From: ' . $displayName . ' <' . $fromEmail . '>',
        'Reply-To: ' . $fromEmail,
        'Return-Path: ' . $fromEmail,
        'X-Mailer: PHP/' . phpversion(),
        'X-Auto-Response-Suppress: OOF, AutoReply',
    ];

    $headersString = implode("\r\n", $headers);
    $extraParams = '-f ' . escapeshellarg($fromEmail);

    foreach ($cleanRecipients as $recipient) {
        $host = preg_replace('/[^a-z0-9.-]/i', '', (string) (parse_url(base_url(), PHP_URL_HOST) ?: 'localhost'));
        $messageId = sprintf('<%s.%s@%s>', time(), bin2hex(random_bytes(6)), $host);
        $headersWithMessageId = $headersString . "\r\nMessage-ID: " . $messageId;

        $sent = @mail($recipient, $subjectEncoded, $bodyHtml, $headersWithMessageId, $extraParams);
        if (!$sent) {
            $sent = @mail($recipient, $subjectEncoded, $bodyHtml, $headersWithMessageId);
        }
        if (!$sent) {
            return false;
        }
    }

    return true;
}
