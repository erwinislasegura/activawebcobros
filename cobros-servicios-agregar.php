<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS tipos_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(120) NOT NULL,
            descripcion TEXT NULL,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_servicio_id INT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_servicios_tipo (tipo_servicio_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios.';
}

function ensure_column(string $table, string $column, string $definition): void
{
    $dbName = $GLOBALS['config']['db']['name'] ?? '';
    if ($dbName === '') {
        return;
    }
    $stmt = db()->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$dbName, $table, $column]);
    $exists = (int) $stmt->fetchColumn() > 0;
    if (!$exists) {
        db()->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
    }
}

try {
    ensure_column('servicios', 'tipo_servicio_id', 'INT NULL');
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de servicios.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de servicios.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM servicios WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('cobros-servicios-agregar.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el servicio.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar el servicio.';
        }
    }
}

$servicioEdit = null;
if (isset($_GET['id'])) {
    $editId = (int) $_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = db()->prepare('SELECT id, tipo_servicio_id, nombre, descripcion, monto, estado FROM servicios WHERE id = ?');
            $stmt->execute([$editId]);
            $servicioEdit = $stmt->fetch() ?: null;
        } catch (Exception $e) {
        } catch (Error $e) {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $tipoServicioId = (int) ($_POST['tipo_servicio_id'] ?? 0);
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
            if ($servicioEdit && isset($servicioEdit['id'])) {
                $stmt = db()->prepare('UPDATE servicios SET tipo_servicio_id = ?, nombre = ?, descripcion = ?, monto = ?, estado = ? WHERE id = ?');
                $stmt->execute([
                    $tipoServicioId > 0 ? $tipoServicioId : null,
                    $nombre,
                    $descripcion !== '' ? $descripcion : null,
                    $monto,
                    $estado,
                    (int) $servicioEdit['id'],
                ]);
            } else {
                $stmt = db()->prepare('INSERT INTO servicios (tipo_servicio_id, nombre, descripcion, monto, estado) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([
                    $tipoServicioId > 0 ? $tipoServicioId : null,
                    $nombre,
                    $descripcion !== '' ? $descripcion : null,
                    $monto,
                    $estado,
                ]);
            }
            redirect('cobros-servicios-agregar.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo guardar el servicio. Verifica la base de datos.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo guardar el servicio. Verifica la base de datos.';
        }
    }
}

$tiposServicios = [];
$servicios = [];
try {
    $tiposServicios = db()->query('SELECT id, nombre FROM tipos_servicios WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $servicios = db()->query(
        'SELECT s.id, s.nombre, s.descripcion, s.monto, s.estado, s.created_at, t.nombre AS tipo
         FROM servicios s
         LEFT JOIN tipos_servicios t ON t.id = s.tipo_servicio_id
         ORDER BY s.id DESC'
    )->fetchAll();
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
                                        <label class="form-label" for="servicio-tipo">Tipo de servicio</label>
                                        <select id="servicio-tipo" name="tipo_servicio_id" class="form-select">
                                            <option value="">Selecciona un tipo</option>
                                            <?php foreach ($tiposServicios as $tipo) : ?>
                                                <option value="<?php echo (int) $tipo['id']; ?>" <?php echo ($servicioEdit['tipo_servicio_id'] ?? 0) == (int) $tipo['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($tipo['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (empty($tiposServicios)) : ?>
                                            <small class="text-muted d-block mt-2">Primero registra tipos en "Tipos de servicios".</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-nombre">Nombre del servicio</label>
                                        <input type="text" id="servicio-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($servicioEdit['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-descripcion">Descripción</label>
                                        <textarea id="servicio-descripcion" name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($servicioEdit['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-monto">Monto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" id="servicio-monto" name="monto" class="form-control" value="<?php echo htmlspecialchars($servicioEdit['monto'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="servicio-estado">Estado</label>
                                        <select id="servicio-estado" name="estado" class="form-select">
                                            <option value="1" <?php echo ($servicioEdit['estado'] ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                                            <option value="0" <?php echo isset($servicioEdit['estado']) && (int) $servicioEdit['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><?php echo $servicioEdit ? 'Actualizar servicio' : 'Guardar servicio'; ?></button>
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
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Creación</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($servicios)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Aún no hay servicios registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($servicios as $servicio) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($servicio['tipo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
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
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="cobros-servicios-agregar.php?id=<?php echo (int) $servicio['id']; ?>">Ver/Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este servicio?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $servicio['id']; ?>">
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
