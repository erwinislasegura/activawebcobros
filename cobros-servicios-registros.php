<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_servicio_id INT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS cobros_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            servicio_id INT NOT NULL,
            cliente VARCHAR(150) NOT NULL,
            referencia VARCHAR(120) NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            fecha_cobro DATE NOT NULL,
            fecha_primer_aviso DATE NULL,
            fecha_segundo_aviso DATE NULL,
            fecha_tercer_aviso DATE NULL,
            estado VARCHAR(40) NOT NULL DEFAULT "Pendiente",
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_cobros_servicios_servicio (servicio_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudieron preparar las tablas de cobros.';
} catch (Error $e) {
    $errorMessage = 'No se pudieron preparar las tablas de cobros.';
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
    ensure_column('cobros_servicios', 'fecha_primer_aviso', 'DATE NULL');
    ensure_column('cobros_servicios', 'fecha_segundo_aviso', 'DATE NULL');
    ensure_column('cobros_servicios', 'fecha_tercer_aviso', 'DATE NULL');
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de cobros.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de cobros.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $servicioId = (int) ($_POST['servicio_id'] ?? 0);
    $cliente = trim($_POST['cliente'] ?? '');
    $referencia = trim($_POST['referencia'] ?? '');
    $monto = trim($_POST['monto'] ?? '');
    $fechaCobro = trim($_POST['fecha_cobro'] ?? '');
    $fechaPrimerAviso = trim($_POST['fecha_primer_aviso'] ?? '');
    $fechaSegundoAviso = trim($_POST['fecha_segundo_aviso'] ?? '');
    $fechaTercerAviso = trim($_POST['fecha_tercer_aviso'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Pendiente');

    if ($servicioId <= 0) {
        $errors[] = 'Selecciona un servicio válido.';
    }
    if ($cliente === '') {
        $errors[] = 'El nombre del cliente es obligatorio.';
    }
    if ($monto === '' || !is_numeric($monto) || (float) $monto < 0) {
        $errors[] = 'Ingresa un monto válido.';
    }
    if ($fechaCobro === '') {
        $errors[] = 'Selecciona la fecha de cobro.';
    }
    if ($estado === '') {
        $errors[] = 'Selecciona un estado.';
    }

    if (empty($errors)) {
        try {
            $stmt = db()->prepare('INSERT INTO cobros_servicios (servicio_id, cliente, referencia, monto, fecha_cobro, fecha_primer_aviso, fecha_segundo_aviso, fecha_tercer_aviso, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $servicioId,
                $cliente,
                $referencia !== '' ? $referencia : null,
                $monto,
                $fechaCobro,
                $fechaPrimerAviso !== '' ? $fechaPrimerAviso : null,
                $fechaSegundoAviso !== '' ? $fechaSegundoAviso : null,
                $fechaTercerAviso !== '' ? $fechaTercerAviso : null,
                $estado,
            ]);
            redirect('cobros-servicios-registros.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo registrar el cobro.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo registrar el cobro.';
        }
    }
}

$servicios = [];
$cobros = [];
try {
    $servicios = db()->query('SELECT id, nombre, monto FROM servicios WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $cobros = db()->query(
        'SELECT cs.id, cs.cliente, cs.referencia, cs.monto, cs.fecha_cobro, cs.fecha_primer_aviso, cs.fecha_segundo_aviso, cs.fecha_tercer_aviso, cs.estado, cs.created_at, s.nombre AS servicio
         FROM cobros_servicios cs
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los registros de cobros.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los registros de cobros.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Registros de cobros"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Cobros de servicios"; $title = "Registros de cobros"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Cobro registrado correctamente.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Registrar cobro</h5>
                                <p class="text-muted mb-0">Asocia un cobro a un servicio existente.</p>
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
                                        <label class="form-label" for="cobro-servicio">Servicio</label>
                                        <select id="cobro-servicio" name="servicio_id" class="form-select" required>
                                            <option value="">Selecciona un servicio</option>
                                            <?php foreach ($servicios as $servicio) : ?>
                                                <option value="<?php echo (int) $servicio['id']; ?>" data-monto="<?php echo htmlspecialchars((string) $servicio['monto'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (empty($servicios)) : ?>
                                            <small class="text-muted d-block mt-2">Primero registra servicios activos en "Agregar servicio".</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-cliente">Cliente</label>
                                        <input type="text" id="cobro-cliente" name="cliente" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-referencia">Referencia</label>
                                        <input type="text" id="cobro-referencia" name="referencia" class="form-control" placeholder="Boleta, factura o folio">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-monto">Monto cobrado</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" id="cobro-monto" name="monto" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-fecha">Fecha de cobro</label>
                                        <input type="date" id="cobro-fecha" name="fecha_cobro" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-primer-aviso">Fecha primer aviso</label>
                                        <input type="date" id="cobro-primer-aviso" name="fecha_primer_aviso" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-segundo-aviso">Fecha segundo aviso</label>
                                        <input type="date" id="cobro-segundo-aviso" name="fecha_segundo_aviso" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-tercer-aviso">Fecha tercer aviso</label>
                                        <input type="date" id="cobro-tercer-aviso" name="fecha_tercer_aviso" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cobro-estado">Estado</label>
                                        <select id="cobro-estado" name="estado" class="form-select">
                                            <option value="Pendiente" selected>Pendiente</option>
                                            <option value="Pagado">Pagado</option>
                                            <option value="Anulado">Anulado</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" <?php echo empty($servicios) ? 'disabled' : ''; ?>>
                                        Registrar cobro
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Cobros registrados</h5>
                                    <p class="text-muted mb-0">Últimos cobros asociados a servicios.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($cobros); ?> registros</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Servicio</th>
                                                <th>Cliente</th>
                                                <th>Referencia</th>
                                                <th>Monto</th>
                                                <th>Fecha</th>
                                                <th>Avisos</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($cobros)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay cobros registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($cobros as $cobro) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($cobro['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($cobro['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($cobro['referencia'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>$<?php echo number_format((float) $cobro['monto'], 2, ',', '.'); ?></td>
                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($cobro['fecha_cobro'] ?? 'now')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <div class="text-muted small">
                                                                <?php echo $cobro['fecha_primer_aviso'] ? htmlspecialchars(date('d/m/Y', strtotime($cobro['fecha_primer_aviso'])), ENT_QUOTES, 'UTF-8') : '-'; ?>
                                                                /
                                                                <?php echo $cobro['fecha_segundo_aviso'] ? htmlspecialchars(date('d/m/Y', strtotime($cobro['fecha_segundo_aviso'])), ENT_QUOTES, 'UTF-8') : '-'; ?>
                                                                /
                                                                <?php echo $cobro['fecha_tercer_aviso'] ? htmlspecialchars(date('d/m/Y', strtotime($cobro['fecha_tercer_aviso'])), ENT_QUOTES, 'UTF-8') : '-'; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $estadoClass = 'text-bg-secondary';
                                                            if ($cobro['estado'] === 'Pagado') {
                                                                $estadoClass = 'text-bg-success';
                                                            } elseif ($cobro['estado'] === 'Pendiente') {
                                                                $estadoClass = 'text-bg-warning';
                                                            } elseif ($cobro['estado'] === 'Anulado') {
                                                                $estadoClass = 'text-bg-danger';
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $estadoClass; ?>">
                                                                <?php echo htmlspecialchars($cobro['estado'], ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
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

    <script>
        (function () {
            const servicioSelect = document.getElementById('cobro-servicio');
            const montoInput = document.getElementById('cobro-monto');
            if (!servicioSelect || !montoInput) {
                return;
            }
            servicioSelect.addEventListener('change', function () {
                const selected = servicioSelect.options[servicioSelect.selectedIndex];
                const monto = selected?.dataset?.monto ?? '';
                if (monto !== '') {
                    montoInput.value = monto;
                }
            });
        })();
    </script>

</body>

</html>
