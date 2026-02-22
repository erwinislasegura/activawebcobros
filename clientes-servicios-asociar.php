<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';
$asociacionEdit = null;


function normalizar_periodicidad_servicio(string $periodicidad): string
{
    $periodicidad = strtolower(trim($periodicidad));
    $periodicidad = str_replace(['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'], ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'], $periodicidad);
    return $periodicidad;
}

function calcular_fecha_vencimiento(string $fechaRegistro, string $periodicidad): ?string
{
    if ($fechaRegistro === '') {
        return null;
    }

    $norm = normalizar_periodicidad_servicio($periodicidad);
    $intervalos = [
        'mensual' => '+1 month',
        'bimestral' => '+2 months',
        'trimestral' => '+3 months',
        'semestral' => '+6 months',
        'anual' => '+1 year',
    ];

    if (!isset($intervalos[$norm])) {
        return null;
    }

    return date('Y-m-d', strtotime($fechaRegistro . ' ' . $intervalos[$norm]));
}

function calcular_dias_faltantes(?string $fechaVencimiento): ?int
{
    $fechaVencimiento = trim((string) $fechaVencimiento);
    if ($fechaVencimiento === '') {
        return null;
    }

    try {
        $hoy = new DateTime('today');
        $venc = new DateTime($fechaVencimiento);
        $diff = $hoy->diff($venc);
        return (int) $diff->format('%r%a');
    } catch (Exception $e) {
        return null;
    }
}

function ensure_column(string $table, string $column, string $definition): void
{
    $dbName = $GLOBALS['config']['db']['name'] ?? '';
    if ($dbName === '') {
        return;
    }
    $stmt = db()->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$dbName, $table, $column]);
    if ((int) $stmt->fetchColumn() === 0) {
        db()->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
    }
}

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            servicio_id INT NOT NULL,
            motivo TEXT NULL,
            info_importante TEXT NULL,
            correo_enviado_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_cliente_servicio (cliente_id, servicio_id),
            INDEX idx_clientes_servicios_cliente (cliente_id),
            INDEX idx_clientes_servicios_servicio (servicio_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de asociaciones cliente-servicio.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de asociaciones cliente-servicio.';
}

try {
    ensure_column('clientes_servicios', 'fecha_registro', 'DATE NULL AFTER servicio_id');
    ensure_column('clientes_servicios', 'tiempo_servicio', 'VARCHAR(30) NULL AFTER fecha_registro');
    ensure_column('clientes_servicios', 'fecha_vencimiento', 'DATE NULL AFTER tiempo_servicio');

    db()->exec('UPDATE clientes_servicios SET tiempo_servicio = "Mensual" WHERE tiempo_servicio IS NULL OR TRIM(tiempo_servicio) = ""');
    db()->exec('UPDATE clientes_servicios SET fecha_registro = DATE(created_at) WHERE fecha_registro IS NULL');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 1 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "mensual"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 2 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "bimestral"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 3 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "trimestral"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 6 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "semestral"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 1 YEAR) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "anual"');
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la estructura de asociaciones.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la estructura de asociaciones.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = trim((string) ($_POST['action'] ?? ''));

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $errors[] = 'No se pudo identificar la asociación a eliminar.';
        } else {
            try {
                $stmt = db()->prepare('DELETE FROM clientes_servicios WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                redirect('clientes-servicios-asociar.php?success=deleted');
            } catch (Exception $e) {
                $errorMessage = 'No se pudo eliminar la asociación.';
            } catch (Error $e) {
                $errorMessage = 'No se pudo eliminar la asociación.';
            }
        }
    }

    if ($action === 'create' || $action === 'update') {
        $asociacionId = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $servicioId = (int) ($_POST['servicio_id'] ?? 0);
        $fechaRegistro = trim((string) ($_POST['fecha_registro'] ?? ''));
        $tiempoServicio = trim((string) ($_POST['tiempo_servicio'] ?? 'Mensual'));

        if ($action === 'update' && $asociacionId <= 0) {
            $errors[] = 'No se pudo identificar la asociación a editar.';
        }

        if ($clienteId <= 0) {
            $errors[] = 'Selecciona un cliente válido.';
        }
        if ($servicioId <= 0) {
            $errors[] = 'Selecciona un servicio válido.';
        }
        if ($fechaRegistro === '') {
            $errors[] = 'Debes indicar la fecha de registro.';
        }

        $fechaVencimiento = calcular_fecha_vencimiento($fechaRegistro, $tiempoServicio);
        if ($fechaVencimiento === null) {
            $errors[] = 'Selecciona un tiempo de servicio válido para calcular el vencimiento.';
        }

        if (empty($errors)) {
            try {
                if ($action === 'update') {
                    $stmtExists = db()->prepare('SELECT COUNT(*) FROM clientes_servicios WHERE cliente_id = ? AND servicio_id = ? AND id <> ?');
                    $stmtExists->execute([$clienteId, $servicioId, $asociacionId]);
                } else {
                    $stmtExists = db()->prepare('SELECT COUNT(*) FROM clientes_servicios WHERE cliente_id = ? AND servicio_id = ?');
                    $stmtExists->execute([$clienteId, $servicioId]);
                }
                if ((int) $stmtExists->fetchColumn() > 0) {
                    $errors[] = 'Esta asociación ya existe.';
                } else {
                    if ($action === 'update') {
                        $stmtUpdate = db()->prepare('UPDATE clientes_servicios SET cliente_id = ?, servicio_id = ?, fecha_registro = ?, tiempo_servicio = ?, fecha_vencimiento = ? WHERE id = ? LIMIT 1');
                        $stmtUpdate->execute([$clienteId, $servicioId, $fechaRegistro, $tiempoServicio, $fechaVencimiento, $asociacionId]);
                        redirect('clientes-servicios-asociar.php?success=updated');
                    } else {
                        $stmtInsert = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id, fecha_registro, tiempo_servicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?)');
                        $stmtInsert->execute([$clienteId, $servicioId, $fechaRegistro, $tiempoServicio, $fechaVencimiento]);
                        redirect('clientes-servicios-asociar.php?success=1');
                    }
                }
            } catch (Exception $e) {
                $errorMessage = 'No se pudo guardar la asociación.';
            } catch (Error $e) {
                $errorMessage = 'No se pudo guardar la asociación.';
            }
        }
    }
}

if (isset($_GET['id'])) {
    $editId = (int) $_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = db()->prepare('SELECT id, cliente_id, servicio_id, fecha_registro, tiempo_servicio FROM clientes_servicios WHERE id = ? LIMIT 1');
            $stmt->execute([$editId]);
            $asociacionEdit = $stmt->fetch() ?: null;
            if (!$asociacionEdit) {
                $errorMessage = 'No se encontró la asociación a editar.';
            }
        } catch (Exception $e) {
            $errorMessage = 'No se pudo cargar la asociación para edición.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo cargar la asociación para edición.';
        }
    }
}

$clientes = [];
$servicios = [];
$asociaciones = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre FROM clientes WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $servicios = db()->query(
        'SELECT s.id,
                s.nombre,
                s.descripcion,
                s.monto,
                s.link_boton_pago,
                s.estado,
                ts.nombre AS tipo_servicio
         FROM servicios s
         LEFT JOIN tipos_servicios ts ON ts.id = s.tipo_servicio_id
         ORDER BY s.nombre'
    )->fetchAll();
    $asociaciones = db()->query(
        'SELECT cs.id,
                c.codigo AS cliente_codigo,
                c.nombre AS cliente,
                s.nombre AS servicio,
                cs.fecha_registro,
                cs.tiempo_servicio,
                cs.fecha_vencimiento,
                cs.created_at
         FROM clientes_servicios cs
         JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los datos.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los datos.';
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = "Asociar servicios a clientes"; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = "Clientes"; $title = "Asociar servicios a clientes"; include('partials/page-title.php'); ?>

            <?php if ($success === '1') : ?><div class="alert alert-success">Asociación creada correctamente.</div><?php endif; ?>
            <?php if ($success === 'deleted') : ?><div class="alert alert-success">Asociación eliminada correctamente.</div><?php endif; ?>
            <?php if ($success === 'updated') : ?><div class="alert alert-success">Asociación actualizada correctamente.</div><?php endif; ?>
            <?php if ($errorMessage !== '') : ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?>
                        <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0"><?php echo $asociacionEdit ? 'Editar asociación' : 'Nueva asociación'; ?></h5></div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="<?php echo $asociacionEdit ? 'update' : 'create'; ?>">
                        <?php if ($asociacionEdit) : ?>
                            <input type="hidden" name="id" value="<?php echo (int) $asociacionEdit['id']; ?>">
                        <?php endif; ?>
                        <div class="col-md-6">
                            <label class="form-label">Cliente</label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">Selecciona un cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo (int) $cliente['id']; ?>" <?php echo ((int) ($_POST['cliente_id'] ?? ($asociacionEdit['cliente_id'] ?? 0)) === (int) $cliente['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars(($cliente['codigo'] ?? '') . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Servicio</label>
                            <select id="servicio-id" name="servicio_id" class="form-select" required>
                                <option value="">Selecciona un servicio</option>
                                <?php foreach ($servicios as $servicio) : ?>
                                    <option
                                        value="<?php echo (int) $servicio['id']; ?>"
                                        <?php echo ((int) ($_POST['servicio_id'] ?? ($asociacionEdit['servicio_id'] ?? 0)) === (int) $servicio['id']) ? 'selected' : ''; ?>
                                        data-nombre="<?php echo htmlspecialchars((string) ($servicio['nombre'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                        data-tipo="<?php echo htmlspecialchars((string) ($servicio['tipo_servicio'] ?? 'Sin tipo'), ENT_QUOTES, 'UTF-8'); ?>"
                                        data-monto="<?php echo htmlspecialchars('$' . number_format((float) ($servicio['monto'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>"
                                        data-estado="<?php echo (int) ($servicio['estado'] ?? 0) === 1 ? 'Activo' : 'Inactivo'; ?>"
                                        data-descripcion="<?php echo htmlspecialchars((string) ($servicio['descripcion'] ?? 'Sin descripción.'), ENT_QUOTES, 'UTF-8'); ?>"
                                        data-link="<?php echo htmlspecialchars((string) ($servicio['link_boton_pago'] ?? 'No configurado'), ENT_QUOTES, 'UTF-8'); ?>"
                                    >
                                        <?php echo htmlspecialchars((string) ($servicio['nombre'] ?? 'Servicio') . ' · $' . number_format((float) ($servicio['monto'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="fecha-registro">Fecha de registro</label>
                            <input type="date" id="fecha-registro" name="fecha_registro" class="form-control" value="<?php echo htmlspecialchars($_POST['fecha_registro'] ?? ($asociacionEdit['fecha_registro'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="tiempo-servicio">Tiempo de servicio</label>
                            <select id="tiempo-servicio" name="tiempo_servicio" class="form-select" required>
                                <?php $tiempoServicioActual = $_POST['tiempo_servicio'] ?? ($asociacionEdit['tiempo_servicio'] ?? 'Mensual'); ?>
                                <?php foreach (['Mensual', 'Bimestral', 'Trimestral', 'Semestral', 'Anual'] as $periodo) : ?>
                                    <option value="<?php echo $periodo; ?>" <?php echo $tiempoServicioActual === $periodo ? 'selected' : ''; ?>><?php echo $periodo; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light" id="servicio-detalle" style="display:none;">
                                <h6 class="mb-2">Detalle del servicio seleccionado</h6>
                                <div class="small text-muted mb-1"><strong>Nombre:</strong> <span id="detalle-nombre"></span></div>
                                <div class="small text-muted mb-1"><strong>Tipo:</strong> <span id="detalle-tipo"></span></div>
                                <div class="small text-muted mb-1"><strong>Monto:</strong> <span id="detalle-monto"></span></div>
                                <div class="small text-muted mb-1"><strong>Estado:</strong> <span id="detalle-estado"></span></div>
                                <div class="small text-muted mb-1"><strong>Descripción:</strong> <span id="detalle-descripcion"></span></div>
                                <div class="small text-muted"><strong>Link pago:</strong> <span id="detalle-link"></span></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?php echo $asociacionEdit ? 'Actualizar asociación' : 'Guardar asociación'; ?></button>
                            <?php if ($asociacionEdit) : ?>
                                <a href="clientes-servicios-asociar.php" class="btn btn-light">Cancelar edición</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Asociaciones registradas</h5>
                    <span class="badge text-bg-primary"><?php echo count($asociaciones); ?> registros</span>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Cliente</th><th>Servicio</th><th>Registro</th><th>Tiempo</th><th>Vencimiento</th><th>Días faltantes</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                        <?php if (empty($asociaciones)) : ?>
                            <tr><td colspan="7" class="text-center text-muted">No hay asociaciones registradas.</td></tr>
                        <?php else : foreach ($asociaciones as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($item['cliente_codigo'] ?? '') . ' - ' . $item['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(!empty($item['fecha_registro']) ? date('d/m/Y', strtotime((string) $item['fecha_registro'])) : '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['tiempo_servicio'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(!empty($item['fecha_vencimiento']) ? date('d/m/Y', strtotime((string) $item['fecha_vencimiento'])) : '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php $diasFaltantes = calcular_dias_faltantes((string) ($item['fecha_vencimiento'] ?? '')); ?>
                                <td>
                                    <?php if ($diasFaltantes === null) : ?>
                                        -
                                    <?php elseif ($diasFaltantes < 0) : ?>
                                        <span class="badge text-bg-danger">Vencido hace <?php echo abs($diasFaltantes); ?> días</span>
                                    <?php elseif ($diasFaltantes === 0) : ?>
                                        <span class="badge text-bg-warning">Vence hoy</span>
                                    <?php else : ?>
                                        <span class="badge text-bg-success"><?php echo $diasFaltantes; ?> días</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="clientes-servicios-asociar.php?id=<?php echo (int) $item['id']; ?>">Editar</a>
                                            </li>
                                            <li>
                                                <form method="post" onsubmit="return confirm('¿Eliminar esta asociación?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                                    <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <?php include('partials/footer.php'); ?>
    </div>
</div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
<script>
(function () {
    const servicioSelect = document.getElementById('servicio-id');
    const detalle = document.getElementById('servicio-detalle');
    if (!servicioSelect || !detalle) return;

    const campos = {
        nombre: document.getElementById('detalle-nombre'),
        tipo: document.getElementById('detalle-tipo'),
        monto: document.getElementById('detalle-monto'),
        estado: document.getElementById('detalle-estado'),
        descripcion: document.getElementById('detalle-descripcion'),
        link: document.getElementById('detalle-link')
    };

    function syncDetalle() {
        const option = servicioSelect.options[servicioSelect.selectedIndex];
        if (!option || !option.value) {
            detalle.style.display = 'none';
            return;
        }

        campos.nombre.textContent = option.dataset.nombre || '-';
        campos.tipo.textContent = option.dataset.tipo || '-';
        campos.monto.textContent = option.dataset.monto || '-';
        campos.estado.textContent = option.dataset.estado || '-';
        campos.descripcion.textContent = option.dataset.descripcion || '-';
        campos.link.textContent = option.dataset.link || '-';
        detalle.style.display = 'block';
    }

    servicioSelect.addEventListener('change', syncDetalle);
    syncDetalle();
})();
</script>
</body>
</html>
