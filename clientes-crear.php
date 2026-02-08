<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(20) NOT NULL,
            nombre VARCHAR(150) NOT NULL,
            correo VARCHAR(150) NULL,
            telefono VARCHAR(50) NULL,
            direccion VARCHAR(180) NULL,
            sitio_web VARCHAR(180) NULL,
            color_hex VARCHAR(10) NOT NULL,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_clientes_codigo (codigo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de clientes.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de clientes.';
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
    ensure_column('clientes', 'sitio_web', 'VARCHAR(180) NULL');
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de clientes.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de clientes.';
}

function generar_codigo_cliente(): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $codigo = '';
    for ($i = 0; $i < 7; $i++) {
        $codigo .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $codigo;
}

function color_por_codigo(string $codigo): string
{
    return '#' . substr(md5($codigo), 0, 6);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM clientes WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('clientes-crear.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el cliente.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar el cliente.';
        }
    }
}

$clienteEdit = null;
if (isset($_GET['id'])) {
    $editId = (int) $_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = db()->prepare('SELECT id, nombre, correo, telefono, direccion, sitio_web, estado FROM clientes WHERE id = ?');
            $stmt->execute([$editId]);
            $clienteEdit = $stmt->fetch() ?: null;
        } catch (Exception $e) {
        } catch (Error $e) {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $sitioWeb = trim($_POST['sitio_web'] ?? '');
    $estado = isset($_POST['estado']) && $_POST['estado'] === '0' ? 0 : 1;

    if ($nombre === '') {
        $errors[] = 'El nombre del cliente es obligatorio.';
    }

    if (empty($errors)) {
        try {
            if ($clienteEdit && isset($clienteEdit['id'])) {
                $stmt = db()->prepare('UPDATE clientes SET nombre = ?, correo = ?, telefono = ?, direccion = ?, sitio_web = ?, estado = ? WHERE id = ?');
                $stmt->execute([
                    $nombre,
                    $correo !== '' ? $correo : null,
                    $telefono !== '' ? $telefono : null,
                    $direccion !== '' ? $direccion : null,
                    $sitioWeb !== '' ? $sitioWeb : null,
                    $estado,
                    (int) $clienteEdit['id'],
                ]);
            } else {
                $codigo = generar_codigo_cliente();
                $stmtCheck = db()->prepare('SELECT COUNT(*) FROM clientes WHERE codigo = ?');
                while (true) {
                    $stmtCheck->execute([$codigo]);
                    if ((int) $stmtCheck->fetchColumn() === 0) {
                        break;
                    }
                    $codigo = generar_codigo_cliente();
                }

                $colorHex = color_por_codigo($codigo);
                $stmt = db()->prepare('INSERT INTO clientes (codigo, nombre, correo, telefono, direccion, sitio_web, color_hex, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([
                    $codigo,
                    $nombre,
                    $correo !== '' ? $correo : null,
                    $telefono !== '' ? $telefono : null,
                    $direccion !== '' ? $direccion : null,
                    $sitioWeb !== '' ? $sitioWeb : null,
                    $colorHex,
                    $estado,
                ]);
            }
            redirect('clientes-crear.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo guardar el cliente.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo guardar el cliente.';
        }
    }
}

$clientes = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre, correo, telefono, direccion, sitio_web, color_hex, estado, created_at FROM clientes ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los clientes.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los clientes.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Crear cliente"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Clientes"; $title = "Crear cliente"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Cliente guardado correctamente.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Nuevo cliente</h5>
                                <p class="text-muted mb-0">Se asigna un código y color automáticamente.</p>
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
                                        <label class="form-label" for="cliente-nombre">Nombre</label>
                                        <input type="text" id="cliente-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($clienteEdit['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cliente-correo">Correo</label>
                                        <input type="email" id="cliente-correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($clienteEdit['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cliente-telefono">Teléfono</label>
                                        <input type="text" id="cliente-telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($clienteEdit['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cliente-direccion">Dirección</label>
                                        <input type="text" id="cliente-direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($clienteEdit['direccion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cliente-sitio-web">Sitio web</label>
                                        <input type="url" id="cliente-sitio-web" name="sitio_web" class="form-control" placeholder="https://" value="<?php echo htmlspecialchars($clienteEdit['sitio_web'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cliente-estado">Estado</label>
                                        <select id="cliente-estado" name="estado" class="form-select">
                                            <option value="1" <?php echo ($clienteEdit['estado'] ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                                            <option value="0" <?php echo isset($clienteEdit['estado']) && (int) $clienteEdit['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><?php echo $clienteEdit ? 'Actualizar cliente' : 'Guardar cliente'; ?></button>
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
                                    <h5 class="card-title mb-0">Clientes registrados</h5>
                                    <p class="text-muted mb-0">Listado con código y color asignado.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($clientes); ?> clientes</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Cliente</th>
                                                <th>Contacto</th>
                                                <th>Sitio web</th>
                                                <th>Color</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($clientes)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Aún no hay clientes registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($cliente['codigo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <div><?php echo htmlspecialchars($cliente['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($cliente['telefono'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($cliente['sitio_web'])) : ?>
                                                                <a href="<?php echo htmlspecialchars($cliente['sitio_web'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                                                    <?php echo htmlspecialchars($cliente['sitio_web'], ENT_QUOTES, 'UTF-8'); ?>
                                                                </a>
                                                            <?php else : ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge" style="background-color: <?php echo htmlspecialchars($cliente['color_hex'], ENT_QUOTES, 'UTF-8'); ?>;">
                                                                <?php echo htmlspecialchars($cliente['color_hex'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ((int) $cliente['estado'] === 1) : ?>
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
                                                                    <li><a class="dropdown-item" href="clientes-crear.php?id=<?php echo (int) $cliente['id']; ?>">Ver/Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este cliente?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $cliente['id']; ?>">
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
