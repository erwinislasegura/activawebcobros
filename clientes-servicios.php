<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            servicio_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_cliente_servicio (cliente_id, servicio_id),
            INDEX idx_clientes_servicios_cliente (cliente_id),
            INDEX idx_clientes_servicios_servicio (servicio_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios por cliente.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios por cliente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM clientes_servicios WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('clientes-servicios.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar la asignación.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar la asignación.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $clienteId = (int) ($_POST['cliente_id'] ?? 0);
    $servicioId = (int) ($_POST['servicio_id'] ?? 0);

    if ($clienteId <= 0) {
        $errors[] = 'Selecciona un cliente válido.';
    }
    if ($servicioId <= 0) {
        $errors[] = 'Selecciona un servicio válido.';
    }

    if (empty($errors)) {
        try {
            $stmt = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id) VALUES (?, ?)');
            $stmt->execute([$clienteId, $servicioId]);
            redirect('clientes-servicios.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo asignar el servicio al cliente.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo asignar el servicio al cliente.';
        }
    }
}

$clientes = [];
$servicios = [];
$asignaciones = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre FROM clientes WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $servicios = db()->query('SELECT id, nombre, monto FROM servicios WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $asignaciones = db()->query(
        'SELECT cs.id,
                c.codigo AS cliente_codigo,
                c.nombre AS cliente,
                s.nombre AS servicio,
                s.monto AS monto,
                cs.created_at
         FROM clientes_servicios cs
         JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar las asignaciones.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar las asignaciones.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Servicios por cliente"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Clientes"; $title = "Servicios por cliente"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Servicio asignado correctamente.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Asignar servicio</h5>
                                <p class="text-muted mb-0">Relaciona servicios activos a clientes para facilitar los cobros.</p>
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
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="cliente-servicio">Cliente</label>
                                            <select id="cliente-servicio" name="cliente_id" class="form-select" required>
                                                <option value="">Selecciona un cliente</option>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                    <option value="<?php echo (int) $cliente['id']; ?>">
                                                        <?php echo htmlspecialchars(($cliente['codigo'] ?? '') . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($clientes)) : ?>
                                                <small class="text-muted d-block mt-2">Primero registra clientes activos.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="servicio-cliente">Servicio</label>
                                            <select id="servicio-cliente" name="servicio_id" class="form-select" required>
                                                <option value="">Selecciona un servicio</option>
                                                <?php foreach ($servicios as $servicio) : ?>
                                                    <option value="<?php echo (int) $servicio['id']; ?>">
                                                        <?php echo htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($servicios)) : ?>
                                                <small class="text-muted d-block mt-2">Primero registra servicios activos.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100" <?php echo empty($clientes) || empty($servicios) ? 'disabled' : ''; ?>>
                                                Asignar servicio
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Servicios asignados</h5>
                                    <p class="text-muted mb-0">Listado de asignaciones por cliente.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($asignaciones); ?> asignaciones</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th>Monto</th>
                                                <th>Creación</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($asignaciones)) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay asignaciones registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($asignaciones as $asignacion) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars(($asignacion['cliente_codigo'] ?? '') . ' - ' . $asignacion['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($asignacion['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>$<?php echo number_format((float) $asignacion['monto'], 2, ',', '.'); ?></td>
                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($asignacion['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <form method="post" class="d-inline" data-confirm="¿Eliminar la asignación?">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $asignacion['id']; ?>">
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
