<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/email_module.php';

$config = email_get_account_config();
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $config['out_email'] = trim($_POST['out_email'] ?? '');
    $config['out_name'] = trim($_POST['out_name'] ?? '');
    $config['out_password'] = trim($_POST['out_password'] ?? '');
    $config['out_host'] = trim($_POST['out_host'] ?? '');
    $config['out_port'] = (int) ($_POST['out_port'] ?? 587);
    $config['out_security'] = trim($_POST['out_security'] ?? 'tls');

    if ($config['out_email'] === '' || $config['out_host'] === '') {
        $errors[] = 'Completa correo y host SMTP.';
    }

    if (empty($errors)) {
        email_save_config($config);
        $success = 'Configuración de salida guardada.';
    }
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = 'Config. Salida Email'; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
<?php include('partials/menu.php'); ?>
<div class="content-page"><div class="container-fluid">
<?php $subtitle = 'Buzón'; $title = 'Configuración de Salida (SMTP)'; include('partials/page-title.php'); ?>
<?php if ($success) : ?><div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
<?php foreach ($errors as $e) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
<div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Cuenta de salida</h5><button class="btn btn-sm btn-outline-secondary" id="preset-gmail-out" type="button">Preset Gmail</button></div><div class="card-body">
<form method="post" class="row g-3">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
<div class="col-md-4"><label class="form-label">Correo</label><input class="form-control" name="out_email" type="email" value="<?php echo htmlspecialchars($config['out_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-4"><label class="form-label">Nombre remitente</label><input class="form-control" name="out_name" type="text" value="<?php echo htmlspecialchars($config['out_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-4"><label class="form-label">Contraseña</label><input class="form-control" name="out_password" type="password" value="<?php echo htmlspecialchars($config['out_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-4"><label class="form-label">Host SMTP</label><input class="form-control" id="out-host" name="out_host" type="text" value="<?php echo htmlspecialchars($config['out_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-2"><label class="form-label">Puerto</label><input class="form-control" id="out-port" name="out_port" type="number" value="<?php echo htmlspecialchars((string) ($config['out_port'] ?? 587), ENT_QUOTES, 'UTF-8'); ?>"></div>
<div class="col-md-3"><label class="form-label">Seguridad</label><?php $os = $config['out_security'] ?? 'tls'; ?><select class="form-select" id="out-security" name="out_security"><option value="tls" <?php echo $os === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="ssl" <?php echo $os === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="none" <?php echo $os === 'none' ? 'selected' : ''; ?>>None</option></select></div>
<div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Guardar</button></div>
</form></div></div>
</div><?php include('partials/footer.php'); ?></div>
</div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
<script>
document.getElementById('preset-gmail-out')?.addEventListener('click', function(){document.getElementById('out-host').value='smtp.gmail.com';document.getElementById('out-port').value='587';document.getElementById('out-security').value='tls';});
</script>
</body></html>
