<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$municipalidad = get_municipalidad();
$name = trim((string) ($municipalidad['nombre'] ?? 'Go Cobros'));
if ($name === '') {
    $name = 'Go Cobros';
}
$shortName = mb_substr($name, 0, 24);
$description = 'Go Cobros · tecnologia escalable para la gestión de eventos, autoridades y validaciones ciudadanas.';
$themeColor = (string) ($municipalidad['color_primary'] ?? '#6658dd');
$logoPath = (string) ($municipalidad['logo_path'] ?? 'assets/images/logo.png');

$assetUrl = static function (string $path): string {
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    return rtrim(base_url(), '/') . '/' . ltrim($path, '/');
};

$icon192 = $assetUrl('pwa-icon.php?size=192');
$icon512 = $assetUrl('pwa-icon.php?size=512');

$manifest = [
    'name' => $name,
    'short_name' => $shortName,
    'description' => $description,
    'start_url' => './index.php',
    'scope' => './',
    'display' => 'standalone',
    'orientation' => 'portrait',
    'background_color' => '#ffffff',
    'theme_color' => $themeColor,
    'icons' => [
        [
            'src' => $icon192,
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
        [
            'src' => $icon512,
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'maskable any',
        ],
    ],
];

header('Content-Type: application/manifest+json; charset=utf-8');
echo json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
