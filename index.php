<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$targetPath = $requestPath !== null ? ltrim($requestPath, '/') : '';

if ($targetPath !== '' && $targetPath !== 'index.php') {
    $targetFile = __DIR__ . '/' . $targetPath;
    if (is_file($targetFile)) {
        require $targetFile;
        exit;
    }
}

redirect('auth-2-sign-in.php');
