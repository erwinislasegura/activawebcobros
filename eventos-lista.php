<?php
require __DIR__ . '/app/bootstrap.php';
redirect('eventos-editar.php');

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($_POST['action'] === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM events WHERE id = ?');
            $stmt->execute([$id]);
            redirect('eventos-lista.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el evento. Verifica dependencias asociadas.';
        }
    }
}

$stmt = db()->query('SELECT e.id, e.titulo, e.fecha_inicio, e.fecha_fin, e.tipo, e.estado, e.habilitado, u.nombre AS encargado_nombre, u.apellido AS encargado_apellido, COUNT(r.id) AS solicitudes_total, SUM(r.correo_enviado = 1) AS correos_enviados FROM events e LEFT JOIN users u ON u.id = e.encargado_id LEFT JOIN event_authority_requests r ON r.event_id = e.id GROUP BY e.id ORDER BY e.fecha_inicio DESC');
$eventos = $stmt->fetchAll();
$eventTypes = [];
foreach ($eventos as $evento) {
    if (!empty($evento['tipo'])) {
        $eventTypes[$evento['tipo']] = true;
    }
}
$eventTypes = array_keys($eventTypes);
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listar eventos"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Eventos Municipales"; $title = "Listar eventos"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card eventos-lista">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Eventos municipales</h5>
                                    <p class="text-muted mb-0">Listado y control de eventos.</p>
                                </div>
                                <a href="eventos-editar.php" class="btn btn-primary">Nuevo evento</a>
                            </div>
                            <div class="card-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="eventos-filters mb-3">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label" for="event-search">Buscar evento</label>
                                            <input type="search" id="event-search" class="form-control" placeholder="Buscar por título o responsable">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label" for="event-start">Desde</label>
                                            <input type="date" id="event-start" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label" for="event-end">Hasta</label>
                                            <input type="date" id="event-end" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label" for="event-status">Estado</label>
                                            <select id="event-status" class="form-select">
                                                <option value="">Todos</option>
                                                <option value="borrador">Borrador</option>
                                                <option value="publicado">Publicado</option>
                                                <option value="finalizado">Finalizado</option>
                                                <option value="cancelado">Cancelado</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label" for="event-type">Tipo</label>
                                            <select id="event-type" class="form-select">
                                                <option value="">Todos</option>
                                                <?php foreach ($eventTypes as $type) : ?>
                                                    <option value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div class="text-muted small" id="events-count">Mostrando <?php echo count($eventos); ?> eventos</div>
                                    <div class="badge bg-primary-subtle text-primary">Actualizado automáticamente</div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0" id="events-table">
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
                                            <?php if (empty($eventos)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay eventos registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($eventos as $evento) : ?>
                                                    <?php
                                                    $responsable = trim(($evento['encargado_nombre'] ?? '') . ' ' . ($evento['encargado_apellido'] ?? ''));
                                                    $estado = strtolower((string) $evento['estado']);
                                                    $tipo = (string) $evento['tipo'];
                                                    $fechaInicio = (string) $evento['fecha_inicio'];
                                                    $fechaFin = (string) ($evento['fecha_fin'] ?? '');
                                                    ?>
                                                    <tr>
                                                        <td data-title="<?php echo htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8'); ?>" data-responsable="<?php echo htmlspecialchars($responsable, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <div class="fw-semibold"><?php echo htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                            <div class="text-muted small">Responsable: <?php echo htmlspecialchars($responsable !== '' ? $responsable : 'Sin asignar', ENT_QUOTES, 'UTF-8'); ?></div>
                                                        </td>
                                                        <td data-fecha="<?php echo htmlspecialchars($fechaInicio, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <div><?php echo htmlspecialchars($fechaInicio, ENT_QUOTES, 'UTF-8'); ?></div>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($fechaFin !== '' ? $fechaFin : 'Sin cierre', ENT_QUOTES, 'UTF-8'); ?></div>
                                                        </td>
                                                        <td data-tipo="<?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <span class="badge bg-info-subtle text-info"><?php echo htmlspecialchars($tipo !== '' ? $tipo : 'Sin tipo', ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $estadoClass = 'secondary';
                                                            if ($estado === 'publicado') {
                                                                $estadoClass = 'success';
                                                            } elseif ($estado === 'borrador') {
                                                                $estadoClass = 'warning';
                                                            } elseif ($estado === 'finalizado') {
                                                                $estadoClass = 'primary';
                                                            } elseif ($estado === 'cancelado') {
                                                                $estadoClass = 'danger';
                                                            }
                                                            ?>
                                                            <span class="badge text-bg-<?php echo $estadoClass; ?>" data-estado="<?php echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo htmlspecialchars($estado !== '' ? ucfirst($estado) : 'Sin estado', ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($responsable !== '' ? $responsable : '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $evento['correos_enviados'] > 0) : ?>
                                                                <span class="badge text-bg-success">Enviada</span>
                                                            <?php elseif ((int) $evento['solicitudes_total'] > 0) : ?>
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
                                                                    <li><a class="dropdown-item" href="eventos-detalle.php?id=<?php echo (int) $evento['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="eventos-editar.php?id=<?php echo (int) $evento['id']; ?>">Editar</a></li>
                                                                    <li><a class="dropdown-item" href="eventos-autoridades.php?event_id=<?php echo (int) $evento['id']; ?>">Asignar autoridades</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este evento?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $evento['id']; ?>">
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
        .eventos-lista .card-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(13, 110, 253, 0.02));
        }

        .eventos-lista .badge {
            font-weight: 600;
        }

        .eventos-filters .form-label {
            font-size: 0.8rem;
            color: #64748b;
        }

        .eventos-filters .form-control,
        .eventos-filters .form-select {
            min-height: 38px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('event-search');
            const startInput = document.getElementById('event-start');
            const endInput = document.getElementById('event-end');
            const statusSelect = document.getElementById('event-status');
            const typeSelect = document.getElementById('event-type');
            const table = document.getElementById('events-table');
            const counter = document.getElementById('events-count');

            if (!table) {
                return;
            }

            const normalize = (value) => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            const filterRows = () => {
                const term = normalize(searchInput?.value || '');
                const start = startInput?.value || '';
                const end = endInput?.value || '';
                const status = (statusSelect?.value || '').toLowerCase();
                const type = (typeSelect?.value || '').toLowerCase();
                let visible = 0;

                table.querySelectorAll('tbody tr').forEach((row) => {
                    const title = normalize(row.querySelector('td[data-title]')?.dataset.title || '');
                    const responsable = normalize(row.querySelector('td[data-title]')?.dataset.responsable || '');
                    const fecha = row.querySelector('td[data-fecha]')?.dataset.fecha || '';
                    const estado = (row.querySelector('[data-estado]')?.dataset.estado || '').toLowerCase();
                    const tipo = (row.querySelector('td[data-tipo]')?.dataset.tipo || '').toLowerCase();

                    const matchTerm = term === '' || title.includes(term) || responsable.includes(term);
                    const matchStatus = status === '' || estado === status;
                    const matchType = type === '' || tipo === type;
                    const matchStart = start === '' || fecha >= start;
                    const matchEnd = end === '' || fecha <= end;

                    const isVisible = matchTerm && matchStatus && matchType && matchStart && matchEnd;
                    row.classList.toggle('d-none', !isVisible);
                    if (isVisible) {
                        visible += 1;
                    }
                });

                if (counter) {
                    counter.textContent = `Mostrando ${visible} eventos`;
                }
            };

            [searchInput, startInput, endInput, statusSelect, typeSelect].forEach((input) => {
                if (!input) {
                    return;
                }
                input.addEventListener('input', filterRows);
                input.addEventListener('change', filterRows);
            });

            filterRows();
        });
    </script>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
