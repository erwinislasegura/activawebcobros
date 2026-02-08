<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $publicScripts = [
        'auth-2-sign-in.php',
        'auth-2-login-pin.php',
        'auth-2-new-pass.php',
        'auth-2-reset-pass.php',
        'auth-2-sign-up.php',
        'auth-2-success-mail.php',
        'auth-2-two-factor.php',
        'auth-login-pin.php',
        'auth-sign-in.php',
        'auth-sign-up.php',
        'auth-new-pass.php',
        'auth-reset-pass.php',
        'auth-success-mail.php',
        'auth-two-factor.php',
        'login.php',
        'logout.php',
        'confirmar-asistencia.php',
        'eventos-validacion.php',
        'medios-acreditacion.php',
        'medios-acreditacion-imprimir.php',
        'eventos-acreditacion-medios.php',
        'm.php',
    ];

    if (!in_array($currentScript, $publicScripts, true) && strncmp($currentScript, 'auth-', 5) !== 0) {
        header('Location: auth-2-sign-in.php');
        exit;
    }
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $settings = $GLOBALS['config']['db'];
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $settings['host'],
        $settings['name'],
        $settings['charset']
    );

    $pdo = new PDO($dsn, $settings['user'], $settings['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

    return $scheme . '://' . $host . ($basePath !== '' ? $basePath : '');
}

function ensure_event_validation_token(int $eventId, ?string $currentToken): string
{
    if (!empty($currentToken)) {
        return $currentToken;
    }

    $token = bin2hex(random_bytes(16));
    $stmt = db()->prepare('UPDATE events SET validation_token = ? WHERE id = ?');
    $stmt->execute([$token, $eventId]);

    return $token;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function get_municipalidad(): array
{
    try {
        $stmt = db()->query('SELECT * FROM municipalidad LIMIT 1');
        $municipalidad = $stmt->fetch();
        if (is_array($municipalidad)) {
            return $municipalidad;
        }
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    return [
        'nombre' => 'Go Muni',
        'logo_path' => 'assets/images/logo.png',
        'logo_topbar_height' => 56,
        'logo_sidenav_height' => 48,
        'logo_sidenav_height_sm' => 36,
        'logo_auth_height' => 48,
        'color_primary' => '#6658dd',
        'color_secondary' => '#4a81d4',
    ];
}

function hex_to_rgb(string $hex): ?array
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return null;
    }
    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    ];
}

function ensure_event_types(): array
{
    $defaults = [
        ['nombre' => 'Reunión', 'color_class' => 'bg-primary-subtle text-primary'],
        ['nombre' => 'Operativo', 'color_class' => 'bg-secondary-subtle text-secondary'],
        ['nombre' => 'Ceremonia', 'color_class' => 'bg-success-subtle text-success'],
        ['nombre' => 'Actividad cultural', 'color_class' => 'bg-warning-subtle text-warning'],
    ];

    try {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS event_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(120) NOT NULL,
                color_class VARCHAR(120) NOT NULL DEFAULT "bg-primary-subtle text-primary",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $count = (int) db()->query('SELECT COUNT(*) FROM event_types')->fetchColumn();
        if ($count === 0) {
            $stmt = db()->prepare('INSERT INTO event_types (nombre, color_class) VALUES (?, ?)');
            foreach ($defaults as $default) {
                $stmt->execute([$default['nombre'], $default['color_class']]);
            }
        }

        return db()->query('SELECT id, nombre, color_class FROM event_types ORDER BY nombre')->fetchAll();
    } catch (Exception $e) {
    } catch (Error $e) {
    }

    return $defaults;
}

function current_role_id(): ?int
{
    if (!isset($_SESSION['user']['rol'])) {
        return null;
    }

    static $roleIdCache = [];
    $roleName = (string) $_SESSION['user']['rol'];
    if ($roleName === '') {
        return null;
    }
    if (array_key_exists($roleName, $roleIdCache)) {
        return $roleIdCache[$roleName];
    }

    try {
        $stmt = db()->prepare('SELECT id FROM roles WHERE nombre = ? LIMIT 1');
        $stmt->execute([$roleName]);
        $roleId = $stmt->fetchColumn();
        $roleIdCache[$roleName] = $roleId ? (int) $roleId : null;
    } catch (Exception $e) {
        $roleIdCache[$roleName] = null;
    } catch (Error $e) {
        $roleIdCache[$roleName] = null;
    }

    return $roleIdCache[$roleName];
}

function is_superuser(): bool
{
    if (!isset($_SESSION['user']['rol'])) {
        return false;
    }

    $roleName = strtolower((string) $_SESSION['user']['rol']);
    return $roleName !== '' && (str_contains($roleName, 'super') || str_contains($roleName, 'admin'));
}

function has_permission(string $module, string $action = 'view'): bool
{
    if (!isset($_SESSION['user'])) {
        return true;
    }

    if (is_superuser()) {
        return true;
    }

    $roleId = current_role_id();
    if (!$roleId) {
        return true;
    }

    try {
        $stmt = db()->prepare('SELECT COUNT(*) FROM role_permissions WHERE role_id = ?');
        $stmt->execute([$roleId]);
        $hasAssignments = (int) $stmt->fetchColumn() > 0;
        if (!$hasAssignments) {
            return true;
        }

        $stmt = db()->prepare('SELECT id FROM permissions WHERE modulo = ? AND accion = ? LIMIT 1');
        $stmt->execute([$module, $action]);
        $permissionId = $stmt->fetchColumn();
        if (!$permissionId) {
            return true;
        }

        $stmt = db()->prepare('SELECT 1 FROM role_permissions WHERE role_id = ? AND permission_id = ?');
        $stmt->execute([$roleId, (int) $permissionId]);
        return (bool) $stmt->fetchColumn();
    } catch (Exception $e) {
        return true;
    } catch (Error $e) {
        return true;
    }
}
