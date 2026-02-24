<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';
$asociacionEdit = null;
$filtroTexto = trim((string) ($_GET['q'] ?? ''));


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
    ensure_column('clientes_servicios', 'codigo_cotizacion', 'VARCHAR(40) NULL AFTER servicio_id');
    ensure_column('clientes_servicios', 'enviar_correo', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_vencimiento');
    ensure_column('clientes_servicios', 'nota_cotizacion', 'TEXT NULL AFTER enviar_correo');
    ensure_column('clientes_servicios', 'subtotal', 'DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER nota_cotizacion');
    ensure_column('clientes_servicios', 'descuento_monto', 'DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal');
    ensure_column('clientes_servicios', 'total', 'DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_monto');

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

    if ($action === 'create_quote') {
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $fechaRegistro = trim((string) ($_POST['fecha_registro'] ?? ''));
        $enviarCorreo = isset($_POST['enviar_correo']) ? 1 : 0;
        $notaCotizacion = trim((string) ($_POST['nota_cotizacion'] ?? ''));
        $serviciosIds = $_POST['servicio_id'] ?? [];
        $periodicidades = $_POST['tiempo_servicio'] ?? [];
        $descuentos = $_POST['descuento'] ?? [];

        if ($clienteId <= 0) {
            $errors[] = 'Selecciona un cliente válido.';
        }
        if ($fechaRegistro === '') {
            $errors[] = 'Debes indicar la fecha de cotización.';
        }
        if (!is_array($serviciosIds) || count($serviciosIds) === 0) {
            $errors[] = 'Agrega al menos un servicio a la cotización.';
        }

        $lineas = [];
        $subtotalCotizacion = 0.0;

        if (empty($errors)) {
            try {
                $stmtServicio = db()->prepare('SELECT id, nombre, monto FROM servicios WHERE id = ? LIMIT 1');

                foreach ($serviciosIds as $idx => $servicioRaw) {
                    $servicioId = (int) $servicioRaw;
                    $periodicidad = trim((string) ($periodicidades[$idx] ?? 'Mensual'));
                    $descuentoLinea = (float) ($descuentos[$idx] ?? 0);

                    if ($servicioId <= 0) {
                        $errors[] = 'Hay un servicio inválido en la cotización.';
                        continue;
                    }

                    $stmtServicio->execute([$servicioId]);
                    $servicio = $stmtServicio->fetch() ?: null;
                    if (!$servicio) {
                        $errors[] = 'Uno de los servicios seleccionados no existe.';
                        continue;
                    }

                    $fechaVencimiento = calcular_fecha_vencimiento($fechaRegistro, $periodicidad);
                    if ($fechaVencimiento === null) {
                        $errors[] = 'Periodo inválido para el servicio ' . ($servicio['nombre'] ?? '#'.$servicioId) . '.';
                        continue;
                    }

                    $monto = (float) ($servicio['monto'] ?? 0);
                    if ($descuentoLinea < 0) {
                        $descuentoLinea = 0;
                    }
                    if ($descuentoLinea > $monto) {
                        $descuentoLinea = $monto;
                    }
                    $totalLinea = $monto - $descuentoLinea;

                    $lineas[] = [
                        'servicio_id' => $servicioId,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'tiempo_servicio' => $periodicidad,
                        'subtotal' => $monto,
                        'descuento_monto' => $descuentoLinea,
                        'total' => $totalLinea,
                    ];
                    $subtotalCotizacion += $monto;
                }
            } catch (Exception $e) {
                $errors[] = 'No se pudieron validar los servicios seleccionados.';
            } catch (Error $e) {
                $errors[] = 'No se pudieron validar los servicios seleccionados.';
            }
        }

        if (empty($errors) && !empty($lineas)) {
            try {
                $codigoCotizacion = 'COT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                db()->beginTransaction();
                $stmtInsert = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id, codigo_cotizacion, fecha_registro, tiempo_servicio, fecha_vencimiento, enviar_correo, nota_cotizacion, subtotal, descuento_monto, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                foreach ($lineas as $linea) {
                    $stmtInsert->execute([
                        $clienteId,
                        $linea['servicio_id'],
                        $codigoCotizacion,
                        $fechaRegistro,
                        $linea['tiempo_servicio'],
                        $linea['fecha_vencimiento'],
                        $enviarCorreo,
                        $notaCotizacion !== '' ? $notaCotizacion : null,
                        $linea['subtotal'],
                        $linea['descuento_monto'],
                        $linea['total'],
                    ]);
                }
                db()->commit();
                redirect('clientes-servicios-asociar.php?success=quote');
            } catch (Exception $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errorMessage = 'No se pudo guardar la cotización.';
            } catch (Error $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errorMessage = 'No se pudo guardar la cotización.';
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
    $sqlAsociaciones = 'SELECT cs.id,
                               c.codigo AS cliente_codigo,
                               c.nombre AS cliente,
                               s.nombre AS servicio,
                               cs.codigo_cotizacion,
                               cs.fecha_registro,
                               cs.tiempo_servicio,
                               cs.fecha_vencimiento,
                               cs.subtotal,
                               cs.descuento_monto,
                               cs.total,
                               cs.enviar_correo,
                               cs.nota_cotizacion
                        FROM clientes_servicios cs
                        JOIN clientes c ON c.id = cs.cliente_id
                        JOIN servicios s ON s.id = cs.servicio_id';

    if ($filtroTexto !== '') {
        $sqlAsociaciones .= ' WHERE c.nombre LIKE :q OR c.codigo LIKE :q OR s.nombre LIKE :q';
    }
    $sqlAsociaciones .= ' ORDER BY cs.id DESC';

    $stmtAsociaciones = db()->prepare($sqlAsociaciones);
    if ($filtroTexto !== '') {
        $stmtAsociaciones->execute(['q' => '%' . $filtroTexto . '%']);
    } else {
        $stmtAsociaciones->execute();
    }
    $asociaciones = $stmtAsociaciones->fetchAll();
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

                <?php $flowCurrentStep = 'asociaciones'; include('partials/flow-quick-nav.php'); ?>

            <?php if ($success === '1') : ?><div class="alert alert-success">Asociación creada correctamente.</div><?php endif; ?>
            <?php if ($success === 'deleted') : ?><div class="alert alert-success">Asociación eliminada correctamente.</div><?php endif; ?>
            <?php if ($success === 'updated') : ?><div class="alert alert-success">Asociación actualizada correctamente.</div><?php endif; ?>
            <?php if ($success === 'quote') : ?><div class="alert alert-success">Cotización guardada con servicios asociados.</div><?php endif; ?>
            <?php if ($errorMessage !== '') : ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?>
                        <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Nueva cotización de servicios</h5></div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="create_quote">
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
                            <label class="form-label" for="fecha-registro">Fecha de cotización</label>
                            <input type="date" id="fecha-registro" name="fecha_registro" class="form-control" value="<?php echo htmlspecialchars($_POST['fecha_registro'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="enviar-correo" name="enviar_correo" value="1" <?php echo isset($_POST['enviar_correo']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enviar-correo">Enviar correo al cliente con servicios asociados</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Servicios cotizados</label>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm align-middle mb-0" id="tabla-cotizacion">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Servicio</th>
                                            <th>Periodicidad</th>
                                            <th>Precio</th>
                                            <th>Descuento</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cotizacion-body"></tbody>
                                </table>
                            </div>
                            <button type="button" id="agregar-linea" class="btn btn-outline-primary btn-sm mt-2">Agregar servicio</button>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="nota-cotizacion">Nota</label>
                            <textarea id="nota-cotizacion" name="nota_cotizacion" class="form-control" rows="3" placeholder="Condiciones, observaciones o términos de la cotización..."><?php echo htmlspecialchars($_POST['nota_cotizacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <strong>Total cotización:</strong> <span id="total-cotizacion">$0,00</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Guardar cotización</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Asociaciones registradas</h5>
                    <span class="badge text-bg-primary"><?php echo count($asociaciones); ?> registros</span>
                </div>
                <div class="card-body border-bottom">
                    <form method="get" class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="q" class="form-control" placeholder="Buscar por cliente, código o servicio" value="<?php echo htmlspecialchars($filtroTexto, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                            <a href="clientes-servicios-asociar.php" class="btn btn-outline-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Cotización</th><th>Cliente</th><th>Servicio</th><th>Tiempo</th><th>Subtotal</th><th>Descuento</th><th>Total</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                        <?php if (empty($asociaciones)) : ?>
                            <tr><td colspan="8" class="text-center text-muted">No hay asociaciones registradas.</td></tr>
                        <?php else : foreach ($asociaciones as $item) : ?>
                            <tr>
                                <td>
                                    <div><?php echo htmlspecialchars($item['codigo_cotizacion'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small class="text-muted"><?php echo !empty($item['fecha_registro']) ? htmlspecialchars(date('d/m/Y', strtotime((string) $item['fecha_registro'])), ENT_QUOTES, 'UTF-8') : '-'; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars(($item['cliente_codigo'] ?? '') . ' - ' . $item['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['tiempo_servicio'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format((float) ($item['subtotal'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format((float) ($item['descuento_monto'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div>$<?php echo htmlspecialchars(number_format((float) ($item['total'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if ((int) ($item['enviar_correo'] ?? 0) === 1) : ?><small class="text-success">Correo marcado</small><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
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
    const servicios = <?php echo json_encode(array_map(static function ($servicio) {
        return [
            'id' => (int) ($servicio['id'] ?? 0),
            'nombre' => (string) ($servicio['nombre'] ?? 'Servicio'),
            'monto' => (float) ($servicio['monto'] ?? 0),
        ];
    }, $servicios), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    const body = document.getElementById('cotizacion-body');
    const btnAgregar = document.getElementById('agregar-linea');
    const totalCotizacion = document.getElementById('total-cotizacion');

    if (!body || !btnAgregar || !totalCotizacion) {
        return;
    }

    function formatoMoneda(valor) {
        return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 2 }).format(Number(valor || 0));
    }

    function construirOpcionesServicio() {
        let html = '<option value="">Selecciona un servicio</option>';
        servicios.forEach((servicio) => {
            html += `<option value="${servicio.id}" data-monto="${servicio.monto}">${servicio.nombre} · ${formatoMoneda(servicio.monto)}</option>`;
        });
        return html;
    }

    function recalcularFila(row) {
        const selectServicio = row.querySelector('.js-servicio');
        const inputDescuento = row.querySelector('.js-descuento');
        const totalEl = row.querySelector('.js-total-linea');
        const precioEl = row.querySelector('.js-precio');

        const option = selectServicio.options[selectServicio.selectedIndex];
        const monto = Number(option?.dataset?.monto || 0);
        let descuento = Number(inputDescuento.value || 0);

        if (descuento < 0) descuento = 0;
        if (descuento > monto) descuento = monto;
        inputDescuento.value = descuento.toFixed(2);

        const total = monto - descuento;
        precioEl.textContent = formatoMoneda(monto);
        totalEl.textContent = formatoMoneda(total);
    }

    function recalcularTotales() {
        let total = 0;
        body.querySelectorAll('tr').forEach((row) => {
            const selectServicio = row.querySelector('.js-servicio');
            const inputDescuento = row.querySelector('.js-descuento');
            const option = selectServicio.options[selectServicio.selectedIndex];
            const monto = Number(option?.dataset?.monto || 0);
            const descuento = Number(inputDescuento.value || 0);
            total += Math.max(0, monto - descuento);
        });
        totalCotizacion.textContent = formatoMoneda(total);
    }

    function agregarLinea() {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="servicio_id[]" class="form-select form-select-sm js-servicio" required>
                    ${construirOpcionesServicio()}
                </select>
            </td>
            <td>
                <select name="tiempo_servicio[]" class="form-select form-select-sm" required>
                    <option value="Mensual">Mensual</option>
                    <option value="Bimestral">Bimestral</option>
                    <option value="Trimestral">Trimestral</option>
                    <option value="Semestral">Semestral</option>
                    <option value="Anual">Anual</option>
                </select>
            </td>
            <td class="js-precio">$0</td>
            <td><input type="number" name="descuento[]" class="form-control form-control-sm js-descuento" min="0" step="0.01" value="0"></td>
            <td class="js-total-linea">$0</td>
            <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger js-eliminar">Quitar</button></td>
        `;

        const selectServicio = row.querySelector('.js-servicio');
        const inputDescuento = row.querySelector('.js-descuento');
        const btnEliminar = row.querySelector('.js-eliminar');

        const sync = () => {
            recalcularFila(row);
            recalcularTotales();
        };

        selectServicio.addEventListener('change', sync);
        inputDescuento.addEventListener('input', sync);
        btnEliminar.addEventListener('click', () => {
            row.remove();
            recalcularTotales();
        });

        body.appendChild(row);
        sync();
    }

    btnAgregar.addEventListener('click', agregarLinea);
    agregarLinea();
})();
</script>
</body>
</html>
