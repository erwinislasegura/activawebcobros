<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

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

    if ($action === 'create') {
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $servicioId = (int) ($_POST['servicio_id'] ?? 0);

        if ($clienteId <= 0) {
            $errors[] = 'Selecciona un cliente válido.';
        }
        if ($servicioId <= 0) {
            $errors[] = 'Selecciona un servicio válido.';
        }

        if (empty($errors)) {
            try {
                $stmtExists = db()->prepare('SELECT COUNT(*) FROM clientes_servicios WHERE cliente_id = ? AND servicio_id = ?');
                $stmtExists->execute([$clienteId, $servicioId]);
                if ((int) $stmtExists->fetchColumn() > 0) {
                    $errors[] = 'Esta asociación ya existe.';
                } else {
                    $stmtInsert = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id) VALUES (?, ?)');
                    $stmtInsert->execute([$clienteId, $servicioId]);
                    redirect('clientes-servicios-asociar.php?success=1');
                }
            } catch (Exception $e) {
                $errorMessage = 'No se pudo guardar la asociación.';
            } catch (Error $e) {
                $errorMessage = 'No se pudo guardar la asociación.';
            }
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
            <?php if ($errorMessage !== '') : ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?>
                        <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Nueva asociación</h5></div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="create">
                        <div class="col-md-6">
                            <label class="form-label">Cliente</label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">Selecciona un cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo (int) $cliente['id']; ?>"><?php echo htmlspecialchars(($cliente['codigo'] ?? '') . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
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
                            <button type="submit" class="btn btn-primary">Guardar asociación</button>
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
                        <thead><tr><th>Cliente</th><th>Servicio</th><th>Fecha</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                        <?php if (empty($asociaciones)) : ?>
                            <tr><td colspan="4" class="text-center text-muted">No hay asociaciones registradas.</td></tr>
                        <?php else : foreach ($asociaciones as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($item['cliente_codigo'] ?? '') . ' - ' . $item['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $item['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end">
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta asociación?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
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
