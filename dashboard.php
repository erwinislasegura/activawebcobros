<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

if (!isset($_SESSION['user'])) {
    redirect('auth-2-sign-in.php');
}

$stats = [
    'events_total' => 0,
    'events_upcoming' => 0,
    'authorities_total' => 0,
    'users_total' => 0,
    'validation_requests' => 0,
    'validation_responded' => 0,
    'validation_pending' => 0,
];
$upcomingEvents = [];
$recentValidations = [];
$eventsByMonth = [];
$validationsByMonth = [];
$authoritiesByGroup = [];
$monthLabels = [];

try {
    $stats['events_total'] = (int) db()->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $stats['events_upcoming'] = (int) db()->query('SELECT COUNT(*) FROM events WHERE fecha_inicio >= NOW()')->fetchColumn();
    $stats['authorities_total'] = (int) db()->query('SELECT COUNT(*) FROM authorities')->fetchColumn();
    $stats['users_total'] = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $stats['validation_requests'] = (int) db()->query('SELECT COUNT(*) FROM event_authority_requests')->fetchColumn();
    $stats['validation_responded'] = (int) db()->query('SELECT COUNT(*) FROM event_authority_requests WHERE estado = "respondido"')->fetchColumn();
    $stats['validation_pending'] = max(0, $stats['validation_requests'] - $stats['validation_responded']);

    $stmt = db()->prepare('SELECT titulo, fecha_inicio, fecha_fin, ubicacion, tipo FROM events ORDER BY fecha_inicio DESC LIMIT 5');
    $stmt->execute();
    $upcomingEvents = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT e.titulo, r.destinatario_nombre, r.destinatario_correo, r.responded_at
         FROM event_authority_requests r
         INNER JOIN events e ON e.id = r.event_id
         WHERE r.estado = "respondido"
         ORDER BY r.responded_at DESC
         LIMIT 5'
    );
    $stmt->execute();
    $recentValidations = $stmt->fetchAll();

    $stmt = db()->prepare(
        'SELECT DATE_FORMAT(fecha_inicio, "%Y-%m") AS month_key, COUNT(*) AS total
         FROM events
         WHERE fecha_inicio >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
         GROUP BY month_key
         ORDER BY month_key'
    );
    $stmt->execute();
    $eventsByMonth = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = db()->prepare(
        'SELECT DATE_FORMAT(responded_at, "%Y-%m") AS month_key, COUNT(*) AS total
         FROM event_authority_requests
         WHERE estado = "respondido" AND responded_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
         GROUP BY month_key
         ORDER BY month_key'
    );
    $stmt->execute();
    $validationsByMonth = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = db()->prepare(
        'SELECT COALESCE(g.nombre, "Sin grupo") AS grupo, COUNT(*) AS total
         FROM authorities a
         LEFT JOIN authority_groups g ON g.id = a.group_id
         GROUP BY grupo
         ORDER BY total DESC'
    );
    $stmt->execute();
    $authoritiesByGroup = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
$eventsSeries = [];
$validationsSeries = [];
for ($i = 5; $i >= 0; $i--) {
    $date = new DateTime("-{$i} months");
    $monthKey = $date->format('Y-m');
    $monthLabel = ($monthsMap[$date->format('m')] ?? $date->format('m')) . ' ' . $date->format('Y');
    $monthLabels[] = $monthLabel;
    $eventsSeries[] = isset($eventsByMonth[$monthKey]) ? (int) $eventsByMonth[$monthKey] : 0;
    $validationsSeries[] = isset($validationsByMonth[$monthKey]) ? (int) $validationsByMonth[$monthKey] : 0;
}

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

                <?php $subtitle = "Resumen general"; $title = "Panel de control"; include('partials/page-title.php'); ?>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm dashboard-hero dashboard-card">
                            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">Panel municipal</h4>
                                    <p class="text-muted mb-0">Visión rápida del estado de eventos, autoridades y validaciones.</p>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary">Eventos próximos: <?php echo (int) $stats['events_upcoming']; ?></span>
                                    <span class="badge bg-success-subtle text-success">Validaciones respondidas: <?php echo (int) $stats['validation_responded']; ?></span>
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
                                        <p class="text-muted mb-1">Eventos registrados</p>
                                        <h4 class="mb-0"><?php echo (int) $stats['events_total']; ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                        <i class="ti ti-calendar-event fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Próximos: <?php echo (int) $stats['events_upcoming']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Autoridades</p>
                                        <h4 class="mb-0"><?php echo (int) $stats['authorities_total']; ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                                        <i class="ti ti-users fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Catálogo activo de autoridades municipales.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Usuarios activos</p>
                                        <h4 class="mb-0"><?php echo (int) $stats['users_total']; ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                                        <i class="ti ti-user-circle fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Accesos habilitados en el sistema.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 dashboard-stat-col">
                        <div class="card border-0 shadow-sm dashboard-stat dashboard-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Validaciones pendientes</p>
                                        <h4 class="mb-0"><?php echo (int) $stats['validation_pending']; ?></h4>
                                    </div>
                                    <span class="avatar-sm rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                                        <i class="ti ti-alert-triangle fs-4"></i>
                                    </span>
                                </div>
                                <div class="mt-3 small text-muted">Solicitudes totales: <?php echo (int) $stats['validation_requests']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Eventos por mes</h5>
                                <span class="text-muted small">Últimos 6 meses</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-fixed chart-sm">
                                    <canvas id="eventsMonthlyChart" aria-label="Eventos por mes" role="img"></canvas>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-2 small text-muted">
                                    <span><span class="fw-semibold text-primary">Eventos:</span> <?php echo (int) $stats['events_total']; ?></span>
                                    <span><span class="fw-semibold text-success">Validaciones respondidas:</span> <?php echo (int) $stats['validation_responded']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Estado de validaciones</h5>
                                <span class="text-muted small">Solicitudes</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-fixed chart-md">
                                    <canvas id="validationStatusChart" aria-label="Estado de validaciones" role="img"></canvas>
                                </div>
                                <?php
                                $validationRate = $stats['validation_requests'] > 0
                                    ? round(($stats['validation_responded'] / $stats['validation_requests']) * 100)
                                    : 0;
                                ?>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                        <span>Tasa de respuesta</span>
                                        <span class="fw-semibold text-success"><?php echo $validationRate; ?>%</span>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $validationRate; ?>%;" aria-valuenow="<?php echo $validationRate; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-xl-3">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Autoridades por grupo</h5>
                                <span class="text-muted small">Distribución</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-fixed chart-lg">
                                    <canvas id="authoritiesGroupChart" aria-label="Autoridades por grupo" role="img"></canvas>
                                </div>
                                <div class="small text-muted mt-2">Total autoridades: <?php echo (int) $stats['authorities_total']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="card border-0 shadow-sm h-100 dashboard-card">
                            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0">
                                <h5 class="card-title mb-0">Eventos recientes</h5>
                                <a class="btn btn-sm btn-outline-primary" href="eventos-lista.php">Ver todos</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($upcomingEvents)) : ?>
                                    <div class="text-muted">No hay eventos registrados todavía.</div>
                                <?php else : ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($upcomingEvents as $event) : ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex align-items-start justify-content-between gap-3">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                        <div class="text-muted small">
                                                            <?php echo htmlspecialchars($event['ubicacion'], ENT_QUOTES, 'UTF-8'); ?>
                                                            · <?php echo htmlspecialchars($event['tipo'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="badge text-bg-light">
                                                            <?php echo htmlspecialchars($event['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </div>
                                                        <div class="text-muted small mt-1">
                                                            <?php echo htmlspecialchars($event['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </div>
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
                                <h5 class="card-title mb-0">Validaciones recientes</h5>
                                <a class="btn btn-sm btn-outline-primary" href="eventos-procesados.php">Ver procesados</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentValidations)) : ?>
                                    <div class="text-muted">Aún no hay validaciones respondidas.</div>
                                <?php else : ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recentValidations as $validation) : ?>
                                            <div class="list-group-item px-0">
                                                <div class="d-flex align-items-start justify-content-between gap-2">
                                                    <div>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($validation['titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="text-muted small">
                                                            <?php echo htmlspecialchars($validation['destinatario_nombre'] ?: 'Sin nombre', ENT_QUOTES, 'UTF-8'); ?>
                                                            · <?php echo htmlspecialchars($validation['destinatario_correo'], ENT_QUOTES, 'UTF-8'); ?>
                                                        </div>
                                                    </div>
                                                    <span class="badge text-bg-success">Respondido</span>
                                                </div>
                                                <div class="text-muted small mt-1">Fecha: <?php echo htmlspecialchars($validation['responded_at'], ENT_QUOTES, 'UTF-8'); ?></div>
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
            const monthlyValues = <?php echo json_encode($eventsSeries, JSON_UNESCAPED_UNICODE); ?>;
            const validationsSeries = <?php echo json_encode($validationsSeries, JSON_UNESCAPED_UNICODE); ?>;
            const validationData = <?php echo json_encode([(int) $stats['validation_responded'], (int) $stats['validation_pending']], JSON_UNESCAPED_UNICODE); ?>;
            const groupLabels = <?php echo json_encode(array_keys($authoritiesByGroup), JSON_UNESCAPED_UNICODE); ?>;
            const groupValues = <?php echo json_encode(array_values($authoritiesByGroup), JSON_UNESCAPED_UNICODE); ?>;

            const monthlyChartEl = document.getElementById('eventsMonthlyChart');
            if (monthlyChartEl) {
                new Chart(monthlyChartEl, {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [
                            {
                                label: 'Eventos',
                                data: monthlyValues,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.12)',
                                tension: 0.35,
                                fill: true,
                                pointRadius: 3,
                                pointBackgroundColor: '#0d6efd',
                            },
                            {
                                label: 'Validaciones respondidas',
                                data: validationsSeries,
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

            const validationChartEl = document.getElementById('validationStatusChart');
            if (validationChartEl) {
                new Chart(validationChartEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['Respondidas', 'Pendientes'],
                        datasets: [
                            {
                                data: validationData,
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

            const groupChartEl = document.getElementById('authoritiesGroupChart');
            if (groupChartEl) {
                new Chart(groupChartEl, {
                    type: 'bar',
                    data: {
                        labels: groupLabels,
                        datasets: [
                            {
                                label: 'Autoridades',
                                data: groupValues,
                                backgroundColor: 'rgba(37, 99, 235, 0.2)',
                                borderColor: '#2563eb',
                                borderWidth: 1,
                                borderRadius: 8,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
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
