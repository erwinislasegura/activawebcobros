<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$usuario = null;
$errors = [];
$errorMessage = '';
$roles = db()->query('SELECT id, nombre FROM roles WHERE estado = 1 ORDER BY nombre')->fetchAll();
$rolesUsuario = [];
$success = $_GET['success'] ?? '';
$usuarios = db()->query('SELECT id, rut, nombre, apellido, correo, rol, estado, ultimo_acceso, avatar_path FROM users ORDER BY id DESC')->fetchAll();

function handle_avatar_upload(array $file, array &$errors): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errors[] = 'No se pudo subir la foto del usuario.';
        return null;
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'El archivo de la foto no es una imagen válida.';
        return null;
    }

    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mimeType = $imageInfo['mime'] ?? '';
    if (!isset($allowedTypes[$mimeType])) {
        $errors[] = 'La foto debe ser JPG, PNG o WEBP.';
        return null;
    }

    $uploadDir = __DIR__ . '/uploads/avatars';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        $errors[] = 'No se pudo crear la carpeta de avatares.';
        return null;
    }

    $filename = sprintf('avatar_%s.%s', bin2hex(random_bytes(8)), $allowedTypes[$mimeType]);
    $targetPath = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $errors[] = 'No se pudo guardar la foto del usuario.';
        return null;
    }

    return 'uploads/avatars/' . $filename;
}

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();

    $stmtRoles = db()->prepare('SELECT role_id FROM user_roles WHERE user_id = ?');
    $stmtRoles->execute([$id]);
    $rolesUsuario = array_map('intval', $stmtRoles->fetchAll(PDO::FETCH_COLUMN));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('usuarios-editar.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el usuario. Verifica dependencias asociadas.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null) && $id > 0) {
    $rut = trim($_POST['rut'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $estado = isset($_POST['estado']) && $_POST['estado'] === '0' ? 0 : 1;
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $rolesSeleccionados = array_map('intval', $_POST['roles'] ?? []);
    $avatarPath = handle_avatar_upload($_FILES['avatar'] ?? [], $errors);

    if ($rut === '' || $nombre === '' || $apellido === '' || $correo === '' || $telefono === '' || $username === '') {
        $errors[] = 'Completa todos los campos obligatorios.';
    }

    if ($password !== '' && $password !== $passwordConfirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        $rolNombre = '';
        if (!empty($rolesSeleccionados)) {
            $rolNombreStmt = db()->prepare('SELECT nombre FROM roles WHERE id = ?');
            $rolNombreStmt->execute([$rolesSeleccionados[0]]);
            $rolNombre = (string) ($rolNombreStmt->fetchColumn() ?: '');
        }

        $params = [$rut, $nombre, $apellido, $correo, $telefono, $direccion !== '' ? $direccion : null, $username, $rolNombre, $estado];
        $sql = 'UPDATE users SET rut = ?, nombre = ?, apellido = ?, correo = ?, telefono = ?, direccion = ?, username = ?, rol = ?, estado = ?';

        if ($avatarPath) {
            $sql .= ', avatar_path = ?';
            $params[] = $avatarPath;
        }

        if ($password !== '') {
            $sql .= ', password_hash = ?';
            $params[] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;
        $stmtUpdate = db()->prepare($sql);
        $stmtUpdate->execute($params);

        $stmtDelete = db()->prepare('DELETE FROM user_roles WHERE user_id = ?');
        $stmtDelete->execute([$id]);
        if (!empty($rolesSeleccionados)) {
            $insertRole = db()->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
            foreach ($rolesSeleccionados as $roleId) {
                $insertRole->execute([$id, $roleId]);
            }
        }

        redirect('usuarios-editar.php?id=' . $id . '&success=1');
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Editar usuario"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Usuarios"; $title = "Editar usuario"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if (!$usuario) : ?>
                                    <div class="alert alert-warning">Usuario no encontrado.</div>
                                <?php else : ?>
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
                                        <div class="alert alert-success">Usuario actualizado correctamente.</div>
                                    <?php endif; ?>
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="usuario-edit-rut">RUT</label>
                                            <input type="text" id="usuario-edit-rut" name="rut" class="form-control" value="<?php echo htmlspecialchars($usuario['rut'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="usuario-edit-nombre">Nombres</label>
                                            <input type="text" id="usuario-edit-nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="usuario-edit-apellido">Apellidos</label>
                                            <input type="text" id="usuario-edit-apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="usuario-edit-correo">Correo</label>
                                            <input type="email" id="usuario-edit-correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label" for="usuario-edit-telefono">Teléfono</label>
                                            <input type="tel" id="usuario-edit-telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario['telefono'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label" for="usuario-edit-estado">Estado</label>
                                            <select id="usuario-edit-estado" name="estado" class="form-select">
                                                <option value="1" <?php echo (int) $usuario['estado'] === 1 ? 'selected' : ''; ?>>Habilitado</option>
                                                <option value="0" <?php echo (int) $usuario['estado'] === 0 ? 'selected' : ''; ?>>Deshabilitado</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label" for="usuario-edit-direccion">Dirección</label>
                                            <input type="text" id="usuario-edit-direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($usuario['direccion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="usuario-edit-username">Username</label>
                                            <input type="text" id="usuario-edit-username" name="username" class="form-control" value="<?php echo htmlspecialchars($usuario['username'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="usuario-edit-password">Nueva contraseña</label>
                                            <input type="password" id="usuario-edit-password" name="password" class="form-control" placeholder="********">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="usuario-edit-password-confirm">Confirmar contraseña</label>
                                            <input type="password" id="usuario-edit-password-confirm" name="password_confirm" class="form-control" placeholder="********">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="usuario-edit-avatar">Foto de perfil</label>
                                            <input type="file" id="usuario-edit-avatar" name="avatar" class="form-control" accept="image/png,image/jpeg,image/webp">
                                            <?php if (!empty($usuario['avatar_path'])) : ?>
                                                <div class="mt-2">
                                                    <img src="<?php echo htmlspecialchars($usuario['avatar_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="avatar actual" class="rounded-circle" width="48" height="48">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Roles asignados</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            <?php if (empty($roles)) : ?>
                                                <span class="text-muted">No hay roles disponibles.</span>
                                            <?php else : ?>
                                                <?php foreach ($roles as $rol) : ?>
                                                    <?php $checked = in_array((int) $rol['id'], $rolesUsuario, true); ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="rol-edit-<?php echo (int) $rol['id']; ?>" name="roles[]" value="<?php echo (int) $rol['id']; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="rol-edit-<?php echo (int) $rol['id']; ?>"><?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8'); ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-primary">Actualizar usuario</button>
                                        <a href="usuarios-lista.php" class="btn btn-outline-secondary">Volver</a>
                                    </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Listado de usuarios</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Avatar</th>
                                                <th>RUT</th>
                                                <th>Nombre</th>
                                                <th>Correo</th>
                                                <th>Rol</th>
                                                <th>Estado</th>
                                                <th>Último acceso</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($usuarios)) : ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">No hay usuarios registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($usuarios as $usuarioItem) : ?>
                                                    <?php $avatar = $usuarioItem['avatar_path'] ?: 'assets/images/users/user-1.jpg'; ?>
                                                    <tr>
                                                        <td>
                                                            <img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="avatar" class="rounded-circle" width="40" height="40">
                                                        </td>
                                                        <td><?php echo htmlspecialchars($usuarioItem['rut'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars(trim($usuarioItem['nombre'] . ' ' . $usuarioItem['apellido']), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuarioItem['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuarioItem['rol'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $usuarioItem['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Habilitado</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Deshabilitado</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $usuarioItem['ultimo_acceso'] ? htmlspecialchars($usuarioItem['ultimo_acceso'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="usuarios-detalle.php?id=<?php echo (int) $usuarioItem['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="usuarios-editar.php?id=<?php echo (int) $usuarioItem['id']; ?>">Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este usuario?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $usuarioItem['id']; ?>">
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
