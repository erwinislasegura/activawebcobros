<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

if (!isset($_SESSION['user'])) {
    redirect('auth-2-sign-in.php');
}

$stats = [
    'pending_amount' => 0.0,
    'paid_amount' => 0.0,
    'paid_today_amount' => 0.0,
    'paid_month_amount' => 0.0,
    'collections_total' => 0,
    'collections_paid' => 0,
    'collections_pending' => 0,
    'due_today' => 0,
    'overdue' => 0,
    'clients_with_pending' => 0,
    'due_next_7_days_amount' => 0.0,
    'services_paid_total' => 0,
    'service_types_paid' => 0,
    'services_with_debt' => 0,
];
$upcomingCollections = [];
$recentPayments = [];
$probablePayments = [];
$paidServices = [];
$debtServices = [];
$collectionsByMonth = [];
$paymentsByMonth = [];
$monthLabels = [];

try {
    $stats['collections_total'] = (int) db()->query('SELECT COUNT(*) FROM cobros_servicios')->fetchColumn();
    $stats['collections_paid'] = (int) db()->query('SELECT COUNT(DISTINCT p.cobro_id) FROM pagos_clientes p INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id')->fetchColumn();
    $stats['collections_pending'] = max(0, $stats['collections_total'] - $stats['collections_paid']);
    $stats['pending_amount'] = (float) db()->query('SELECT COALESCE(SUM(cs.monto), 0) FROM cobros_servicios cs LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id WHERE p.id IS NULL')->fetchColumn();
    $stats['paid_amount'] = (float) db()->query('SELECT COALESCE(SUM(p.monto), 0) FROM pagos_clientes p INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id')->fetchColumn();
    $stats['paid_today_amount'] = (float) db()->query('SELECT COALESCE(SUM(p.monto), 0) FROM pagos_clientes p INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id WHERE DATE(p.fecha_pago) = CURDATE()')->fetchColumn();
    $stats['paid_month_amount'] = (float) db()->query('SELECT COALESCE(SUM(p.monto), 0) FROM pagos_clientes p INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id WHERE DATE_FORMAT(p.fecha_pago, "%Y-%m") = DATE_FORMAT(CURDATE(), "%Y-%m")')->fetchColumn();
    $stats['due_today'] = (int) db()->query('SELECT COUNT(*) FROM cobros_servicios cs LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id WHERE p.id IS NULL AND cs.fecha_cobro = CURDATE()')->fetchColumn();
    $stats['overdue'] = (int) db()->query('SELECT COUNT(*) FROM cobros_servicios cs LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id WHERE p.id IS NULL AND cs.fecha_cobro < CURDATE()')->fetchColumn();
    $stats['clients_with_pending'] = (int) db()->query('SELECT COUNT(DISTINCT cs.cliente_id) FROM cobros_servicios cs LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id WHERE p.id IS NULL AND cs.cliente_id IS NOT NULL')->fetchColumn();
    $stats['due_next_7_days_amount'] = (float) db()->query('SELECT COALESCE(SUM(cs.monto), 0) FROM cobros_servicios cs LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id WHERE p.id IS NULL AND cs.fecha_cobro BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)')->fetchColumn();
    $stats['services_paid_total'] = (int) db()->query('SELECT COUNT(DISTINCT p.cobro_id) FROM pagos_clientes p INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id')->fetchColumn();
    $stats['service_types_paid'] = (int) db()->query('SELECT COUNT(DISTINCT cs.servicio_id) FROM pagos_clientes p INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id WHERE cs.servicio_id IS NOT NULL')->fetchColumn();
    $stats['services_with_debt'] = (int) db()->query('SELECT COUNT(DISTINCT cs.servicio_id) FROM cobros_servicios cs LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id WHERE p.id IS NULL AND cs.servicio_id IS NOT NULL')->fetchColumn();

    $stmt = db()->prepare(
        'SELECT cliente, referencia, monto, fecha_cobro, estado
         FROM cobros_servicios
         WHERE NOT EXISTS (SELECT 1 FROM pagos_clientes p WHERE p.cobro_id = cobros_servicios.id)
         ORDER BY fecha_cobro ASC
         LIMIT 6'
    );
    $stmt->execute();
    $upcomingCollections = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT cs.cliente,
                cs.referencia,
                cs.fecha_cobro,
                cs.monto,
                COALESCE(s.nombre, "Servicio sin nombre") AS servicio
         FROM cobros_servicios cs
         LEFT JOIN servicios s ON s.id = cs.servicio_id
         WHERE NOT EXISTS (SELECT 1 FROM pagos_clientes p WHERE p.cobro_id = cs.id)
           AND cs.fecha_cobro BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
         ORDER BY cs.fecha_cobro ASC, cs.monto DESC
         LIMIT 8'
    );
    $stmt->execute();
    $probablePayments = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT c.cliente,
                c.referencia,
                p.monto,
                p.fecha_pago,
                p.metodo,
                COALESCE(s.nombre, "Servicio sin nombre") AS servicio
         FROM pagos_clientes p
         INNER JOIN cobros_servicios c ON c.id = p.cobro_id
         LEFT JOIN servicios s ON s.id = c.servicio_id
         ORDER BY p.fecha_pago DESC
         LIMIT 8'
    );
    $stmt->execute();
    $recentPayments = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT COALESCE(s.nombre, "Servicio sin nombre") AS servicio,
                COUNT(*) AS total_cobros,
                SUM(cs.monto) AS total_monto
         FROM cobros_servicios cs
         LEFT JOIN servicios s ON s.id = cs.servicio_id
         WHERE EXISTS (SELECT 1 FROM pagos_clientes p WHERE p.cobro_id = cs.id)
         GROUP BY servicio
         ORDER BY total_monto DESC
         LIMIT 5'
    );
    $stmt->execute();
    $paidServices = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT COALESCE(s.nombre, "Servicio sin nombre") AS servicio,
                COUNT(*) AS total_cobros,
                SUM(cs.monto) AS total_monto
         FROM cobros_servicios cs
         LEFT JOIN servicios s ON s.id = cs.servicio_id
         WHERE NOT EXISTS (SELECT 1 FROM pagos_clientes p WHERE p.cobro_id = cs.id)
         GROUP BY servicio
         ORDER BY total_monto DESC
         LIMIT 5'
    );
    $stmt->execute();
    $debtServices = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT DATE_FORMAT(fecha_cobro, "%Y-%m") AS month_key, SUM(monto) AS total
         FROM cobros_servicios
         WHERE fecha_cobro >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
         GROUP BY month_key
         ORDER BY month_key'
    );
    $stmt->execute();
    $collectionsByMonth = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = db()->prepare(
        'SELECT DATE_FORMAT(fecha_pago, "%Y-%m") AS month_key, SUM(monto) AS total
         FROM pagos_clientes p
         INNER JOIN cobros_servicios cs ON cs.id = p.cobro_id
         WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
         GROUP BY month_key
         ORDER BY month_key'
    );
    $stmt->execute();
    $paymentsByMonth = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
} catch (Error $e) {
}

$monthsMap = [
    '01' => 'Ene',
    '02' => 'Feb',
    '03' => 'Mar',
    '04' => 'Abr',
    '05' => 'May',
    '06' => 'Jun',
    '07' => 'Jul',
    '08' => 'Ago',
    '09' => 'Sep',
    '10' => 'Oct',
    '11' => 'Nov',
    '12' => 'Dic',
];
$monthLabels = [];
$collectionsSeries = [];
$paymentsSeries = [];
for ($i = 5; $i >= 0; $i--) {
    $date = new DateTime("-{$i} months");
    $monthKey = $date->format('Y-m');
    $monthLabel = ($monthsMap[$date->format('m')] ?? $date->format('m')) . ' ' . $date->format('Y');
    $monthLabels[] = $monthLabel;
    $collectionsSeries[] = isset($collectionsByMonth[$monthKey]) ? (float) $collectionsByMonth[$monthKey] : 0;
    $paymentsSeries[] = isset($paymentsByMonth[$monthKey]) ? (float) $paymentsByMonth[$monthKey] : 0;
}

$moneyFormatter = static function (float $value): string {
    return '$' . number_format($value, 2, ',', '.');
};

$collectionTotalAmount = $stats['pending_amount'] + $stats['paid_amount'];
$collectionRate = $collectionTotalAmount > 0
    ? round(($stats['paid_amount'] / $collectionTotalAmount) * 100)
    : 0;

include('partials/html.php');
?>

<head>
    <?php $title = "Panel"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">

            <div class="container-fluid dashboard-compact">

                <?php $subtitle = "Resumen financiero"; $title = "Panel de cobros"; include('partials/page-title.php'); ?>

                <div class="card border-0 shadow-sm dashboard-card mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="mb-0">Flujo rápido de trabajo</h6>
                                <small class="text-muted">Accesos directos a las vistas principales del menú activo.</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="clientes-crear.php">1) Alta cliente</a>
                                <a class="btn btn-sm btn-outline-primary" href="clientes-servicios-asociar.php">2) Asociar servicio</a>
                                <a class="btn btn-sm btn-outline-primary" href="cobros-servicios-registros.php">3) Registrar cobro</a>
                                <a class="btn btn-sm btn-outline-success" href="cobros-pagos.php">4) Registrar pago</a>
                                <a class="btn btn-sm btn-outline-warning" href="cobros-avisos.php">5) Enviar avisos</a>
                                <a class="btn btn-sm btn-outline-dark" href="cobros-totales.php">6) Ver totales</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Pendiente por cobrar</p>
                                        <h4 class="mb-0"><?php echo $moneyFormatter($stats['pending_amount']); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                        <i class="ti ti-alert-circle fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Deuda en próximos 7 días: <?php echo $moneyFormatter($stats['due_next_7_days_amount']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Pagado hoy</p>
                                        <h4 class="mb-0"><?php echo $moneyFormatter($stats['paid_today_amount']); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="ti ti-cash-banknote fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Pagado este mes: <?php echo $moneyFormatter($stats['paid_month_amount']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Servicios pagados</p>
                                        <h4 class="mb-0"><?php echo (int) $stats['services_paid_total']; ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i class="ti ti-rosette-discount-check fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Tipos de servicio pagados: <?php echo (int) $stats['service_types_paid']; ?> · Con deuda: <?php echo (int) $stats['services_with_debt']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Efectividad de cobro</p>
                                        <h4 class="mb-0"><?php echo $collectionRate; ?>%</h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="ti ti-chart-line fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Pagados: <?php echo (int) $stats['collections_paid']; ?> | Deudas: <?php echo (int) $stats['collections_pending']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Cobros y pagos por mes</h5>
                                <span class="text-muted small">Últimos 6 meses</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-fixed chart-sm">
                                    <canvas id="collectionsMonthlyChart" aria-label="Cobros y pagos por mes" role="img"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Control diario</h5>
                                <span class="text-muted small">Crítico</span>
                            </div>
                            <div class="card-body d-flex flex-column gap-2">
                                <div class="dashboard-kpi-item"><span class="text-muted">Cobros vencidos</span><strong class="text-danger"><?php echo (int) $stats['overdue']; ?></strong></div>
                                <div class="dashboard-kpi-item"><span class="text-muted">Cobros que vencen hoy</span><strong><?php echo (int) $stats['due_today']; ?></strong></div>
                                <div class="dashboard-kpi-item"><span class="text-muted">Clientes con deuda</span><strong><?php echo (int) $stats['clients_with_pending']; ?></strong></div>
                                <div class="dashboard-kpi-item"><span class="text-muted">Monto ya recaudado</span><strong class="text-success"><?php echo $moneyFormatter($stats['paid_amount']); ?></strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Próximos pagos probables (7 días)</h5>
                                <a class="btn btn-sm btn-outline-primary" href="cobros-servicios-registros.php">Gestionar</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($probablePayments)) : ?>
                                    <div class="text-muted">No hay pagos probables para los próximos 7 días.</div>
                                <?php else : ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($probablePayments as $row) : ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between gap-2">
                                                    <div>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($row['cliente'] ?: 'Cliente sin nombre', ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="text-muted small"><?php echo htmlspecialchars($row['servicio'], ENT_QUOTES, 'UTF-8'); ?> · Ref: <?php echo htmlspecialchars($row['referencia'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="badge text-bg-light"><?php echo htmlspecialchars((string) $row['fecha_cobro'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="small mt-1"><?php echo $moneyFormatter((float) $row['monto']); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Pagos realizados</h5>
                                <a class="btn btn-sm btn-outline-primary" href="cobros-pagos.php">Ver todos</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentPayments)) : ?>
                                    <div class="text-muted">Aún no hay pagos registrados.</div>
                                <?php else : ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recentPayments as $payment) : ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between gap-2">
                                                    <div>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($payment['cliente'] ?: 'Cliente sin nombre', ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="text-muted small"><?php echo htmlspecialchars($payment['servicio'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($payment['metodo'] ?: 'Sin método', ENT_QUOTES, 'UTF-8'); ?></div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge text-bg-success"><?php echo $moneyFormatter((float) $payment['monto']); ?></span>
                                                        <div class="text-muted small mt-1"><?php echo htmlspecialchars((string) $payment['fecha_pago'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">Servicios más pagados</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($paidServices)) : ?>
                                    <div class="text-muted">Aún no hay servicios pagados.</div>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr><th>Servicio</th><th class="text-end">Cobros</th><th class="text-end">Monto</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($paidServices as $row) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?php echo (int) $row['total_cobros']; ?></td>
                                                        <td class="text-end text-success"><?php echo $moneyFormatter((float) $row['total_monto']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">Servicios con deuda</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($debtServices)) : ?>
                                    <div class="text-muted">No hay deudas pendientes por servicio.</div>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr><th>Servicio</th><th class="text-end">Cobros</th><th class="text-end">Deuda</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($debtServices as $row) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?php echo (int) $row['total_cobros']; ?></td>
                                                        <td class="text-end text-warning"><?php echo $moneyFormatter((float) $row['total_monto']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Cobros pendientes generales</h5>
                                <a class="btn btn-sm btn-outline-primary" href="cobros-servicios-registros.php">Ver todos</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($upcomingCollections)) : ?>
                                    <div class="text-muted">No hay cobros pendientes por ahora.</div>
                                <?php else : ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($upcomingCollections as $collection) : ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex align-items-start justify-content-between gap-3">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($collection['cliente'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                        <div class="text-muted small">Referencia: <?php echo htmlspecialchars($collection['referencia'] ?: 'Sin referencia', ENT_QUOTES, 'UTF-8'); ?></div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="badge text-bg-light"><?php echo htmlspecialchars((string) $collection['fecha_cobro'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="text-muted small mt-1"><?php echo $moneyFormatter((float) $collection['monto']); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('partials/footer.php'); ?>

        </div>

    </div>

    <?php include('partials/customizer.php'); ?>

    <style>
        .dashboard-compact .card-body {
            padding: 16px;
        }

        .dashboard-compact .card-header {
            padding: 14px 16px 0;
        }

        .dashboard-stat h4 {
            letter-spacing: -0.02em;
        }

        .dashboard-stat .avatar-sm {
            height: 44px;
            width: 44px;
        }

        .dashboard-stat .avatar-sm i {
            font-size: 20px;
        }

        .dashboard-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }

        .dashboard-kpi-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #edf1f7;
            border-radius: 10px;
            padding: 10px 12px;
            background: #fff;
        }

        .chart-fixed {
            position: relative;
            width: 100%;
        }

        .chart-sm {
            height: 220px;
        }

        @media (max-width: 767.98px) {
            .dashboard-stat-col {
                flex: 0 0 auto;
                width: 50%;
            }

            .chart-sm {
                height: 220px;
            }
        }
    </style>

    <script src="assets/plugins/chartjs/chart.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const monthlyLabels = <?php echo json_encode($monthLabels, JSON_UNESCAPED_UNICODE); ?>;
            const collectionsSeries = <?php echo json_encode($collectionsSeries, JSON_UNESCAPED_UNICODE); ?>;
            const paymentsSeries = <?php echo json_encode($paymentsSeries, JSON_UNESCAPED_UNICODE); ?>;

            const monthlyChartEl = document.getElementById('collectionsMonthlyChart');
            if (monthlyChartEl) {
                new Chart(monthlyChartEl, {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [{
                                label: 'Cobros',
                                data: collectionsSeries,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.12)',
                                tension: 0.35,
                                fill: true,
                                pointRadius: 3,
                                pointBackgroundColor: '#0d6efd',
                            },
                            {
                                label: 'Pagos',
                                data: paymentsSeries,
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22, 163, 74, 0.08)',
                                tension: 0.35,
                                fill: false,
                                pointRadius: 3,
                                pointBackgroundColor: '#16a34a',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                },
                            },
                        },
                    },
                });
            }
        });
    </script>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
