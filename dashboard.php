<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

if (!isset($_SESSION['user'])) {
    redirect('auth-2-sign-in.php');
}

$stats = [
    'pending_amount' => 0.0,
    'paid_amount' => 0.0,
    'collections_total' => 0,
    'collections_paid' => 0,
    'collections_pending' => 0,
    'clients_total' => 0,
    'clients_active' => 0,
];
$upcomingCollections = [];
$recentPayments = [];
$collectionsByMonth = [];
$paymentsByMonth = [];
$monthLabels = [];

try {
    $stats['collections_total'] = (int) db()->query('SELECT COUNT(*) FROM cobros_servicios')->fetchColumn();
    $stats['collections_paid'] = (int) db()->query('SELECT COUNT(*) FROM pagos_clientes')->fetchColumn();
    $stats['collections_pending'] = max(0, $stats['collections_total'] - $stats['collections_paid']);
    $stats['pending_amount'] = (float) db()->query('SELECT COALESCE(SUM(monto), 0) FROM cobros_servicios WHERE estado <> "Pagado"')->fetchColumn();
    $stats['paid_amount'] = (float) db()->query('SELECT COALESCE(SUM(monto), 0) FROM pagos_clientes')->fetchColumn();
    $stats['clients_total'] = (int) db()->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
    $stats['clients_active'] = (int) db()->query('SELECT COUNT(*) FROM clientes WHERE estado = 1')->fetchColumn();

    $stmt = db()->prepare(
        'SELECT cliente, referencia, monto, fecha_cobro, estado
         FROM cobros_servicios
         WHERE estado <> "Pagado"
         ORDER BY fecha_cobro ASC
         LIMIT 5'
    );
    $stmt->execute();
    $upcomingCollections = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT c.cliente, p.monto, p.fecha_pago, p.metodo
         FROM pagos_clientes p
         LEFT JOIN cobros_servicios c ON c.id = p.cobro_id
         ORDER BY p.fecha_pago DESC
         LIMIT 5'
    );
    $stmt->execute();
    $recentPayments = $stmt->fetchAll();

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
         FROM pagos_clientes
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

include('partials/html.php');
?>

<head>
    <?php $title = "Panel"; include('partials/title-meta.php'); ?>

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

            <div class="container-fluid dashboard-compact">

                <?php $subtitle = "Resumen financiero"; $title = "Panel de cobros"; include('partials/page-title.php'); ?>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm dashboard-hero dashboard-card">
                            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">Estado general de cobros</h4>
                                    <p class="text-muted mb-0">Seguimiento de montos pendientes, pagos recibidos y salud de la cartera.</p>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-warning">Pendiente: <?php echo $moneyFormatter($stats['pending_amount']); ?></span>
                                    <span class="badge bg-success-subtle text-success">Pagado: <?php echo $moneyFormatter($stats['paid_amount']); ?></span>
                                </div>
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
                                        <p class="text-muted mb-1">Monto pendiente</p>
                                        <h4 class="mb-0"><?php echo $moneyFormatter($stats['pending_amount']); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                        <i class="ti ti-alert-circle fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Cobros en curso: <?php echo (int) $stats['collections_pending']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Monto pagado</p>
                                        <h4 class="mb-0"><?php echo $moneyFormatter($stats['paid_amount']); ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="ti ti-credit-card fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Pagos registrados: <?php echo (int) $stats['collections_paid']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">KPI de cobros</p>
                                        <?php
                                        $collectionTotalAmount = $stats['pending_amount'] + $stats['paid_amount'];
                                        $collectionRate = $collectionTotalAmount > 0
                                            ? round(($stats['paid_amount'] / $collectionTotalAmount) * 100)
                                            : 0;
                                        ?>
                                        <h4 class="mb-0"><?php echo $collectionRate; ?>%</h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i class="ti ti-chart-line fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Efectividad de pago sobre el monto total.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">KPI de clientes</p>
                                        <?php
                                        $clientRate = $stats['clients_total'] > 0
                                            ? round(($stats['clients_active'] / $stats['clients_total']) * 100)
                                            : 0;
                                        ?>
                                        <h4 class="mb-0"><?php echo $clientRate; ?>%</h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="ti ti-users-group fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Clientes activos: <?php echo (int) $stats['clients_active']; ?> de <?php echo (int) $stats['clients_total']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Cobros y pagos por mes</h5>
                                <span class="text-muted small">Últimos 6 meses</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-fixed chart-sm">
                                    <canvas id="collectionsMonthlyChart" aria-label="Cobros y pagos por mes" role="img"></canvas>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-2 small text-muted">
                                    <span><span class="fw-semibold text-primary">Cobros generados:</span> <?php echo (int) $stats['collections_total']; ?></span>
                                    <span><span class="fw-semibold text-success">Pagos registrados:</span> <?php echo (int) $stats['collections_paid']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Estado de cobros</h5>
                                <span class="text-muted small">Totales</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-fixed chart-md">
                                    <canvas id="collectionsStatusChart" aria-label="Estado de cobros" role="img"></canvas>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                        <span>Tasa de cobro</span>
                                        <span class="fw-semibold text-success"><?php echo $collectionRate; ?>%</span>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $collectionRate; ?>%;" aria-valuenow="<?php echo $collectionRate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Resumen de clientes</h5>
                                <span class="text-muted small">Actividad</span>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                            <span>Clientes activos</span>
                                            <span class="fw-semibold text-info"><?php echo (int) $stats['clients_active']; ?></span>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $clientRate; ?>%;" aria-valuenow="<?php echo $clientRate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 bg-light">
                                        <div class="text-muted small">Clientes inactivos</div>
                                        <div class="h5 mb-0"><?php echo max(0, $stats['clients_total'] - $stats['clients_active']); ?></div>
                                    </div>
                                    <div class="text-muted small">Total clientes: <?php echo (int) $stats['clients_total']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Cobros pendientes próximos</h5>
                                <a class="btn btn-sm btn-outline-primary" href="cobros-servicios-registros.php">Ver cobros</a>
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
                                                        <div class="badge text-bg-light">
                                                            <?php echo htmlspecialchars($collection['fecha_cobro'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </div>
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

                <div class="row g-2">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Pagos recientes</h5>
                                <a class="btn btn-sm btn-outline-primary" href="cobros-pagos.php">Ver pagos</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentPayments)) : ?>
                                    <div class="text-muted">Aún no hay pagos registrados.</div>
                                <?php else : ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recentPayments as $payment) : ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex align-items-start justify-content-between gap-2">
                                                    <div>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($payment['cliente'] ?: 'Cliente sin nombre', ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="text-muted small">
                                                            Método: <?php echo htmlspecialchars($payment['metodo'] ?: 'Sin método', ENT_QUOTES, 'UTF-8'); ?>
                                                        </div>
                                                    </div>
                                                    <span class="badge text-bg-success"><?php echo $moneyFormatter((float) $payment['monto']); ?></span>
                                                </div>
                                                <div class="text-muted small mt-1">Fecha: <?php echo htmlspecialchars($payment['fecha_pago'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
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
        .dashboard-hero {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(13, 110, 253, 0.02));
        }

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

        .progress-sm {
            height: 6px;
        }

        .chart-fixed {
            position: relative;
            width: 100%;
        }

        .chart-sm {
            height: 220px;
        }

        .chart-md {
            height: 260px;
        }

        .chart-lg {
            height: 280px;
        }

        @media (max-width: 767.98px) {
            .dashboard-stat {
                margin-bottom: 0;
            }

            .dashboard-stat-col {
                flex: 0 0 auto;
                width: 50%;
            }

            .chart-sm,
            .chart-md,
            .chart-lg {
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
            const collectionsData = <?php echo json_encode([(int) $stats['collections_paid'], (int) $stats['collections_pending']], JSON_UNESCAPED_UNICODE); ?>;

            const monthlyChartEl = document.getElementById('collectionsMonthlyChart');
            if (monthlyChartEl) {
                new Chart(monthlyChartEl, {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [
                            {
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

            const statusChartEl = document.getElementById('collectionsStatusChart');
            if (statusChartEl) {
                new Chart(statusChartEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pagados', 'Pendientes'],
                        datasets: [
                            {
                                data: collectionsData,
                                backgroundColor: ['#16a34a', '#f59e0b'],
                                borderWidth: 0,
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
                        cutout: '70%',
                    },
                });
            }

        });
    </script>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
