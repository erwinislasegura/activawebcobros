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
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de tipos de servicios.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de tipos de servicios.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM tipos_servicios WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('tipos-servicios.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el tipo de servicio.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar el tipo de servicio.';
        }
    }
}

$tipoEdit = null;
if (isset($_GET['id'])) {
    $editId = (int) $_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = db()->prepare('SELECT id, nombre, descripcion, estado FROM tipos_servicios WHERE id = ?');
            $stmt->execute([$editId]);
            $tipoEdit = $stmt->fetch() ?: null;
        } catch (Exception $e) {
        } catch (Error $e) {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = isset($_POST['estado']) && $_POST['estado'] === '0' ? 0 : 1;

    if ($nombre === '') {
        $errors[] = 'El nombre del tipo de servicio es obligatorio.';
    }

    if (empty($errors)) {
        try {
            if ($tipoEdit && isset($tipoEdit['id'])) {
                $stmt = db()->prepare('UPDATE tipos_servicios SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?');
                $stmt->execute([
                    $nombre,
                    $descripcion !== '' ? $descripcion : null,
                    $estado,
                    (int) $tipoEdit['id'],
                ]);
            } else {
                $stmt = db()->prepare('INSERT INTO tipos_servicios (nombre, descripcion, estado) VALUES (?, ?, ?)');
                $stmt->execute([
                    $nombre,
                    $descripcion !== '' ? $descripcion : null,
                    $estado,
                ]);
            }
            redirect('tipos-servicios.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo guardar el tipo de servicio.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo guardar el tipo de servicio.';
        }
    }
}

$tipos = [];
try {
    $tipos = db()->query('SELECT id, nombre, descripcion, estado, created_at FROM tipos_servicios ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los tipos de servicios.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los tipos de servicios.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Tipos de servicios"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Servicios"; $title = "Tipos de servicios"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Tipo de servicio guardado correctamente.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Nuevo tipo</h5>
                                <p class="text-muted mb-0">Clasifica los servicios para facilitar su gestión.</p>
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
                                        <label class="form-label" for="tipo-nombre">Nombre del tipo</label>
                                        <input type="text" id="tipo-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($tipoEdit['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="tipo-descripcion">Descripción</label>
                                        <textarea id="tipo-descripcion" name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($tipoEdit['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="tipo-estado">Estado</label>
                                        <select id="tipo-estado" name="estado" class="form-select">
                                            <option value="1" <?php echo ($tipoEdit['estado'] ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                                            <option value="0" <?php echo isset($tipoEdit['estado']) && (int) $tipoEdit['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><?php echo $tipoEdit ? 'Actualizar tipo' : 'Guardar tipo'; ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Tipos registrados</h5>
                                    <p class="text-muted mb-0">Listado de tipos disponibles.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($tipos); ?> tipos</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                                <th>Creación</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($tipos)) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Aún no hay tipos registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($tipos as $tipo) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($tipo['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($tipo['descripcion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $tipo['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Activo</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Inactivo</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($tipo['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="tipos-servicios.php?id=<?php echo (int) $tipo['id']; ?>">Ver/Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este tipo?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $tipo['id']; ?>">
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
