<?php
require __DIR__ . '/app/bootstrap.php';

$modules = [
    ['key' => 'usuarios', 'label' => 'Usuarios', 'permisos' => ['view', 'create', 'edit', 'delete']],
    ['key' => 'roles', 'label' => 'Roles', 'permisos' => ['view', 'create', 'edit', 'delete']],
    ['key' => 'eventos', 'label' => 'Eventos', 'permisos' => ['view', 'create', 'edit', 'delete', 'publish']],
    ['key' => 'autoridades', 'label' => 'Autoridades', 'permisos' => ['view', 'create', 'edit', 'delete', 'export']],
    ['key' => 'mantenedores', 'label' => 'Mantenedores', 'permisos' => ['view', 'edit']],
    ['key' => 'adjuntos', 'label' => 'Adjuntos', 'permisos' => ['view', 'create', 'delete']],
];

$permisosDisponibles = [
    'view' => 'Ver',
    'create' => 'Crear',
    'edit' => 'Editar',
    'delete' => 'Eliminar',
    'publish' => 'Publicar',
    'export' => 'Exportar',
];

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS permissions (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            modulo VARCHAR(60) NOT NULL,
            accion VARCHAR(30) NOT NULL,
            descripcion VARCHAR(200) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY permissions_modulo_accion_unique (modulo, accion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INT UNSIGNED NOT NULL,
            permission_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (role_id, permission_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

try {
    $stmtPermission = db()->prepare(
        'INSERT INTO permissions (modulo, accion, descripcion)
         VALUES (:modulo, :accion, :descripcion)
         ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion)'
    );
    foreach ($modules as $module) {
        foreach ($module['permisos'] as $permisoKey) {
            $stmtPermission->execute([
                'modulo' => $module['key'],
                'accion' => $permisoKey,
                'descripcion' => $module['label'] . ' - ' . ($permisosDisponibles[$permisoKey] ?? $permisoKey),
            ]);
        }
    }
} catch (Exception $e) {
} catch (Error $e) {
}

$permissions = [];
try {
    $permissions = db()->query('SELECT id, modulo, accion FROM permissions')->fetchAll();
} catch (Exception $e) {
} catch (Error $e) {
}

$permissionMap = [];
foreach ($permissions as $permission) {
    $permissionMap[$permission['modulo']][$permission['accion']] = (int) $permission['id'];
}

$roles = db()->query('SELECT id, nombre FROM roles ORDER BY nombre')->fetchAll();
$selectedRoleId = isset($_GET['rol_id']) ? (int) $_GET['rol_id'] : 0;

if ($selectedRoleId === 0 && !empty($roles)) {
    $selectedRoleId = (int) $roles[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $selectedRoleId = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 0;
    $permisosSeleccionados = $_POST['permisos'] ?? [];

    if ($selectedRoleId > 0) {
        $stmtDelete = db()->prepare('DELETE FROM role_permissions WHERE role_id = ?');
        $stmtDelete->execute([$selectedRoleId]);

        if (!empty($permisosSeleccionados)) {
            $stmtInsert = db()->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)');
            foreach ($modules as $module) {
                $moduleKey = $module['key'];
                if (!isset($permisosSeleccionados[$moduleKey]) || !is_array($permisosSeleccionados[$moduleKey])) {
                    continue;
                }
                foreach ($permisosSeleccionados[$moduleKey] as $permisoKey => $valor) {
                    if ($valor !== '1') {
                        continue;
                    }
                    $permissionId = $permissionMap[$moduleKey][$permisoKey] ?? null;
                    if (!$permissionId) {
                        continue;
                    }
                    $stmtInsert->execute([$selectedRoleId, $permissionId]);
                }
            }
        }
    }

    redirect('roles-permisos.php?rol_id=' . $selectedRoleId . '&updated=1');
}

$permisosRol = [];
if ($selectedRoleId > 0) {
    $stmtPerms = db()->prepare('SELECT p.modulo, p.accion FROM role_permissions rp INNER JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = ?');
    $stmtPerms->execute([$selectedRoleId]);
    foreach ($stmtPerms->fetchAll() as $permiso) {
        $permisosRol[$permiso['modulo']][$permiso['accion']] = true;
    }
}

$updated = isset($_GET['updated']) && $_GET['updated'] === '1';
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Matriz de permisos"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Roles y Permisos"; $title = "Matriz de permisos"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-body">
                                <?php if ($updated) : ?>
                                    <div class="alert alert-success">Los permisos se actualizaron correctamente.</div>
                                <?php endif; ?>
                                <?php if (empty($roles)) : ?>
                                    <div class="alert alert-warning">No hay roles creados. Debes crear un rol antes de asignar permisos.</div>
                                <?php else : ?>
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                        <form method="get" class="d-flex flex-wrap align-items-center gap-2">
                                            <label class="form-label mb-0" for="rol-permisos">Rol</label>
                                            <select class="form-select w-auto" id="rol-permisos" name="rol_id" onchange="this.form.submit()">
                                                <?php foreach ($roles as $rol) : ?>
                                                    <?php $selected = $selectedRoleId === (int) $rol['id'] ? 'selected' : ''; ?>
                                                    <option value="<?php echo (int) $rol['id']; ?>" <?php echo $selected; ?>>
                                                        <?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <noscript>
                                                <button class="btn btn-outline-secondary" type="submit">Cargar rol</button>
                                            </noscript>
                                        </form>
                                        <button class="btn btn-primary" type="submit" form="permisos-form">Guardar cambios</button>
                                    </div>
                                    <form id="permisos-form" method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="role_id" value="<?php echo (int) $selectedRoleId; ?>">
                                        <div class="row g-3">
                                            <?php foreach ($modules as $module) : ?>
                                                <?php $moduleKey = $module['key']; ?>
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="card h-100 border">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <h6 class="mb-0"><?php echo htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                                <span class="badge text-bg-light"><?php echo htmlspecialchars($moduleKey, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </div>
                                                            <p class="text-muted small mb-3">Define qué acciones puede ejecutar este rol en el módulo.</p>
                                                            <div class="d-flex flex-column gap-2">
                                                                <?php foreach ($module['permisos'] as $permisoKey) : ?>
                                                                    <?php $permisoLabel = $permisosDisponibles[$permisoKey] ?? $permisoKey; ?>
                                                                    <?php $checked = !empty($permisosRol[$moduleKey][$permisoKey]); ?>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" role="switch" id="perm-<?php echo htmlspecialchars($moduleKey . '-' . $permisoKey, ENT_QUOTES, 'UTF-8'); ?>" name="permisos[<?php echo htmlspecialchars($moduleKey, ENT_QUOTES, 'UTF-8'); ?>][<?php echo htmlspecialchars($permisoKey, ENT_QUOTES, 'UTF-8'); ?>]" value="1" <?php echo $checked ? 'checked' : ''; ?>>
                                                                        <label class="form-check-label" for="perm-<?php echo htmlspecialchars($moduleKey . '-' . $permisoKey, ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <?php echo htmlspecialchars($permisoLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                                        </label>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </form>
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
