<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$autoridad = null;
$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

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

$groups = db()->query('SELECT id, nombre FROM authority_groups ORDER BY nombre')->fetchAll();
$groupOptions = [];
foreach ($groups as $group) {
    $groupOptions[(int) $group['id']] = $group['nombre'];
}

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM authorities WHERE id = ?');
    $stmt->execute([$id]);
    $autoridad = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM authorities WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('autoridades-editar.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar la autoridad. Verifica dependencias asociadas.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? null;
    $estado = isset($_POST['estado']) && $_POST['estado'] === '0' ? 0 : 1;
    $groupId = isset($_POST['group_id']) && $_POST['group_id'] !== '' ? (int) $_POST['group_id'] : null;
    $tipo = $groupId && isset($groupOptions[$groupId]) ? $groupOptions[$groupId] : 'Sin grupo';

    if ($nombre === '' || $fechaInicio === '') {
        $errors[] = 'Completa los campos obligatorios.';
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE authorities SET nombre = ?, tipo = ?, correo = ?, telefono = ?, fecha_inicio = ?, fecha_fin = ?, estado = ?, group_id = ? WHERE id = ?');
            $stmt->execute([
                $nombre,
                $tipo,
                $correo !== '' ? $correo : null,
                $telefono !== '' ? $telefono : null,
                $fechaInicio,
                $fechaFin !== '' ? $fechaFin : null,
                $estado,
                $groupId,
                $id,
            ]);
            redirect('autoridades-editar.php?id=' . $id . '&success=1');
        } else {
            $stmt = db()->prepare('INSERT INTO authorities (nombre, tipo, correo, telefono, fecha_inicio, fecha_fin, estado, group_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $nombre,
                $tipo,
                $correo !== '' ? $correo : null,
                $telefono !== '' ? $telefono : null,
                $fechaInicio,
                $fechaFin !== '' ? $fechaFin : null,
                $estado,
                $groupId,
            ]);
            $newId = (int) db()->lastInsertId();
            redirect('autoridades-editar.php?id=' . $newId . '&success=1');
        }
    }
}

$autoridades = db()->query(
    'SELECT a.id, a.nombre, a.fecha_inicio, a.fecha_fin, a.correo, a.estado,
            g.nombre AS grupo, g.id AS grupo_id
     FROM authorities a
     LEFT JOIN authority_groups g ON g.id = a.group_id
     ORDER BY a.fecha_inicio DESC'
)->fetchAll();

$groupPalette = [
    'bg-primary-subtle text-primary',
    'bg-success-subtle text-success',
    'bg-warning-subtle text-warning',
    'bg-info-subtle text-info',
    'bg-danger-subtle text-danger',
    'bg-secondary-subtle text-secondary',
];

function group_badge_class(?int $groupId, array $palette): string
{
    if (!$groupId) {
        return 'bg-light text-muted';
    }
    $index = $groupId % count($palette);
    return $palette[$index];
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Crear/editar autoridad"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Autoridades"; $title = "Crear/editar autoridad"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($success === '1') : ?>
                                    <div class="alert alert-success">Autoridad guardada correctamente.</div>
                                <?php endif; ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="autoridad-nombre">Nombre completo</label>
                                            <input type="text" id="autoridad-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($autoridad['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="autoridad-correo">Correo</label>
                                        <input type="email" id="autoridad-correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($autoridad['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="autoridad-telefono">Teléfono</label>
                                            <input type="tel" id="autoridad-telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($autoridad['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                        <label class="form-label" for="autoridad-grupo">Grupo o tipo de autoridad</label>
                                        <select id="autoridad-grupo" name="group_id" class="form-select">
                                            <option value="">Sin grupo</option>
                                            <?php $grupoActual = $autoridad['group_id'] ?? null; ?>
                                            <?php foreach ($groups as $group) : ?>
                                                    <option value="<?php echo (int) $group['id']; ?>" <?php echo (int) $grupoActual === (int) $group['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($group['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="autoridad-inicio">Fecha inicio</label>
                                            <input type="date" id="autoridad-inicio" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($autoridad['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="autoridad-fin">Fecha fin</label>
                                            <input type="date" id="autoridad-fin" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($autoridad['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="autoridad-estado">Estado</label>
                                            <select id="autoridad-estado" name="estado" class="form-select">
                                                <option value="1" <?php echo !$autoridad || (int) ($autoridad['estado'] ?? 1) === 1 ? 'selected' : ''; ?>>Habilitado</option>
                                                <option value="0" <?php echo $autoridad && (int) $autoridad['estado'] === 0 ? 'selected' : ''; ?>>Deshabilitado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <button type="submit" class="btn btn-primary">Guardar autoridad</button>
                                        <a href="autoridades-lista.php" class="btn btn-outline-secondary">Volver</a>
                                        <?php if ($autoridad) : ?>
                                            <form method="post" class="ms-auto" onsubmit="return confirm('¿Seguro que deseas eliminar esta autoridad?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo (int) $autoridad['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Listado de autoridades</h5>
                                    <p class="text-muted mb-0">Autoridades registradas con su grupo/tipo asociado.</p>
                                </div>
                                <a href="autoridades-editar.php" class="btn btn-outline-primary">Nueva autoridad</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Autoridad</th>
                                                <th>Grupo / Tipo</th>
                                                <th>Periodo</th>
                                                <th>Contacto</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($autoridades)) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay autoridades registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($autoridades as $autoridadItem) : ?>
                                                    <?php
                                                    $badgeClass = group_badge_class(isset($autoridadItem['grupo_id']) ? (int) $autoridadItem['grupo_id'] : null, $groupPalette);
                                                    $grupoLabel = $autoridadItem['grupo'] ?? 'Sin grupo';
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($autoridadItem['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $badgeClass; ?>">
                                                                <?php echo htmlspecialchars($grupoLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($autoridadItem['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($autoridadItem['fecha_fin'] ?? 'Vigente', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($autoridadItem['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <a class="btn btn-sm btn-outline-primary" href="autoridades-editar.php?id=<?php echo (int) $autoridadItem['id']; ?>">Editar</a>
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
