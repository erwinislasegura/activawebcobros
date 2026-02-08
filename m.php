<?php
require __DIR__ . '/app/bootstrap.php';

$code = trim($_GET['c'] ?? '');
if ($code === '') {
    http_response_code(400);
    echo 'Código de acceso no válido.';
    exit;
}

$stmt = db()->prepare('SELECT token FROM event_media_accreditation_links WHERE short_code = ? LIMIT 1');
$stmt->execute([$code]);
$token = $stmt->fetchColumn();

if (!$token) {
    http_response_code(404);
    echo 'Enlace no encontrado.';
    exit;
}

header('Location: medios-acreditacion.php?token=' . urlencode($token));
exit;
