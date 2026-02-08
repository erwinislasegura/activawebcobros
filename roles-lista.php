<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($_POST['action'] === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM roles WHERE id = ?');
            $stmt->execute([$id]);
            redirect('roles-lista.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el rol. Verifica dependencias asociadas.';
        }
    }
}

$roles = db()->query('SELECT id, nombre, descripcion, estado FROM roles ORDER BY nombre')->fetchAll();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listar roles"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Roles y Permisos"; $title = "Listar roles"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Roles</h5>
                                    <p class="text-muted mb-0">Configuración de roles y permisos.</p>
                                </div>
                                <a href="roles-editar.php" class="btn btn-primary">Nuevo rol</a>
                            </div>
                            <div class="card-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Buscar rol">
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Rol</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($roles)) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No hay roles registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($roles as $rol) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($rol['descripcion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $rol['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Activo</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Inactivo</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="roles-editar.php?id=<?php echo (int) $rol['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="roles-editar.php?id=<?php echo (int) $rol['id']; ?>">Editar</a></li>
                                                                    <li><a class="dropdown-item" href="roles-permisos.php?rol_id=<?php echo (int) $rol['id']; ?>">Permisos</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este rol?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $rol['id']; ?>">
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Eliminar</button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
