<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';

function enviar_correo_alta_servicio(string $destinatario, string $asunto, string $cuerpoHtml, string $fromEmail, string $fromName): bool
{
    if ($destinatario === '' || $fromEmail === '') {
        return false;
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . ($fromName !== '' ? $fromName : $fromEmail) . ' <' . $fromEmail . '>',
    ];

    return @mail($destinatario, $asunto, $cuerpoHtml, implode("\r\n", $headers));
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

    $columnasRequeridas = [
        'motivo' => 'ALTER TABLE clientes_servicios ADD COLUMN motivo TEXT NULL AFTER servicio_id',
        'info_importante' => 'ALTER TABLE clientes_servicios ADD COLUMN info_importante TEXT NULL AFTER motivo',
        'correo_enviado_at' => 'ALTER TABLE clientes_servicios ADD COLUMN correo_enviado_at DATETIME NULL AFTER info_importante',
    ];

    foreach ($columnasRequeridas as $columna => $sqlAlter) {
        $stmtColumna = db()->prepare('SHOW COLUMNS FROM clientes_servicios LIKE ?');
        $stmtColumna->execute([$columna]);
        if (!$stmtColumna->fetch()) {
            db()->exec($sqlAlter);
        }
    }
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios por cliente.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de servicios por cliente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($deleteId > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM clientes_servicios WHERE id = ?');
            $stmt->execute([$deleteId]);
            redirect('clientes-servicios.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar la asignación.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo eliminar la asignación.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $clienteId = (int) ($_POST['cliente_id'] ?? 0);
    $servicioId = (int) ($_POST['servicio_id'] ?? 0);
    $motivo = trim($_POST['motivo'] ?? '');
    $infoImportante = trim($_POST['info_importante'] ?? '');

    if ($clienteId <= 0) {
        $errors[] = 'Selecciona un cliente válido.';
    }
    if ($servicioId <= 0) {
        $errors[] = 'Selecciona un servicio válido.';
    }
    if ($motivo === '') {
        $errors[] = 'Debes indicar el motivo del alta del servicio.';
    }

    if (empty($errors)) {
        try {
            $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];
            $fromEmail = trim((string) ($correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? ''));
            $fromName = trim((string) ($correoConfig['from_nombre'] ?? ''));

            $stmtDetalle = db()->prepare(
                'SELECT c.nombre AS cliente_nombre,
                        c.correo AS cliente_correo,
                        s.nombre AS servicio_nombre,
                        s.monto AS servicio_monto
                 FROM clientes c
                 JOIN servicios s ON s.id = ?
                 WHERE c.id = ?
                 LIMIT 1'
            );
            $stmtDetalle->execute([$servicioId, $clienteId]);
            $detalle = $stmtDetalle->fetch();

            if (!$detalle) {
                $errorMessage = 'No se pudo obtener la información del cliente o servicio.';
            } elseif (empty($detalle['cliente_correo'])) {
                $errorMessage = 'El cliente no tiene correo configurado.';
            } elseif ($fromEmail === '') {
                $errorMessage = 'Configura el correo de envío en Notificaciones antes de dar de alta.';
            } else {
                db()->beginTransaction();

                $stmt = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id, motivo, info_importante) VALUES (?, ?, ?, ?)');
                $stmt->execute([
                    $clienteId,
                    $servicioId,
                    $motivo,
                    $infoImportante !== '' ? $infoImportante : null,
                ]);

                $municipalidad = get_municipalidad();
                $nombreMunicipalidad = trim((string) ($municipalidad['nombre'] ?? 'Nuestra institución'));
                if ($fromName === '') {
                    $fromName = $nombreMunicipalidad;
                }

                $asunto = 'Alta de servicio: ' . (string) ($detalle['servicio_nombre'] ?? 'Servicio');
                $montoTexto = '$' . number_format((float) ($detalle['servicio_monto'] ?? 0), 2, ',', '.');
                $cuerpoHtml = '
                    <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                        <p>Estimado/a <strong>' . htmlspecialchars((string) $detalle['cliente_nombre'], ENT_QUOTES, 'UTF-8') . '</strong>,</p>
                        <p>Junto con saludar, informamos que se ha dado de alta el siguiente servicio en su cuenta:</p>
                        <ul>
                            <li><strong>Servicio:</strong> ' . htmlspecialchars((string) $detalle['servicio_nombre'], ENT_QUOTES, 'UTF-8') . '</li>
                            <li><strong>Monto referencial:</strong> ' . htmlspecialchars($montoTexto, ENT_QUOTES, 'UTF-8') . '</li>
                            <li><strong>Motivo del alta:</strong> ' . nl2br(htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8')) . '</li>
                        </ul>
                        <p><strong>Información importante:</strong><br>' . ($infoImportante !== '' ? nl2br(htmlspecialchars($infoImportante, ENT_QUOTES, 'UTF-8')) : 'Para más detalles, comuníquese con nuestra oficina de atención al cliente.') . '</p>
                        <p>Este correo tiene carácter informativo y forma parte de nuestros registros de atención.</p>
                        <p>Atentamente,<br><strong>' . htmlspecialchars($nombreMunicipalidad, ENT_QUOTES, 'UTF-8') . '</strong></p>
                    </div>';

                $correoEnviado = enviar_correo_alta_servicio((string) $detalle['cliente_correo'], $asunto, $cuerpoHtml, $fromEmail, $fromName);

                if (!$correoEnviado) {
                    db()->rollBack();
                    $errorMessage = 'No se pudo enviar el correo de alta. Verifica la configuración de correo e inténtalo nuevamente.';
                } else {
                    $nuevoId = (int) db()->lastInsertId();
                    $stmtUpdate = db()->prepare('UPDATE clientes_servicios SET correo_enviado_at = NOW() WHERE id = ?');
                    $stmtUpdate->execute([$nuevoId]);
                    db()->commit();
                    redirect('clientes-servicios.php?success=1');
                }
            }
        } catch (Exception $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $errorMessage = 'No se pudo dar de alta el servicio para el cliente.';
        } catch (Error $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $errorMessage = 'No se pudo dar de alta el servicio para el cliente.';
        }
    }
}

$clientes = [];
$servicios = [];
$asignaciones = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre FROM clientes WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $servicios = db()->query('SELECT id, nombre, monto FROM servicios WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $asignaciones = db()->query(
        'SELECT cs.id,
                c.codigo AS cliente_codigo,
                c.nombre AS cliente,
                s.nombre AS servicio,
                s.monto AS monto,
                cs.motivo,
                cs.info_importante,
                cs.correo_enviado_at,
                cs.created_at
         FROM clientes_servicios cs
         JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar las altas de servicios.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar las altas de servicios.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Alta de servicios por cliente";
    include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Clientes";
                $title = "Dar de alta servicio";
                include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Servicio dado de alta y correo enviado correctamente.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Alta de servicio para cliente</h5>
                                <p class="text-muted mb-0">Registra el alta, el motivo y envía notificación formal al correo del cliente.</p>
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
                                            <label class="form-label" for="cliente-servicio">Cliente</label>
                                            <select id="cliente-servicio" name="cliente_id" class="form-select" required>
                                                <option value="">Selecciona un cliente</option>
                                                <?php foreach ($clientes as $cliente) : ?>
                                                    <option value="<?php echo (int) $cliente['id']; ?>">
                                                        <?php echo htmlspecialchars(($cliente['codigo'] ?? '') . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($clientes)) : ?>
                                                <small class="text-muted d-block mt-2">Primero registra clientes activos.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="servicio-cliente">Servicio</label>
                                            <select id="servicio-cliente" name="servicio_id" class="form-select" required>
                                                <option value="">Selecciona un servicio</option>
                                                <?php foreach ($servicios as $servicio) : ?>
                                                    <option value="<?php echo (int) $servicio['id']; ?>">
                                                        <?php echo htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($servicios)) : ?>
                                                <small class="text-muted d-block mt-2">Primero registra servicios activos.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="motivo-alta">Motivo del alta</label>
                                            <textarea id="motivo-alta" name="motivo" class="form-control" rows="3" required placeholder="Ej.: incorporación de plan de mantenimiento anual"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="info-importante">Información importante</label>
                                            <textarea id="info-importante" name="info_importante" class="form-control" rows="3" placeholder="Ej.: vigencia, condiciones, plazos de pago, contacto"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100" <?php echo empty($clientes) || empty($servicios) ? 'disabled' : ''; ?>>
                                                Dar de alta y enviar correo
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
                                    <h5 class="card-title mb-0">Altas registradas</h5>
                                    <p class="text-muted mb-0">Historial de servicios dados de alta por cliente.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($asignaciones); ?> altas</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th>Monto</th>
                                                <th>Motivo</th>
                                                <th>Información importante</th>
                                                <th>Correo enviado</th>
                                                <th>Creación</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($asignaciones)) : ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">No hay altas registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($asignaciones as $asignacion) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars(($asignacion['cliente_codigo'] ?? '') . ' - ' . $asignacion['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($asignacion['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>$<?php echo number_format((float) $asignacion['monto'], 2, ',', '.'); ?></td>
                                                        <td><?php echo nl2br(htmlspecialchars((string) ($asignacion['motivo'] ?? '-'), ENT_QUOTES, 'UTF-8')); ?></td>
                                                        <td><?php echo nl2br(htmlspecialchars((string) ($asignacion['info_importante'] ?? '-'), ENT_QUOTES, 'UTF-8')); ?></td>
                                                        <td><?php echo !empty($asignacion['correo_enviado_at']) ? htmlspecialchars(date('d/m/Y H:i', strtotime((string) $asignacion['correo_enviado_at'])), ENT_QUOTES, 'UTF-8') : 'Pendiente'; ?></td>
                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($asignacion['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <form method="post" class="d-inline" data-confirm="¿Eliminar el alta?">
                                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $asignacion['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                            </form>
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

            <?php include('partials/footer.php'); ?>

        </div>

    </div>

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
