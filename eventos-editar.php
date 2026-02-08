<?php
require __DIR__ . '/app/bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$evento = null;
$errors = [];
$success = $_GET['success'] ?? '';
$validationLink = null;

$usuarios = db()->query('SELECT id, nombre, apellido FROM users WHERE estado = 1 ORDER BY nombre')->fetchAll();
$eventTypes = ensure_event_types();
$eventosListado = [];
try {
    $stmtListado = db()->query('SELECT e.id, e.titulo, e.fecha_inicio, e.tipo, e.estado, e.habilitado, u.nombre AS encargado_nombre, u.apellido AS encargado_apellido, COUNT(r.id) AS solicitudes_total, SUM(r.correo_enviado = 1) AS correos_enviados FROM events e LEFT JOIN users u ON u.id = e.encargado_id LEFT JOIN event_authority_requests r ON r.event_id = e.id GROUP BY e.id ORDER BY e.fecha_inicio DESC');
    $eventosListado = $stmtListado->fetchAll();
} catch (Exception $e) {
} catch (Error $e) {
}

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $evento = $stmt->fetch();
    if ($evento) {
        $evento['validation_token'] = ensure_event_validation_token($id, $evento['validation_token'] ?? null);
        $validationLink = base_url() . '/eventos-validacion.php?token=' . urlencode($evento['validation_token']);
    }
}

function normalize_datetime_input(?string $value): string
{
    if (!$value) {
        return '';
    }
    $value = trim($value);
    $date = DateTime::createFromFormat('Y-m-d\\TH:i', $value);
    if ($date instanceof DateTime) {
        return $date->format('Y-m-d H:i:s');
    }
    $value = str_replace('T', ' ', $value);
    if (strlen($value) === 16) {
        return $value . ':00';
    }
    return $value;
}

function format_datetime_local(?string $value): string
{
    if (!$value) {
        return '';
    }
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
    if (!$date) {
        $date = DateTime::createFromFormat('Y-m-d H:i', $value);
    }
    return $date ? $date->format('Y-m-d\\TH:i') : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_action']) && $_POST['list_action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM events WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('eventos-editar.php');
        } catch (Exception $e) {
            $errors[] = 'No se pudo eliminar el evento. Verifica dependencias asociadas.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['list_action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['event_action'] ?? 'save';
    $postId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;

    if ($action === 'delete' && $postId > 0) {
        $stmt = db()->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([$postId]);
        redirect('eventos-editar.php?success=1');
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $fechaInicio = normalize_datetime_input($_POST['fecha_inicio'] ?? '');
    $fechaFin = normalize_datetime_input($_POST['fecha_fin'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $estado = $_POST['estado'] ?? 'borrador';
    $cupos = $_POST['cupos'] !== '' ? (int) $_POST['cupos'] : null;
    $publico = trim($_POST['publico_objetivo'] ?? '');
    $creadoPor = isset($_POST['creado_por']) ? (int) $_POST['creado_por'] : 0;
    $encargado = isset($_POST['encargado_id']) ? (int) $_POST['encargado_id'] : null;

    if ($titulo === '' || $descripcion === '' || $ubicacion === '' || $fechaInicio === '' || $fechaFin === '' || $tipo === '' || $creadoPor === 0) {
        $errors[] = 'Completa los campos obligatorios del evento.';
    }

    if (empty($errors)) {
        $targetId = $postId > 0 ? $postId : $id;
        if ($targetId > 0) {
            $stmt = db()->prepare('UPDATE events SET titulo = ?, descripcion = ?, ubicacion = ?, fecha_inicio = ?, fecha_fin = ?, tipo = ?, cupos = ?, publico_objetivo = ?, estado = ?, creado_por = ?, encargado_id = ? WHERE id = ?');
            $stmt->execute([
                $titulo,
                $descripcion,
                $ubicacion,
                $fechaInicio,
                $fechaFin,
                $tipo,
                $cupos,
                $publico !== '' ? $publico : null,
                $estado,
                $creadoPor,
                $encargado ?: null,
                $targetId,
            ]);
        } else {
            $validationToken = bin2hex(random_bytes(16));
            $stmt = db()->prepare('INSERT INTO events (titulo, descripcion, ubicacion, fecha_inicio, fecha_fin, tipo, cupos, publico_objetivo, estado, creado_por, encargado_id, validation_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $titulo,
                $descripcion,
                $ubicacion,
                $fechaInicio,
                $fechaFin,
                $tipo,
                $cupos,
                $publico !== '' ? $publico : null,
                $estado,
                $creadoPor,
                $encargado ?: null,
                $validationToken,
            ]);
        }

        redirect('eventos-editar.php?success=1');
    }
}

?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Nuevo evento"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Eventos Municipales"; $title = "Nuevo evento"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Evento actualizado correctamente.</div>
                <?php endif; ?>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) : ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row evento-formal">
                    <div class="col-12">
                        <div class="card gm-section mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?php echo $id > 0 ? 'Editar evento' : 'Crear evento'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form class="needs-validation" name="event-form" id="forms-event" data-submit="server" method="post" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="event_id" id="event-id" value="<?php echo (int) ($evento['id'] ?? 0); ?>">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="control-label form-label" for="event-title">Título</label>
                                            <input class="form-control" type="text" name="titulo" id="event-title" value="<?php echo htmlspecialchars($evento['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                            <div class="invalid-feedback">Ingresa un título válido.</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="event-estado">Estado</label>
                                            <select id="event-estado" name="estado" class="form-select">
                                                <?php $estadoActual = $evento['estado'] ?? 'borrador'; ?>
                                                <option value="borrador" <?php echo $estadoActual === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                                                <option value="revision" <?php echo $estadoActual === 'revision' ? 'selected' : ''; ?>>Revisión</option>
                                                <option value="publicado" <?php echo $estadoActual === 'publicado' ? 'selected' : ''; ?>>Publicado</option>
                                                <option value="finalizado" <?php echo $estadoActual === 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                                                <option value="cancelado" <?php echo $estadoActual === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="control-label form-label" for="event-description">Descripción</label>
                                            <textarea id="event-description" name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($evento['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="control-label form-label" for="event-location">Ubicación/Dirección</label>
                                            <input type="text" id="event-location" name="ubicacion" class="form-control" value="<?php echo htmlspecialchars($evento['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="control-label form-label" for="event-start">Fecha inicio</label>
                                            <input type="datetime-local" id="event-start" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars(format_datetime_local($evento['fecha_inicio'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="control-label form-label" for="event-end">Fecha fin</label>
                                            <input type="datetime-local" id="event-end" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars(format_datetime_local($evento['fecha_fin'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="control-label form-label" for="event-category">Tipo</label>
                                            <select class="form-select" name="tipo" id="event-category" required>
                                                <?php $tipoActual = $evento['tipo'] ?? ''; ?>
                                                <?php foreach ($eventTypes as $eventType) : ?>
                                                    <option value="<?php echo htmlspecialchars($eventType['nombre'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $tipoActual === $eventType['nombre'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($eventType['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="control-label form-label" for="event-cupos">Cupos (opcional)</label>
                                            <input type="number" id="event-cupos" name="cupos" class="form-control" value="<?php echo htmlspecialchars((string) ($evento['cupos'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="control-label form-label" for="event-publico">Público objetivo</label>
                                            <input type="text" id="event-publico" name="publico_objetivo" class="form-control" value="<?php echo htmlspecialchars($evento['publico_objetivo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="control-label form-label" for="event-creador">Creado por</label>
                                            <select id="event-creador" name="creado_por" class="form-select" required>
                                                <?php $creadorActual = (int) ($evento['creado_por'] ?? 0); ?>
                                                <?php foreach ($usuarios as $usuario) : ?>
                                                    <option value="<?php echo (int) $usuario['id']; ?>" <?php echo $creadorActual === (int) $usuario['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido']), ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="control-label form-label" for="event-encargado">Encargado</label>
                                            <select id="event-encargado" name="encargado_id" class="form-select">
                                                <?php $encargadoActual = (int) ($evento['encargado_id'] ?? 0); ?>
                                                <option value="">Sin encargado</option>
                                                <?php foreach ($usuarios as $usuario) : ?>
                                                    <option value="<?php echo (int) $usuario['id']; ?>" <?php echo $encargadoActual === (int) $usuario['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido']), ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Enlace público de validación</label>
                                        <?php if ($validationLink) : ?>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($validationLink, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                            <div class="form-text">Comparte este enlace para validar qué autoridades asistirán al evento.</div>
                                        <?php else : ?>
                                            <div class="form-control-plaintext text-muted">Guarda el evento para generar el enlace de validación.</div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <?php if ($id > 0) : ?>
                                            <a href="eventos-autoridades.php?event_id=<?php echo (int) $id; ?>" class="btn btn-outline-primary">
                                                Enviar confirmación de invitados
                                            </a>
                                        <?php endif; ?>

                                        <button type="reset" class="btn btn-light ms-auto">
                                            Limpiar
                                        </button>

                                        <button type="submit" class="btn btn-primary" id="btn-save-event" name="event_action" value="save">
                                            Guardar evento
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Listado de eventos</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Evento</th>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                                <th>Responsable</th>
                                                <th>Notificación</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($eventosListado)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay eventos registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($eventosListado as $eventoListado) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($eventoListado['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($eventoListado['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($eventoListado['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <span class="badge text-bg-<?php echo $eventoListado['estado'] === 'publicado' ? 'success' : ($eventoListado['estado'] === 'borrador' ? 'warning' : 'secondary'); ?>">
                                                                <?php echo htmlspecialchars(ucfirst($eventoListado['estado']), ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars(trim(($eventoListado['encargado_nombre'] ?? '') . ' ' . ($eventoListado['encargado_apellido'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $eventoListado['correos_enviados'] > 0) : ?>
                                                                <span class="badge text-bg-success">Enviada</span>
                                                            <?php elseif ((int) $eventoListado['solicitudes_total'] > 0) : ?>
                                                                <span class="badge text-bg-warning">Pendiente</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Sin enviar</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="eventos-detalle.php?id=<?php echo (int) $eventoListado['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="eventos-editar.php?id=<?php echo (int) $eventoListado['id']; ?>">Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este evento?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="list_action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $eventoListado['id']; ?>">
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

    <style>
        .evento-formal .card-header {
            background: #f8fafc;
        }

        .evento-formal .card-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .evento-formal .card-body {
            padding: 1rem;
        }

        .evento-formal .table-responsive {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
        }

        .evento-formal .table > :not(caption) > * > * {
            padding: 0.5rem 0.6rem;
        }

        .evento-formal .badge {
            font-weight: 600;
        }

    </style>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
