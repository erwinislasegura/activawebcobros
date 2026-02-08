<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$autoridad = null;

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

if ($id > 0) {
    $stmt = db()->prepare(
        'SELECT a.*, g.nombre AS grupo
         FROM authorities a
         LEFT JOIN authority_groups g ON g.id = a.group_id
         WHERE a.id = ?'
    );
    $stmt->execute([$id]);
    $autoridad = $stmt->fetch();
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Detalle de autoridad"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Autoridades"; $title = "Detalle de autoridad"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if (!$autoridad) : ?>
                                    <div class="alert alert-warning">Autoridad no encontrada.</div>
                                <?php else : ?>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Nombre</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($autoridad['nombre'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Tipo</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($autoridad['tipo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Grupo</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($autoridad['grupo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Periodo</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($autoridad['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($autoridad['fecha_fin'] ?? 'Vigente', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Contacto</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($autoridad['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h6 class="mb-3">Adjuntos</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item text-muted">Adjuntos pendientes de carga.</li>
                                        </ul>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <a href="autoridades-editar.php?id=<?php echo (int) $autoridad['id']; ?>" class="btn btn-primary">Editar autoridad</a>
                                        <a href="autoridades-lista.php" class="btn btn-link">Volver al listado</a>
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
