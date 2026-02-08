<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $monto = trim($_POST['monto'] ?? '');
    $estado = isset($_POST['estado']) && $_POST['estado'] === '0' ? 0 : 1;

    if ($nombre === '') {
        $errors[] = 'El nombre del servicio es obligatorio.';
    }

    if ($monto === '' || !is_numeric($monto) || (float) $monto < 0) {
        $errors[] = 'Ingresa un monto válido para el servicio.';
    }

    if (empty($errors)) {
        try {
            $stmt = db()->prepare('INSERT INTO servicios (nombre, descripcion, monto, estado) VALUES (?, ?, ?, ?)');
            $stmt->execute([
                $nombre,
                $descripcion !== '' ? $descripcion : null,
                $monto,
                $estado,
            ]);
            redirect('cobros-servicios-agregar.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo guardar el servicio. Verifica la base de datos.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo guardar el servicio. Verifica la base de datos.';
        }
    }
}

$servicios = [];
try {
    $servicios = db()->query('SELECT id, nombre, descripcion, monto, estado, created_at FROM servicios ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los servicios.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los servicios.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Agregar servicio"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Cobros de servicios"; $title = "Agregar servicio"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Servicio guardado correctamente.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Nuevo servicio</h5>
                                <p class="text-muted mb-0">Registra servicios disponibles para cobros.</p>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-nombre">Nombre del servicio</label>
                                        <input type="text" id="servicio-nombre" name="nombre" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-descripcion">Descripción</label>
                                        <textarea id="servicio-descripcion" name="descripcion" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-monto">Monto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" id="servicio-monto" name="monto" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-estado">Estado</label>
                                        <select id="servicio-estado" name="estado" class="form-select">
                                            <option value="1" selected>Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Guardar servicio</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Servicios registrados</h5>
                                    <p class="text-muted mb-0">Listado de servicios disponibles para cobro.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($servicios); ?> servicios</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Servicio</th>
                                                <th>Descripción</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Creación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($servicios)) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Aún no hay servicios registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($servicios as $servicio) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($servicio['descripcion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>$<?php echo number_format((float) $servicio['monto'], 2, ',', '.'); ?></td>
                                                        <td>
                                                            <?php if ((int) $servicio['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Activo</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Inactivo</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($servicio['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8'); ?></td>
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
