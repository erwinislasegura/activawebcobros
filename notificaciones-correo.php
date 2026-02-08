<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$stmt = db()->query('SELECT * FROM notificacion_correos LIMIT 1');
$correoConfig = $stmt->fetch() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $correoImap = trim($_POST['correo_imap'] ?? '');
    $passwordImap = trim($_POST['password_imap'] ?? '');
    $hostImap = trim($_POST['host_imap'] ?? '');
    $puertoImap = (int) ($_POST['puerto_imap'] ?? 993);
    $seguridadImap = trim($_POST['seguridad_imap'] ?? 'ssl');
    $fromNombre = trim($_POST['from_nombre'] ?? '');
    $fromCorreo = trim($_POST['from_correo'] ?? '');

    if ($correoImap === '' || $passwordImap === '' || $hostImap === '') {
        $errors[] = 'Completa los campos obligatorios de correo IMAP.';
    }

    if (empty($errors)) {
        $stmt = db()->query('SELECT id FROM notificacion_correos LIMIT 1');
        $id = $stmt->fetchColumn();

        if ($id) {
            $stmtUpdate = db()->prepare('UPDATE notificacion_correos SET correo_imap = ?, password_imap = ?, host_imap = ?, puerto_imap = ?, seguridad_imap = ?, from_nombre = ?, from_correo = ? WHERE id = ?');
            $stmtUpdate->execute([
                $correoImap,
                $passwordImap,
                $hostImap,
                $puertoImap,
                $seguridadImap,
                $fromNombre !== '' ? $fromNombre : null,
                $fromCorreo !== '' ? $fromCorreo : null,
                $id,
            ]);
        } else {
            $stmtInsert = db()->prepare('INSERT INTO notificacion_correos (correo_imap, password_imap, host_imap, puerto_imap, seguridad_imap, from_nombre, from_correo) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmtInsert->execute([
                $correoImap,
                $passwordImap,
                $hostImap,
                $puertoImap,
                $seguridadImap,
                $fromNombre !== '' ? $fromNombre : null,
                $fromCorreo !== '' ? $fromCorreo : null,
            ]);
        }

        redirect('notificaciones-correo.php');
    }

    $correoConfig = [
        'correo_imap' => $correoImap,
        'password_imap' => $passwordImap,
        'host_imap' => $hostImap,
        'puerto_imap' => $puertoImap,
        'seguridad_imap' => $seguridadImap,
        'from_nombre' => $fromNombre,
        'from_correo' => $fromCorreo,
    ];
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Correo de envío"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Mantenedores"; $title = "Correo de envío"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Correo IMAP de envío</h5>
                                    <p class="text-muted mb-0">Configura la casilla que enviará los avisos desde el sistema.</p>
                                </div>
                                <button type="submit" form="correo-notificaciones-form" class="btn btn-primary">Guardar configuración</button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <form id="correo-notificaciones-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="correo-imap">Correo IMAP</label>
                                            <input type="email" id="correo-imap" name="correo_imap" class="form-control" value="<?php echo htmlspecialchars($correoConfig['correo_imap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="password-imap">Password IMAP</label>
                                            <input type="password" id="password-imap" name="password_imap" class="form-control" value="<?php echo htmlspecialchars($correoConfig['password_imap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="host-imap">Host IMAP</label>
                                            <input type="text" id="host-imap" name="host_imap" class="form-control" value="<?php echo htmlspecialchars($correoConfig['host_imap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label" for="puerto-imap">Puerto</label>
                                            <input type="number" id="puerto-imap" name="puerto_imap" class="form-control" value="<?php echo htmlspecialchars((string) ($correoConfig['puerto_imap'] ?? 993), ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label" for="seguridad-imap">Seguridad</label>
                                            <select id="seguridad-imap" name="seguridad_imap" class="form-select">
                                                <?php $seguridad = $correoConfig['seguridad_imap'] ?? 'ssl'; ?>
                                                <option value="ssl" <?php echo $seguridad === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                                <option value="tls" <?php echo $seguridad === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="from-nombre">Nombre remitente</label>
                                            <input type="text" id="from-nombre" name="from_nombre" class="form-control" value="<?php echo htmlspecialchars($correoConfig['from_nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="from-correo">Correo remitente</label>
                                            <input type="email" id="from-correo" name="from_correo" class="form-control" value="<?php echo htmlspecialchars($correoConfig['from_correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
