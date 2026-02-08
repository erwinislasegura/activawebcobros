<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$usuario = null;
$rolesUsuario = [];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();

    $stmtRoles = db()->prepare('SELECT r.nombre FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = ?');
    $stmtRoles->execute([$id]);
    $rolesUsuario = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Detalle de usuario"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Usuarios"; $title = "Detalle de usuario"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if (!$usuario) : ?>
                                    <div class="alert alert-warning">Usuario no encontrado.</div>
                                <?php else : ?>
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted">Nombre completo</label>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido']), ENT_QUOTES, 'UTF-8'); ?></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted">RUT</label>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['rut'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted">Correo</label>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted">Teléfono</label>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['telefono'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted">Estado</label>
                                                    <div>
                                                        <?php if ((int) $usuario['estado'] === 1) : ?>
                                                            <span class="badge text-bg-success">Habilitado</span>
                                                        <?php else : ?>
                                                            <span class="badge text-bg-secondary">Deshabilitado</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted">Último acceso</label>
                                                    <div class="fw-semibold"><?php echo $usuario['ultimo_acceso'] ? htmlspecialchars($usuario['ultimo_acceso'], ENT_QUOTES, 'UTF-8') : '-'; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label text-muted">Roles</label>
                                            <ul class="list-group">
                                                <?php if (empty($rolesUsuario)) : ?>
                                                    <li class="list-group-item text-muted">Sin roles asignados.</li>
                                                <?php else : ?>
                                                    <?php foreach ($rolesUsuario as $rol) : ?>
                                                        <li class="list-group-item"><?php echo htmlspecialchars($rol, ENT_QUOTES, 'UTF-8'); ?></li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <a href="usuarios-editar.php?id=<?php echo (int) $usuario['id']; ?>" class="btn btn-primary">Editar usuario</a>
                                        <a href="usuarios-asignar-roles.php?id=<?php echo (int) $usuario['id']; ?>" class="btn btn-outline-secondary">Asignar roles</a>
                                        <a href="usuarios-lista.php" class="btn btn-link">Volver al listado</a>
                                    </div>
                                <?php endif; ?>
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
