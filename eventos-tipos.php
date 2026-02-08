<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = $_GET['success'] ?? '';

$colorOptions = [
    'bg-primary-subtle text-primary' => 'Primario',
    'bg-secondary-subtle text-secondary' => 'Secundario',
    'bg-success-subtle text-success' => 'Éxito',
    'bg-warning-subtle text-warning' => 'Advertencia',
    'bg-danger-subtle text-danger' => 'Peligro',
    'bg-info-subtle text-info' => 'Información',
    'bg-dark-subtle text-dark' => 'Oscuro',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('DELETE FROM event_types WHERE id = ?');
            $stmt->execute([$id]);
            redirect('eventos-tipos.php?success=1');
        }
    } else {
        $nombre = trim($_POST['nombre'] ?? '');
        $colorClass = $_POST['color_class'] ?? 'bg-primary-subtle text-primary';

        if ($nombre === '') {
            $errors[] = 'El nombre del tipo es obligatorio.';
        }

        if (!array_key_exists($colorClass, $colorOptions)) {
            $errors[] = 'Selecciona un color válido.';
        }

        if (empty($errors)) {
            $stmt = db()->prepare('INSERT INTO event_types (nombre, color_class) VALUES (?, ?)');
            $stmt->execute([$nombre, $colorClass]);
            redirect('eventos-tipos.php?success=1');
        }
    }
}

$eventTypes = ensure_event_types();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Tipos de evento"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Mantenedores"; $title = "Tipos de evento"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Tipo de evento actualizado correctamente.</div>
                <?php endif; ?>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) : ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Crear tipo de evento</h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="create">

                                    <div class="mb-3">
                                        <label class="form-label" for="tipo-nombre">Nombre</label>
                                        <input type="text" id="tipo-nombre" name="nombre" class="form-control" placeholder="Ej: Feria comunitaria" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="tipo-color">Color</label>
                                        <select id="tipo-color" name="color_class" class="form-select">
                                            <?php foreach ($colorOptions as $value => $label) : ?>
                                                <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Guardar tipo</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Listado de tipos</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Color</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($eventTypes)) : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No hay tipos registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($eventTypes as $eventType) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($eventType['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo htmlspecialchars($eventType['color_class'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo htmlspecialchars($colorOptions[$eventType['color_class']] ?? 'Personalizado', ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $eventType['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                            </form>
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
