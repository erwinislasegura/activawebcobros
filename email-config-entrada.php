<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/email_module.php';

$config = email_get_account_config();
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $config['in_email'] = trim($_POST['in_email'] ?? '');
    $config['in_password'] = trim($_POST['in_password'] ?? '');
    $config['in_host'] = trim($_POST['in_host'] ?? '');
    $config['in_port'] = (int) ($_POST['in_port'] ?? 993);
    $config['in_security'] = trim($_POST['in_security'] ?? 'ssl');

    if ($config['in_email'] === '' || $config['in_password'] === '' || $config['in_host'] === '') {
        $errors[] = 'Completa correo, contraseña y host IMAP.';
    }

    if (empty($errors)) {
        email_save_config($config);
        $success = 'Configuración de entrada guardada.';
    }
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = 'Config. Entrada Email'; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
<?php include('partials/menu.php'); ?>
<div class="content-page"><div class="container-fluid">
<?php $subtitle = 'Buzón'; $title = 'Configuración de Entrada (IMAP)'; include('partials/page-title.php'); ?>
<?php if ($success) : ?><div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
<?php foreach ($errors as $e) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
<div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Cuenta de entrada</h5><button class="btn btn-sm btn-outline-secondary" id="preset-gmail-in" type="button">Preset Gmail</button></div><div class="card-body">
<form method="post" class="row g-3">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
<div class="col-md-6"><label class="form-label">Correo</label><input class="form-control" name="in_email" type="email" value="<?php echo htmlspecialchars($config['in_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-6"><label class="form-label">Contraseña</label><input class="form-control" name="in_password" type="password" value="<?php echo htmlspecialchars($config['in_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-4"><label class="form-label">Host</label><input class="form-control" id="in-host" name="in_host" type="text" value="<?php echo htmlspecialchars($config['in_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-2"><label class="form-label">Puerto</label><input class="form-control" id="in-port" name="in_port" type="number" value="<?php echo htmlspecialchars((string) ($config['in_port'] ?? 993), ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-3"><label class="form-label">Seguridad</label><?php $is = $config['in_security'] ?? 'ssl'; ?><select class="form-select" id="in-security" name="in_security"><option value="ssl" <?php echo $is === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="tls" <?php echo $is === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="none" <?php echo $is === 'none' ? 'selected' : ''; ?>>None</option></select></div>
<div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Guardar</button></div>
</form></div></div>
</div><?php include('partials/footer.php'); ?></div>
</div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
<script>
document.getElementById('preset-gmail-in')?.addEventListener('click', function(){document.getElementById('in-host').value='imap.gmail.com';document.getElementById('in-port').value='993';document.getElementById('in-security').value='ssl';});
</script>
</body></html>
