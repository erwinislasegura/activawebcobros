<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';
$mailStatus = $_GET['mail'] ?? '';

function build_payment_receipt_email(array $data): string
{
    $cliente = htmlspecialchars((string) ($data['cliente'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8');
    $servicio = htmlspecialchars((string) ($data['servicio'] ?? '-'), ENT_QUOTES, 'UTF-8');
    $referencia = htmlspecialchars((string) ($data['referencia'] ?? '-'), ENT_QUOTES, 'UTF-8');
    $fechaPago = htmlspecialchars((string) ($data['fecha_pago'] ?? '-'), ENT_QUOTES, 'UTF-8');
    $metodo = htmlspecialchars((string) ($data['metodo'] ?? 'No informado'), ENT_QUOTES, 'UTF-8');
    $referenciaPago = htmlspecialchars((string) ($data['referencia_pago'] ?? 'No informada'), ENT_QUOTES, 'UTF-8');
    $monto = htmlspecialchars((string) ($data['monto'] ?? '$0,00'), ENT_QUOTES, 'UTF-8');
    $empresa = htmlspecialchars((string) ($data['empresa'] ?? 'Nuestro equipo'), ENT_QUOTES, 'UTF-8');

    return <<<HTML
<html>
<body style="font-family:Arial,Helvetica,sans-serif;background:#f8fafc;color:#111827;padding:24px;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
    <tr>
      <td style="padding:24px;">
        <h2 style="margin:0 0 12px 0;">¡Gracias por tu pago, {$cliente}!</h2>
        <p style="margin:0 0 16px 0;color:#4b5563;">Hemos recibido correctamente tu pago. A continuación compartimos el comprobante:</p>
        <table width="100%" cellpadding="8" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;">
          <tr><td style="color:#6b7280;">Servicio</td><td><strong>{$servicio}</strong></td></tr>
          <tr><td style="color:#6b7280;">Referencia cobro</td><td><strong>{$referencia}</strong></td></tr>
          <tr><td style="color:#6b7280;">Monto pagado</td><td><strong>{$monto}</strong></td></tr>
          <tr><td style="color:#6b7280;">Fecha de pago</td><td><strong>{$fechaPago}</strong></td></tr>
          <tr><td style="color:#6b7280;">Método</td><td><strong>{$metodo}</strong></td></tr>
          <tr><td style="color:#6b7280;">Referencia de pago</td><td><strong>{$referenciaPago}</strong></td></tr>
        </table>
        <p style="margin:16px 0 0 0;color:#4b5563;">Gracias por preferirnos.<br>{$empresa}</p>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS pagos_clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cobro_id INT NOT NULL,
            cliente_id INT NULL,
            servicio_id INT NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            fecha_pago DATE NOT NULL,
            metodo VARCHAR(60) NULL,
            referencia_pago VARCHAR(120) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_pagos_cobro (cobro_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de pagos.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de pagos.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $cobroId = (int) ($_POST['cobro_id'] ?? 0);
    $fechaPago = trim($_POST['fecha_pago'] ?? '');
    $metodo = trim($_POST['metodo'] ?? '');
    $referenciaPago = trim($_POST['referencia_pago'] ?? '');

    if ($cobroId <= 0) {
        $errors[] = 'Selecciona un cobro válido.';
    }
    if ($fechaPago === '') {
        $errors[] = 'Selecciona la fecha de pago.';
    }

    if (empty($errors)) {
        try {
            $stmtCobro = db()->prepare(
                'SELECT cs.id,
                        cs.cliente_id,
                        cs.servicio_id,
                        cs.monto,
                        cs.estado,
                        cs.referencia,
                        COALESCE(c.nombre, cs.cliente) AS cliente,
                        c.correo AS cliente_correo,
                        s.nombre AS servicio
                 FROM cobros_servicios cs
                 LEFT JOIN clientes c ON c.id = cs.cliente_id
                 LEFT JOIN servicios s ON s.id = cs.servicio_id
                 WHERE cs.id = ?
                 LIMIT 1'
            );
            $stmtCobro->execute([$cobroId]);
            $cobro = $stmtCobro->fetch();

            if (!$cobro) {
                $errors[] = 'El cobro seleccionado no existe.';
            } elseif (isset($cobro['estado']) && $cobro['estado'] === 'Pagado') {
                $errors[] = 'Este cobro ya está marcado como pagado.';
            } else {
                $stmtPago = db()->prepare('SELECT id FROM pagos_clientes WHERE cobro_id = ? LIMIT 1');
                $stmtPago->execute([$cobroId]);
                if ($stmtPago->fetchColumn()) {
                    $errors[] = 'Ya existe un pago registrado para este cobro.';
                }
            }

            if (empty($errors)) {
                $stmtInsert = db()->prepare('INSERT INTO pagos_clientes (cobro_id, cliente_id, servicio_id, monto, fecha_pago, metodo, referencia_pago) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmtInsert->execute([
                    $cobroId,
                    $cobro['cliente_id'] ?? null,
                    $cobro['servicio_id'] ?? null,
                    $cobro['monto'] ?? 0,
                    $fechaPago,
                    $metodo !== '' ? $metodo : null,
                    $referenciaPago !== '' ? $referenciaPago : null,
                ]);

                $stmtUpdate = db()->prepare('UPDATE cobros_servicios SET estado = ? WHERE id = ?');
                $stmtUpdate->execute(['Pagado', $cobroId]);

                $mailSent = false;
                if (!empty($cobro['cliente_correo']) && filter_var((string) $cobro['cliente_correo'], FILTER_VALIDATE_EMAIL)) {
                    $municipalidad = get_municipalidad();
                    try {
                        $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];
                    } catch (Exception $e) {
                        $correoConfig = [];
                    } catch (Error $e) {
                        $correoConfig = [];
                    }

                    $fromEmail = $correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? '';
                    $fromName = $correoConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? 'Soporte');

                    if ($fromEmail !== '' && filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                        $subject = 'Comprobante de pago - ' . ($cobro['servicio'] ?? 'Servicio');
                        $bodyHtml = build_payment_receipt_email([
                            'cliente' => $cobro['cliente'] ?? 'Cliente',
                            'servicio' => $cobro['servicio'] ?? '-',
                            'referencia' => $cobro['referencia'] ?? '-',
                            'monto' => '$' . number_format((float) ($cobro['monto'] ?? 0), 2, ',', '.'),
                            'fecha_pago' => date('d/m/Y', strtotime($fechaPago)),
                            'metodo' => $metodo !== '' ? $metodo : 'No informado',
                            'referencia_pago' => $referenciaPago !== '' ? $referenciaPago : 'No informada',
                            'empresa' => $municipalidad['nombre'] ?? 'Nuestro equipo',
                        ]);
                        $headers = [
                            'MIME-Version: 1.0',
                            'Content-type: text/html; charset=UTF-8',
                            'From: ' . ($fromName !== '' ? $fromName : $fromEmail) . ' <' . $fromEmail . '>',
                        ];
                        $mailSent = @mail((string) $cobro['cliente_correo'], $subject, $bodyHtml, implode("\r\n", $headers));
                    }
                }

                redirect('cobros-pagos.php?success=1&mail=' . ($mailSent ? '1' : '0'));
            }
        } catch (Exception $e) {
            $errorMessage = 'No se pudo registrar el pago.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo registrar el pago.';
        }
    }
}

$cobrosPendientes = [];
$pagos = [];
try {
    $cobrosPendientes = db()->query(
        'SELECT cs.id,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                s.nombre AS servicio,
                cs.referencia,
                cs.monto
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         WHERE cs.estado <> "Pagado"
         ORDER BY cs.id DESC'
    )->fetchAll();

    $pagos = db()->query(
        'SELECT p.id,
                p.monto,
                p.fecha_pago,
                p.metodo,
                p.referencia_pago,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                s.nombre AS servicio,
                cs.referencia
         FROM pagos_clientes p
         JOIN cobros_servicios cs ON cs.id = p.cobro_id
         LEFT JOIN clientes c ON c.id = p.cliente_id
         LEFT JOIN servicios s ON s.id = p.servicio_id
         ORDER BY p.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los pagos.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los pagos.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Registrar pago"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Cobros de servicios"; $title = "Registrar pago"; include('partials/page-title.php'); ?>

                <?php if ($success === '1') : ?>
                    <div class="alert alert-success">Pago registrado correctamente.</div>
                <?php endif; ?>

                <?php if ($success === '1' && $mailStatus === '1') : ?>
                    <div class="alert alert-info">Comprobante enviado al correo del cliente.</div>
                <?php elseif ($success === '1' && $mailStatus === '0') : ?>
                    <div class="alert alert-warning">Pago registrado, pero no se pudo enviar el correo de comprobante.</div>
                <?php endif; ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Nuevo pago</h5>
                                <p class="text-muted mb-0">Selecciona un cobro pendiente para evitar doble digitación.</p>
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
                                            <label class="form-label" for="pago-cobro">Cobro pendiente</label>
                                            <select id="pago-cobro" name="cobro_id" class="form-select" required>
                                                <option value="">Selecciona un cobro</option>
                                                <?php foreach ($cobrosPendientes as $cobro) : ?>
                                                    <option value="<?php echo (int) $cobro['id']; ?>"
                                                        data-cliente="<?php echo htmlspecialchars($cobro['cliente'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-servicio="<?php echo htmlspecialchars($cobro['servicio'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-referencia="<?php echo htmlspecialchars($cobro['referencia'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-monto="<?php echo htmlspecialchars((string) $cobro['monto'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($cobro['cliente'] . ' - ' . $cobro['servicio'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (empty($cobrosPendientes)) : ?>
                                                <small class="text-muted d-block mt-2">No hay cobros pendientes disponibles.</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-fecha">Fecha de pago</label>
                                            <input type="date" id="pago-fecha" name="fecha_pago" class="form-control" value="<?php echo htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-cliente">Cliente</label>
                                            <input type="text" id="pago-cliente" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-servicio">Servicio</label>
                                            <input type="text" id="pago-servicio" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-referencia">Referencia del cobro</label>
                                            <input type="text" id="pago-referencia" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-monto">Monto</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" id="pago-monto" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-metodo">Método de pago</label>
                                            <select id="pago-metodo" name="metodo" class="form-select">
                                                <option value="">Selecciona un método</option>
                                                <option value="Transferencia">Transferencia</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Tarjeta">Tarjeta</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="pago-referencia-pago">Referencia de pago</label>
                                            <input type="text" id="pago-referencia-pago" name="referencia_pago" class="form-control" placeholder="Comprobante, transacción, etc.">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100" <?php echo empty($cobrosPendientes) ? 'disabled' : ''; ?>>Registrar pago</button>
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
                                    <h5 class="card-title mb-0">Pagos registrados</h5>
                                    <p class="text-muted mb-0">Listado de pagos ingresados en el sistema.</p>
                                </div>
                                <span class="badge text-bg-primary"><?php echo count($pagos); ?> pagos</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th>Referencia</th>
                                                <th>Monto</th>
                                                <th>Fecha pago</th>
                                                <th>Método</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($pagos)) : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No hay pagos registrados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($pagos as $pago) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($pago['cliente'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($pago['servicio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($pago['referencia'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>$<?php echo number_format((float) $pago['monto'], 2, ',', '.'); ?></td>
                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pago['fecha_pago'] ?? 'now')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($pago['metodo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
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
            const cobroSelect = document.getElementById('pago-cobro');
            const clienteInput = document.getElementById('pago-cliente');
            const servicioInput = document.getElementById('pago-servicio');
            const referenciaInput = document.getElementById('pago-referencia');
            const montoInput = document.getElementById('pago-monto');

            function syncCobroInfo() {
                if (!cobroSelect) {
                    return;
                }
                const selected = cobroSelect.options[cobroSelect.selectedIndex];
                if (!selected) {
                    return;
                }
                clienteInput.value = selected.getAttribute('data-cliente') || '';
                servicioInput.value = selected.getAttribute('data-servicio') || '';
                referenciaInput.value = selected.getAttribute('data-referencia') || '';
                montoInput.value = selected.getAttribute('data-monto') || '';
            }

            if (cobroSelect) {
                cobroSelect.addEventListener('change', syncCobroInfo);
                syncCobroInfo();
            }
        })();
    </script>

</body>

</html>
