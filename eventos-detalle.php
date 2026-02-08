<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$evento = null;
$validationLink = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $evento = $stmt->fetch();
    if ($evento) {
        $evento['validation_token'] = ensure_event_validation_token($id, $evento['validation_token'] ?? null);
        $validationLink = base_url() . '/eventos-validacion.php?token=' . urlencode($evento['validation_token']);
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Detalle de evento"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Eventos Municipales"; $title = "Detalle de evento"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if (!$evento) : ?>
                                    <div class="alert alert-warning">Evento no encontrado.</div>
                                <?php else : ?>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Título</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Estado</label>
                                            <div><span class="badge text-bg-<?php echo $evento['estado'] === 'publicado' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars(ucfirst($evento['estado']), ENT_QUOTES, 'UTF-8'); ?></span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Fecha inicio</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($evento['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Fecha fin</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($evento['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Ubicación</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted">Tipo</label>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($evento['tipo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted">Descripción</label>
                                            <div><?php echo htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted">Enlace público de validación</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($validationLink ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <a href="eventos-lista.php" class="btn btn-link">Volver al listado</a>
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
