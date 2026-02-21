<?php
require __DIR__ . '/app/bootstrap.php';

$calendarEvents = [];
$eventMap = [];

$registerEvent = static function (array &$map, string $fecha, string $tipo, string $cliente, string $servicio, string $color, int $cobroId): void {
    if ($fecha === '') {
        return;
    }

    $mapKey = implode('|', [$fecha, strtolower($cliente), strtolower($servicio)]);

    if (!isset($map[$mapKey])) {
        $map[$mapKey] = [
            'id' => 'cobro-' . $cobroId . '-' . md5($mapKey),
            'title' => sprintf('%s · %s', $cliente, $servicio),
            'start' => $fecha,
            'allDay' => true,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'tipos' => [],
                'cliente' => $cliente,
                'servicio' => $servicio,
                'fecha' => $fecha,
            ],
        ];
    }

    if (!in_array($tipo, $map[$mapKey]['extendedProps']['tipos'], true)) {
        $map[$mapKey]['extendedProps']['tipos'][] = $tipo;
    }

    $map[$mapKey]['extendedProps']['tipo'] = implode(', ', $map[$mapKey]['extendedProps']['tipos']);
};

try {
    $stmtCobros = db()->query(
        'SELECT cs.id,
                cs.fecha_cobro,
                cs.fecha_primer_aviso,
                cs.fecha_segundo_aviso,
                cs.fecha_tercer_aviso,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                s.nombre AS servicio,
                c.color_hex AS color
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         LEFT JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.fecha_cobro'
    );

    foreach ($stmtCobros->fetchAll() as $cobro) {
        $color = $cobro['color'] ?: '#6c757d';
        $cliente = trim((string) ($cobro['cliente'] ?: 'Cliente'));
        $servicio = trim((string) ($cobro['servicio'] ?: 'Servicio'));
        $cobroId = (int) $cobro['id'];

        $registerEvent($eventMap, (string) ($cobro['fecha_cobro'] ?? ''), 'Vencimiento', $cliente, $servicio, $color, $cobroId);
        $registerEvent($eventMap, (string) ($cobro['fecha_primer_aviso'] ?? ''), 'Aviso 1', $cliente, $servicio, $color, $cobroId);
        $registerEvent($eventMap, (string) ($cobro['fecha_segundo_aviso'] ?? ''), 'Aviso 2', $cliente, $servicio, $color, $cobroId);
        $registerEvent($eventMap, (string) ($cobro['fecha_tercer_aviso'] ?? ''), 'Aviso 3', $cliente, $servicio, $color, $cobroId);
    }

    $calendarEvents = array_values($eventMap);
} catch (Exception $e) {
} catch (Error $e) {
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Calendario"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
    <style>
        #calendar .fc-toolbar-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        #calendar .fc-daygrid-event {
            border-radius: 999px;
            border: 0;
            padding: 2px 8px;
            font-size: .75rem;
        }

        #calendar .fc-daygrid-day-number {
            color: #334155;
            font-weight: 500;
        }

        #calendar .fc-col-header-cell-cushion {
            color: #64748b;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
    </style>
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

                <?php $subtitle = "Cobros de servicios"; $title = "Calendario"; include('partials/page-title.php'); ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Calendario de cobros y avisos</h5>
                        <p class="text-muted mb-0">Vista simplificada. Haz clic sobre un evento para ver el detalle.</p>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
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

    <!-- Fullcalendar js -->
    <script src="assets/plugins/fullcalendar/index.global.min.js"></script>

    <div class="modal fade" id="event-details-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="event-details-title">Detalle del evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><span class="text-muted">Tipo(s):</span> <strong id="event-detail-type">-</strong></p>
                    <p class="mb-2"><span class="text-muted">Cliente:</span> <strong id="event-detail-client">-</strong></p>
                    <p class="mb-2"><span class="text-muted">Servicio:</span> <strong id="event-detail-service">-</strong></p>
                    <p class="mb-0"><span class="text-muted">Fecha:</span> <strong id="event-detail-date">-</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar App Demo js -->
    <script src="assets/js/pages/apps-calendar.js"></script>

    <script>
        window.calendarLocale = 'es';
        window.calendarEvents = <?php echo json_encode($calendarEvents, JSON_UNESCAPED_UNICODE); ?>;
        window.calendarReadOnly = true;
    </script>

</body>

</html>
