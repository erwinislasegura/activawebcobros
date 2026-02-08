<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$roles = [];
try {
    $roles = db()->query('SELECT id, nombre FROM roles WHERE estado = 1 ORDER BY nombre')->fetchAll();
} catch (Exception $e) {
    $errorMessage = 'No se pudieron cargar los roles. Verifica la base de datos.';
} catch (Error $e) {
    $errorMessage = 'No se pudieron cargar los roles. Verifica la base de datos.';
}
$success = $_GET['success'] ?? '';
$usuarios = [];
try {
    $usuarios = db()->query('SELECT id, rut, nombre, apellido, correo, rol, estado, ultimo_acceso, avatar_path FROM users ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los usuarios. Verifica la base de datos.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los usuarios. Verifica la base de datos.';
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('usuarios-crear.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el usuario. Verifica dependencias asociadas.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
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

    if ($rut === '' || $nombre === '' || $apellido === '' || $correo === '' || $telefono === '' || $username === '' || $password === '') {
        $errors[] = 'Completa todos los campos obligatorios.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        try {
            $pdo = db();
            $existingStmt = $pdo->prepare('SELECT rut, correo, username FROM users WHERE rut = ? OR correo = ? OR username = ? LIMIT 1');
            $existingStmt->execute([$rut, $correo, $username]);
            $existing = $existingStmt->fetch();

            if ($existing) {
                if ($existing['rut'] === $rut) {
                    $errors[] = 'El RUT ya está registrado.';
                }
                if ($existing['correo'] === $correo) {
                    $errors[] = 'El correo ya está registrado.';
                }
                if ($existing['username'] === $username) {
                    $errors[] = 'El username ya está registrado.';
                }
            }

            if (!empty($rolesSeleccionados)) {
                $placeholders = implode(',', array_fill(0, count($rolesSeleccionados), '?'));
                $roleCheckStmt = $pdo->prepare('SELECT id FROM roles WHERE id IN (' . $placeholders . ')');
                $roleCheckStmt->execute($rolesSeleccionados);
                $rolesValidos = $roleCheckStmt->fetchAll(PDO::FETCH_COLUMN);
                if (count($rolesValidos) !== count($rolesSeleccionados)) {
                    $errors[] = 'Selecciona roles válidos para el usuario.';
                }
            }

            if (empty($errors)) {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare('INSERT INTO users (rut, nombre, apellido, correo, telefono, direccion, username, rol, avatar_path, password_hash, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $rolNombre = '';
                if (!empty($rolesSeleccionados)) {
                    $rolNombreStmt = $pdo->prepare('SELECT nombre FROM roles WHERE id = ?');
                    $rolNombreStmt->execute([$rolesSeleccionados[0]]);
                    $rolNombre = (string) ($rolNombreStmt->fetchColumn() ?: '');
                }
                $stmt->execute([
                    $rut,
                    $nombre,
                    $apellido,
                    $correo,
                    $telefono,
                    $direccion !== '' ? $direccion : null,
                    $username,
                    $rolNombre,
                    $avatarPath,
                    password_hash($password, PASSWORD_BCRYPT),
                    $estado,
                ]);

                $userId = (int) $pdo->lastInsertId();
                if ($userId > 0 && !empty($rolesSeleccionados)) {
                    $insertRole = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
                    foreach ($rolesSeleccionados as $roleId) {
                        $insertRole->execute([$userId, $roleId]);
                    }
                }

                $pdo->commit();
                redirect('usuarios-crear.php?success=1');
            }
        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'No se pudo crear el usuario. Verifica la base de datos.';
        } catch (Error $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'No se pudo crear el usuario. Verifica la base de datos.';
        }
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Crear usuario"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Usuarios"; $title = "Crear usuario"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">Registro de usuarios</h5>
                                        <p class="text-muted mb-0">Crea usuarios con roles asignados en el mismo formulario.</p>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">Roles desde registro</span>
                                </div>
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
                                    <div class="alert alert-success">Usuario creado correctamente.</div>
                                <?php endif; ?>
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="card border shadow-none mb-3">
                                                <div class="card-body">
                                                    <h6 class="text-uppercase text-muted small mb-3">Datos personales</h6>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label" for="usuario-rut">RUT</label>
                                                            <input type="text" id="usuario-rut" name="rut" class="form-control" placeholder="12.345.678-9">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label" for="usuario-nombre">Nombres</label>
                                                            <input type="text" id="usuario-nombre" name="nombre" class="form-control" placeholder="Nombre">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label" for="usuario-apellido">Apellidos</label>
                                                            <input type="text" id="usuario-apellido" name="apellido" class="form-control" placeholder="Apellido">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="usuario-correo">Correo</label>
                                                            <input type="email" id="usuario-correo" name="correo" class="form-control" placeholder="usuario@muni.cl">
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label" for="usuario-telefono">Teléfono</label>
                                                            <input type="tel" id="usuario-telefono" name="telefono" class="form-control" placeholder="+56 9 1234 5678">
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label" for="usuario-estado">Estado</label>
                                                            <select id="usuario-estado" name="estado" class="form-select">
                                                                <option value="1">Habilitado</option>
                                                                <option value="0">Deshabilitado</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8 mb-3">
                                                            <label class="form-label" for="usuario-direccion">Dirección (opcional)</label>
                                                            <input type="text" id="usuario-direccion" name="direccion" class="form-control" placeholder="Dirección">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label" for="usuario-username">Username</label>
                                                            <input type="text" id="usuario-username" name="username" class="form-control" placeholder="usuario">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <h6 class="text-uppercase text-muted small mb-3">Credenciales</h6>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="usuario-password">Contraseña</label>
                                                            <input type="password" id="usuario-password" name="password" class="form-control" placeholder="********">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="usuario-password-confirm">Confirmar contraseña</label>
                                                            <input type="password" id="usuario-password-confirm" name="password_confirm" class="form-control" placeholder="********">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="usuario-avatar">Foto de perfil</label>
                                                            <input type="file" id="usuario-avatar" name="avatar" class="form-control" accept="image/png,image/jpeg,image/webp">
                                                            <small class="text-muted d-block mt-1">Formatos permitidos: JPG, PNG o WEBP.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="card border shadow-none h-100">
                                                <div class="card-body">
                                                    <h6 class="text-uppercase text-muted small mb-3">Roles asignados</h6>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <?php if (empty($roles)) : ?>
                                                            <span class="text-muted">No hay roles disponibles.</span>
                                                        <?php else : ?>
                                                            <?php foreach ($roles as $rol) : ?>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="rol-<?php echo (int) $rol['id']; ?>" name="roles[]" value="<?php echo (int) $rol['id']; ?>">
                                                                    <label class="form-check-label" for="rol-<?php echo (int) $rol['id']; ?>"><?php echo htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8'); ?></label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="alert alert-info mt-3 mb-0 small">
                                                        Los roles se asignan al crear el usuario. No se requiere asignación adicional.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar usuario</button>
                                    <a href="usuarios-lista.php" class="btn btn-outline-secondary ms-2">Cancelar</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
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
                                                <?php foreach ($usuarios as $usuario) : ?>
                                                    <?php
                                                    $avatar = $usuario['avatar_path'] ?: 'assets/images/users/user-1.jpg';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="avatar" class="rounded-circle" width="40" height="40">
                                                        </td>
                                                        <td><?php echo htmlspecialchars($usuario['rut'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido']), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($usuario['rol'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $usuario['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Habilitado</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Deshabilitado</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $usuario['ultimo_acceso'] ? htmlspecialchars($usuario['ultimo_acceso'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="usuarios-detalle.php?id=<?php echo (int) $usuario['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="usuarios-editar.php?id=<?php echo (int) $usuario['id']; ?>">Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este usuario?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $usuario['id']; ?>">
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
