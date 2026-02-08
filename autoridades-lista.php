<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS authority_groups (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(120) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY authority_groups_nombre_unique (nombre)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($_POST['action'] === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM authorities WHERE id = ?');
            $stmt->execute([$id]);
            redirect('autoridades-lista.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar la autoridad. Verifica dependencias asociadas.';
        }
    }
}

$autoridades = db()->query(
    'SELECT a.id, a.nombre, a.tipo, a.fecha_inicio, a.fecha_fin, a.correo, a.estado,
            g.nombre AS grupo
     FROM authorities a
     LEFT JOIN authority_groups g ON g.id = a.group_id
     ORDER BY a.fecha_inicio DESC'
)->fetchAll();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listar autoridades"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Autoridades"; $title = "Listar autoridades"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Autoridades</h5>
                                    <p class="text-muted mb-0">Registro de autoridades municipales.</p>
                                </div>
                                <a href="autoridades-editar.php" class="btn btn-primary">Nueva autoridad</a>
                            </div>
                            <div class="card-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <select class="form-select">
                                        <option value="">Estado</option>
                                        <option>Vigente</option>
                                        <option>Histórico</option>
                                    </select>
                                    <select class="form-select">
                                        <option value="">Tipo</option>
                                        <option>Alcalde</option>
                                        <option>Concejal</option>
                                        <option>Administrador Municipal</option>
                                    </select>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Autoridad</th>
                                                <th>Tipo</th>
                                                <th>Grupo</th>
                                                <th>Periodo</th>
                                                <th>Contacto</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($autoridades)) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No hay autoridades registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($autoridades as $autoridad) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($autoridad['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($autoridad['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($autoridad['grupo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($autoridad['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($autoridad['fecha_fin'] ?? 'Vigente', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($autoridad['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="autoridades-detalle.php?id=<?php echo (int) $autoridad['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="autoridades-editar.php?id=<?php echo (int) $autoridad['id']; ?>">Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar esta autoridad?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $autoridad['id']; ?>">
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
