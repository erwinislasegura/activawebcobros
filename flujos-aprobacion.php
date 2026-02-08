<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = $_GET['success'] ?? '';

$unidades = db()->query('SELECT id, nombre FROM unidades ORDER BY nombre')->fetchAll();
$flows = db()->query('SELECT f.*, u.nombre AS unidad FROM approval_flows f LEFT JOIN unidades u ON u.id = f.unidad_id ORDER BY f.created_at DESC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_flow') {
        $nombre = trim($_POST['nombre'] ?? '');
        $entidad = trim($_POST['entidad'] ?? '');
        $unidadId = (int) ($_POST['unidad_id'] ?? 0);
        $slaHoras = (int) ($_POST['sla_horas'] ?? 48);
        $etapasRaw = trim($_POST['etapas'] ?? '');

        if ($nombre === '' || $entidad === '') {
            $errors[] = 'Nombre y entidad son obligatorios.';
        }

        $etapas = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $etapasRaw)));
        if (empty($etapas)) {
            $errors[] = 'Agrega al menos una etapa.';
        }

        if (empty($errors)) {
            $stmt = db()->prepare('INSERT INTO approval_flows (nombre, entidad, unidad_id, sla_horas, estado) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $nombre,
                $entidad,
                $unidadId > 0 ? $unidadId : null,
                $slaHoras > 0 ? $slaHoras : 48,
                'activo',
            ]);
            $flowId = (int) db()->lastInsertId();

            $stmt = db()->prepare('INSERT INTO approval_steps (flow_id, orden, responsable) VALUES (?, ?, ?)');
            $orden = 1;
            foreach ($etapas as $etapa) {
                $stmt->execute([$flowId, $orden, $etapa]);
                $orden++;
            }

            redirect('flujos-aprobacion.php?success=flow');
        }
    }

    if ($action === 'update_sla') {
        $flowId = (int) ($_POST['flow_id'] ?? 0);
        $slaHoras = (int) ($_POST['sla_horas'] ?? 48);

        if ($flowId > 0) {
            $stmt = db()->prepare('UPDATE approval_flows SET sla_horas = ? WHERE id = ?');
            $stmt->execute([$slaHoras > 0 ? $slaHoras : 48, $flowId]);
            redirect('flujos-aprobacion.php?success=sla');
        }
    }
}

$steps = db()->query('SELECT s.*, f.nombre AS flujo FROM approval_steps s JOIN approval_flows f ON f.id = s.flow_id ORDER BY s.flow_id, s.orden')->fetchAll();
$stepsByFlow = [];
foreach ($steps as $step) {
    $stepsByFlow[$step['flow_id']][] = $step['responsable'];
}

$entidades = ['Eventos', 'Documentos', 'Autoridades'];
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Flujos de aprobación"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Aprobaciones"; $title = "Flujos de aprobación"; include('partials/page-title.php'); ?>

                <?php if ($success === 'flow') : ?>
                    <div class="alert alert-success">Flujo creado correctamente.</div>
                <?php elseif ($success === 'sla') : ?>
                    <div class="alert alert-success">SLA actualizado correctamente.</div>
                <?php endif; ?>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) : ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Crear flujo</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" class="row g-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="create_flow">
                                    <div class="col-12">
                                        <label class="form-label" for="flujo-nombre">Nombre</label>
                                        <input type="text" id="flujo-nombre" name="nombre" class="form-control" placeholder="Aprobación de eventos" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="flujo-entidad">Entidad</label>
                                        <select id="flujo-entidad" name="entidad" class="form-select" required>
                                            <option value="">Selecciona</option>
                                            <?php foreach ($entidades as $entidad) : ?>
                                                <option value="<?php echo htmlspecialchars($entidad, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($entidad, ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="flujo-unidad">Unidad responsable</label>
                                        <select id="flujo-unidad" name="unidad_id" class="form-select">
                                            <option value="">Todas</option>
                                            <?php foreach ($unidades as $unidad) : ?>
                                                <option value="<?php echo (int) $unidad['id']; ?>"><?php echo htmlspecialchars($unidad['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="flujo-sla">SLA (horas)</label>
                                        <input type="number" id="flujo-sla" name="sla_horas" class="form-control" value="48" min="1">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="flujo-etapas">Etapas (una por línea)</label>
                                        <textarea id="flujo-etapas" name="etapas" class="form-control" rows="4" placeholder="Encargado\nJefatura\nAlcaldía" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit">Guardar flujo</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Actualizar SLA</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" class="row g-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="update_sla">
                                    <div class="col-12">
                                        <label class="form-label" for="sla-flow">Flujo</label>
                                        <select id="sla-flow" name="flow_id" class="form-select">
                                            <?php foreach ($flows as $flow) : ?>
                                                <option value="<?php echo (int) $flow['id']; ?>"><?php echo htmlspecialchars($flow['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="sla">Tiempo máximo (horas)</label>
                                        <input type="number" id="sla" name="sla_horas" class="form-control" value="48" min="1">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-outline-secondary w-100">Actualizar SLA</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Flujos activos</h5>
                                    <p class="text-muted mb-0">Secuencias de validación por entidad y unidad.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Flujo</th>
                                                <th>Entidad</th>
                                                <th>Unidad</th>
                                                <th>Etapas</th>
                                                <th>SLA</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($flows)) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-muted text-center">No hay flujos creados.</td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php foreach ($flows as $flow) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($flow['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($flow['entidad'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($flow['unidad'] ?? 'Todas', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td>
                                                        <?php
                                                        $etapas = $stepsByFlow[$flow['id']] ?? [];
                                                        echo htmlspecialchars(implode(' → ', $etapas), ENT_QUOTES, 'UTF-8');
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars((string) $flow['sla_horas'], ENT_QUOTES, 'UTF-8'); ?>h</td>
                                                    <td><span class="badge text-bg-success">Activo</span></td>
                                                </tr>
                                            <?php endforeach; ?>
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
