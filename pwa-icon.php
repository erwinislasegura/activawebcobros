<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$municipalidad = get_municipalidad();
$logoPath = (string) ($municipalidad['logo_path'] ?? 'assets/images/logo.png');
$size = (int) ($_GET['size'] ?? 192);
if (!in_array($size, [192, 512], true)) {
    $size = 192;
}

$path = $logoPath;
if (!preg_match('/^https?:\/\//i', $path)) {
    $path = __DIR__ . '/' . ltrim($path, '/');
}

if (preg_match('/^https?:\/\//i', $logoPath) || !is_file($path) || !is_readable($path) || !function_exists('imagecreatetruecolor')) {
    $fallback = preg_match('/^https?:\/\//i', $logoPath)
        ? $logoPath
        : rtrim(base_url(), '/') . '/' . ltrim($logoPath, '/');
    header('Location: ' . $fallback, true, 302);
    exit;
}

$raw = file_get_contents($path);
$src = $raw !== false ? @imagecreatefromstring($raw) : false;
if (!$src) {
    $fallback = rtrim(base_url(), '/') . '/' . ltrim($logoPath, '/');
    header('Location: ' . $fallback, true, 302);
    exit;
}

$srcW = imagesx($src);
$srcH = imagesy($src);
$dst = imagecreatetruecolor($size, $size);
imagesavealpha($dst, true);
$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
imagefill($dst, 0, 0, $transparent);

$scale = min($size / max(1, $srcW), $size / max(1, $srcH));
$newW = (int) floor($srcW * $scale);
$newH = (int) floor($srcH * $scale);
$dstX = (int) floor(($size - $newW) / 2);
$dstY = (int) floor(($size - $newH) / 2);

imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);

header('Content-Type: image/png');
imagepng($dst);
imagedestroy($src);
imagedestroy($dst);
