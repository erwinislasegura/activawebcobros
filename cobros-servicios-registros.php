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
            color_hex VARCHAR(10) NOT NULL,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_clientes_codigo (codigo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_servicio_id INT NULL,
            nombre VARCHAR(150) NOT NULL,
            descripcion TEXT NULL,
            link_boton_pago VARCHAR(255) NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            estado TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    db()->exec(
        'CREATE TABLE IF NOT EXISTS cobros_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            servicio_id INT NOT NULL,
            cliente_id INT NULL,
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
    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            servicio_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_cliente_servicio (cliente_id, servicio_id),
            INDEX idx_clientes_servicios_cliente (cliente_id),
            INDEX idx_clientes_servicios_servicio (servicio_id)
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
    ensure_column('servicios', 'link_boton_pago', 'VARCHAR(255) NULL');
    ensure_column('cobros_servicios', 'cliente_id', 'INT NULL');
    ensure_column('cobros_servicios', 'fecha_primer_aviso', 'DATE NULL');
    ensure_column('cobros_servicios', 'fecha_segundo_aviso', 'DATE NULL');
    ensure_column('cobros_servicios', 'fecha_tercer_aviso', 'DATE NULL');
    ensure_column('cobros_servicios', 'aviso_1_enviado_at', 'DATETIME NULL');
    ensure_column('cobros_servicios', 'aviso_2_enviado_at', 'DATETIME NULL');
    ensure_column('cobros_servicios', 'aviso_3_enviado_at', 'DATETIME NULL');
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de cobros.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la tabla de cobros.';
}

function generar_referencia_cobro(): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $ref = '';
    for ($i = 0; $i < 7; $i++) {
        $ref .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $ref;
}

function referencia_unica(): string
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM cobros_servicios WHERE referencia = ?');
    $referencia = generar_referencia_cobro();
    while (true) {
        $stmt->execute([$referencia]);
        if ((int) $stmt->fetchColumn() === 0) {
            break;
        }
        $referencia = generar_referencia_cobro();
    }
    return $referencia;
}

$referenciaInput = trim($_POST['referencia'] ?? '');
if ($referenciaInput === '') {
    $referenciaInput = referencia_unica();
}

$serviciosIniciales = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM cobros_servicios WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('cobros-servicios-registros.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el cobro.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar el cobro.';
        }
    }
}

$cobroEdit = null;
if (isset($_GET['id'])) {
    $editId = (int) $_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = db()->prepare('SELECT id, servicio_id, cliente_id, referencia, monto, fecha_primer_aviso, fecha_segundo_aviso, fecha_tercer_aviso, estado FROM cobros_servicios WHERE id = ?');
            $stmt->execute([$editId]);
            $cobroEdit = $stmt->fetch() ?: null;
        } catch (Exception $e) {
        } catch (Error $e) {
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $servicioId = (int) ($_POST['servicio_id'] ?? 0);
    $clienteId = (int) ($_POST['cliente_id'] ?? 0);
    $cliente = '';
    $referencia = trim($_POST['referencia'] ?? '');
    $monto = trim($_POST['monto'] ?? '');
    $fechaCobro = '';
    $fechaPrimerAviso = trim($_POST['fecha_primer_aviso'] ?? '');
    $fechaSegundoAviso = trim($_POST['fecha_segundo_aviso'] ?? '');
    $fechaTercerAviso = trim($_POST['fecha_tercer_aviso'] ?? '');
    $estado = trim($_POST['estado'] ?? 'Pendiente');

    if ($servicioId <= 0) {
        $errors[] = 'Selecciona un servicio válido.';
    }
    if ($clienteId <= 0) {
        $errors[] = 'Selecciona un cliente válido.';
    }
    if ($clienteId > 0 && $servicioId > 0) {
        try {
            $stmt = db()->prepare('SELECT COUNT(*) FROM clientes_servicios WHERE cliente_id = ? AND servicio_id = ?');
            $stmt->execute([$clienteId, $servicioId]);
            if ((int) $stmt->fetchColumn() === 0) {
                $errors[] = 'El servicio seleccionado no está asignado al cliente.';
            }
        } catch (Exception $e) {
            $errors[] = 'No se pudo validar el servicio del cliente.';
        } catch (Error $e) {
            $errors[] = 'No se pudo validar el servicio del cliente.';
        }
    }
    if ($monto === '' || !is_numeric($monto) || (float) $monto < 0) {
        $errors[] = 'Ingresa un monto válido.';
    }
    if ($fechaPrimerAviso === '') {
        $errors[] = 'Selecciona la fecha del primer aviso.';
    }
    if ($estado === '') {
        $errors[] = 'Selecciona un estado.';
    }

    if ($fechaPrimerAviso !== '') {
        $fechaCobro = $fechaPrimerAviso;
    }
    if ($fechaPrimerAviso !== '' && $fechaSegundoAviso === '') {
        $fechaSegundoAviso = date('Y-m-d', strtotime($fechaPrimerAviso . ' +2 days'));
    }
    if ($fechaPrimerAviso !== '' && $fechaTercerAviso === '') {
        $fechaTercerAviso = date('Y-m-d', strtotime($fechaPrimerAviso . ' +5 days'));
    }

    if ($referencia === '') {
        $referencia = referencia_unica();
    }

    if (empty($errors)) {
        try {
            $clienteStmt = db()->prepare('SELECT nombre FROM clientes WHERE id = ?');
            $clienteStmt->execute([$clienteId]);
            $cliente = (string) ($clienteStmt->fetchColumn() ?: '');

            if ($cobroEdit && isset($cobroEdit['id'])) {
                $stmt = db()->prepare('UPDATE cobros_servicios SET servicio_id = ?, cliente_id = ?, cliente = ?, referencia = ?, monto = ?, fecha_cobro = ?, fecha_primer_aviso = ?, fecha_segundo_aviso = ?, fecha_tercer_aviso = ?, estado = ? WHERE id = ?');
                $stmt->execute([
                    $servicioId,
                    $clienteId > 0 ? $clienteId : null,
                    $cliente !== '' ? $cliente : 'Cliente sin nombre',
                    $referencia !== '' ? $referencia : null,
                    $monto,
                    $fechaCobro,
                    $fechaPrimerAviso !== '' ? $fechaPrimerAviso : null,
                    $fechaSegundoAviso !== '' ? $fechaSegundoAviso : null,
                    $fechaTercerAviso !== '' ? $fechaTercerAviso : null,
                    $estado,
                    (int) $cobroEdit['id'],
                ]);
            } else {
                $stmt = db()->prepare('INSERT INTO cobros_servicios (servicio_id, cliente_id, cliente, referencia, monto, fecha_cobro, fecha_primer_aviso, fecha_segundo_aviso, fecha_tercer_aviso, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([
                    $servicioId,
                    $clienteId > 0 ? $clienteId : null,
                    $cliente !== '' ? $cliente : 'Cliente sin nombre',
                    $referencia !== '' ? $referencia : null,
                    $monto,
                    $fechaCobro,
                    $fechaPrimerAviso !== '' ? $fechaPrimerAviso : null,
                    $fechaSegundoAviso !== '' ? $fechaSegundoAviso : null,
                    $fechaTercerAviso !== '' ? $fechaTercerAviso : null,
                    $estado,
                ]);
            }
            redirect('cobros-servicios-registros.php?success=1');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo registrar el cobro.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo registrar el cobro.';
        }
    }
}

$clientes = [];
$servicios = [];
$serviciosPorCliente = [];
$cobros = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre, color_hex FROM clientes WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $servicios = db()->query('SELECT id, nombre, monto FROM servicios WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $asignaciones = db()->query(
        'SELECT cs.cliente_id,
                s.id,
                s.nombre,
                s.monto
         FROM clientes_servicios cs
         JOIN servicios s ON s.id = cs.servicio_id
         WHERE s.estado = 1
         ORDER BY s.nombre'
    )->fetchAll();
    foreach ($asignaciones as $asignacion) {
        $clienteId = (int) $asignacion['cliente_id'];
        if (!isset($serviciosPorCliente[$clienteId])) {
            $serviciosPorCliente[$clienteId] = [];
        }
        $serviciosPorCliente[$clienteId][] = [
            'id' => (int) $asignacion['id'],
            'nombre' => $asignacion['nombre'],
            'monto' => $asignacion['monto'],
        ];
    }
    $cobros = db()->query(
        'SELECT cs.id,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                c.codigo AS cliente_codigo,
                c.color_hex AS cliente_color,
                c.correo AS cliente_correo,
                cs.referencia,
                cs.monto,
                cs.fecha_cobro,
                cs.fecha_primer_aviso,
                cs.fecha_segundo_aviso,
                cs.fecha_tercer_aviso,
                cs.aviso_1_enviado_at,
                cs.aviso_2_enviado_at,
                cs.aviso_3_enviado_at,
                cs.estado,
                cs.created_at,
                s.nombre AS servicio
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los registros de cobros.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los registros de cobros.';
}

if ($cobroEdit && isset($cobroEdit['cliente_id'])) {
    $serviciosIniciales = $serviciosPorCliente[(int) $cobroEdit['cliente_id']] ?? [];
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
                    <div class="col-12">
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
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="cobro-servicio">Servicio</label>
                                            <select id="cobro-servicio" name="servicio_id" class="form-select" required>
                                                <option value=""><?php echo empty($serviciosIniciales) ? 'Selecciona un cliente primero' : 'Selecciona un servicio'; ?></option>
                                                <?php foreach ($serviciosIniciales as $servicio) : ?>
                                                    <option value="<?php echo (int) $servicio['id']; ?>" data-monto="<?php echo htmlspecialchars((string) $servicio['monto'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($cobroEdit['servicio_id'] ?? 0) == (int) $servicio['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($servicios)) : ?>
                                                <small class="text-muted d-block mt-2">Primero registra servicios activos en "Agregar servicio".</small>
                                            <?php elseif (empty($serviciosIniciales)) : ?>
                                                <small class="text-muted d-block mt-2">Asigna servicios al cliente para habilitar el listado.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="cobro-cliente">Cliente</label>
                                            <select id="cobro-cliente" name="cliente_id" class="form-select" required>
                                                <option value="">Selecciona un cliente</option>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                <option value="<?php echo (int) $cliente['id']; ?>" <?php echo ($cobroEdit['cliente_id'] ?? 0) == (int) $cliente['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cliente['codigo'] . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($clientes)) : ?>
                                                <small class="text-muted d-block mt-2">Primero registra clientes en "Crear cliente".</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="cobro-referencia">Referencia</label>
                                            <input type="text" id="cobro-referencia" name="referencia" class="form-control" value="<?php echo htmlspecialchars($cobroEdit['referencia'] ?? $referenciaInput, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                            <small class="text-muted">Referencia única generada automáticamente.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="cobro-monto">Monto cobrado</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0" id="cobro-monto" name="monto" class="form-control" value="<?php echo htmlspecialchars($cobroEdit['monto'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="cobro-primer-aviso">Fecha primer aviso</label>
                                            <input type="date" id="cobro-primer-aviso" name="fecha_primer_aviso" class="form-control" value="<?php echo htmlspecialchars($cobroEdit['fecha_primer_aviso'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                            <small class="text-muted">La fecha de cobro se iguala al primer aviso.</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="cobro-segundo-aviso">Fecha segundo aviso</label>
                                            <input type="date" id="cobro-segundo-aviso" name="fecha_segundo_aviso" class="form-control" value="<?php echo htmlspecialchars($cobroEdit['fecha_segundo_aviso'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="cobro-tercer-aviso">Fecha tercer aviso</label>
                                            <input type="date" id="cobro-tercer-aviso" name="fecha_tercer_aviso" class="form-control" value="<?php echo htmlspecialchars($cobroEdit['fecha_tercer_aviso'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="cobro-estado">Estado</label>
                                            <select id="cobro-estado" name="estado" class="form-select">
                                                <option value="Pendiente" <?php echo ($cobroEdit['estado'] ?? 'Pendiente') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="Pagado" <?php echo ($cobroEdit['estado'] ?? '') === 'Pagado' ? 'selected' : ''; ?>>Pagado</option>
                                                <option value="Anulado" <?php echo ($cobroEdit['estado'] ?? '') === 'Anulado' ? 'selected' : ''; ?>>Anulado</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100" <?php echo empty($servicios) ? 'disabled' : ''; ?>>
                                                <?php echo $cobroEdit ? 'Actualizar cobro' : 'Registrar cobro'; ?>
                                            </button>
                                        </div>
                                    </div>
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
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($cobros)) : ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">No hay cobros registrados.</td>
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
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="cobros-servicios-registros.php?id=<?php echo (int) $cobro['id']; ?>">Ver/Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este cobro?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $cobro['id']; ?>">
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
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Avisos por cliente</h5>
                                <p class="text-muted mb-0">Envía los avisos programados desde cada cobro.</p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th>Correo</th>
                                                <th>Avisos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($cobros)) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No hay avisos registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($cobros as $cobro) : ?>
                                                    <?php
                                                    $avisos = [
                                                        [
                                                            'key' => 'aviso_1',
                                                            'label' => 'Aviso 1',
                                                            'fecha' => $cobro['fecha_primer_aviso'] ?? null,
                                                            'sent' => $cobro['aviso_1_enviado_at'] ?? null,
                                                        ],
                                                        [
                                                            'key' => 'aviso_2',
                                                            'label' => 'Aviso 2',
                                                            'fecha' => $cobro['fecha_segundo_aviso'] ?? null,
                                                            'sent' => $cobro['aviso_2_enviado_at'] ?? null,
                                                        ],
                                                        [
                                                            'key' => 'aviso_3',
                                                            'label' => 'Aviso 3',
                                                            'fecha' => $cobro['fecha_tercer_aviso'] ?? null,
                                                            'sent' => $cobro['aviso_3_enviado_at'] ?? null,
                                                        ],
                                                    ];
                                                    $correoCliente = $cobro['cliente_correo'] ?? '';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge" style="background-color: <?php echo htmlspecialchars($cobro['cliente_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8'); ?>;">
                                                                <?php echo htmlspecialchars($cobro['cliente_codigo'] ?? 'SIN-COD', ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                            <?php echo htmlspecialchars($cobro['cliente'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($cobro['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($correoCliente !== '' ? $correoCliente : 'Sin correo', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <ul class="list-group list-group-flush">
                                                                <?php foreach ($avisos as $aviso) : ?>
                                                                    <li class="list-group-item px-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                        <div>
                                                                            <div class="fw-semibold"><?php echo htmlspecialchars($aviso['label'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                                            <div class="text-muted small">
                                                                                Fecha: <?php echo $aviso['fecha'] ? htmlspecialchars(date('d/m/Y', strtotime($aviso['fecha'])), ENT_QUOTES, 'UTF-8') : 'Sin fecha'; ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                                                            <?php if (!empty($aviso['sent'])) : ?>
                                                                                <span class="badge text-bg-success">Enviado</span>
                                                                                <span class="text-muted small">
                                                                                    <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($aviso['sent'])), ENT_QUOTES, 'UTF-8'); ?>
                                                                                </span>
                                                                            <?php else : ?>
                                                                                <span class="badge text-bg-warning">Pendiente</span>
                                                                            <?php endif; ?>
                                                                            <form method="post" action="cobros-avisos.php" class="d-inline">
                                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="action" value="send_aviso">
                                                                                <input type="hidden" name="id" value="<?php echo (int) $cobro['id']; ?>">
                                                                                <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($aviso['key'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="return_url" value="cobros-servicios-registros.php">
                                                                                <?php
                                                                                $disabled = $correoCliente === '' || $aviso['fecha'] === null || $aviso['fecha'] === '';
                                                                                ?>
                                                                                <button type="submit" class="btn btn-sm btn-outline-primary" <?php echo $disabled ? 'disabled' : ''; ?>>
                                                                                    <?php echo !empty($aviso['sent']) ? 'Reenviar' : 'Enviar'; ?>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
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
            const clienteSelect = document.getElementById('cobro-cliente');
            const primerAvisoInput = document.getElementById('cobro-primer-aviso');
            const segundoAvisoInput = document.getElementById('cobro-segundo-aviso');
            const tercerAvisoInput = document.getElementById('cobro-tercer-aviso');
            const serviciosPorCliente = <?php echo json_encode($serviciosPorCliente); ?>;
            const servicioSeleccionado = <?php echo (int) ($cobroEdit['servicio_id'] ?? 0); ?>;

            function renderServicios(clienteId) {
                if (!servicioSelect) {
                    return;
                }
                servicioSelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                if (!clienteId) {
                    placeholder.textContent = 'Selecciona un cliente primero';
                    servicioSelect.appendChild(placeholder);
                    return;
                }
                const servicios = serviciosPorCliente[clienteId] || [];
                if (!servicios.length) {
                    placeholder.textContent = 'No hay servicios asignados';
                    servicioSelect.appendChild(placeholder);
                    return;
                }
                placeholder.textContent = 'Selecciona un servicio';
                servicioSelect.appendChild(placeholder);
                servicios.forEach((servicio) => {
                    const option = document.createElement('option');
                    option.value = servicio.id;
                    option.textContent = servicio.nombre;
                    option.dataset.monto = servicio.monto;
                    if (servicioSeleccionado && Number(servicioSeleccionado) === Number(servicio.id)) {
                        option.selected = true;
                    }
                    servicioSelect.appendChild(option);
                });
            }

            if (servicioSelect && montoInput) {
                servicioSelect.addEventListener('change', function () {
                    const selected = servicioSelect.options[servicioSelect.selectedIndex];
                    const monto = selected?.dataset?.monto ?? '';
                    if (monto !== '') {
                        montoInput.value = monto;
                    }
                });
            }

            if (clienteSelect) {
                clienteSelect.addEventListener('change', function () {
                    renderServicios(clienteSelect.value);
                    if (montoInput) {
                        montoInput.value = '';
                    }
                });
                renderServicios(clienteSelect.value);
            }

            if (primerAvisoInput && segundoAvisoInput && tercerAvisoInput) {
                primerAvisoInput.addEventListener('change', function () {
                    if (!primerAvisoInput.value) {
                        return;
                    }
                    const primer = new Date(primerAvisoInput.value + 'T00:00:00');
                    const segundo = new Date(primer);
                    segundo.setDate(segundo.getDate() + 2);
                    const tercero = new Date(primer);
                    tercero.setDate(tercero.getDate() + 5);
                    segundoAvisoInput.value = segundo.toISOString().slice(0, 10);
                    tercerAvisoInput.value = tercero.toISOString().slice(0, 10);
                });
            }
        })();
    </script>

</body>

</html>
