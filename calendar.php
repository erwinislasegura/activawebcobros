<?php
require __DIR__ . '/app/bootstrap.php';

$calendarEvents = [];
$seenEvents = [];

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
        $cliente = $cobro['cliente'] ?: 'Cliente';
        $servicio = $cobro['servicio'] ?: 'Servicio';

        $appendEvent = static function (array $event) use (&$calendarEvents, &$seenEvents): void {
            $dedupeKey = implode('|', [
                $event['extendedProps']['tipo'] ?? '',
                $event['start'] ?? '',
                $event['extendedProps']['cliente'] ?? '',
                $event['extendedProps']['servicio'] ?? '',
            ]);

            if (isset($seenEvents[$dedupeKey])) {
                return;
            }

            $seenEvents[$dedupeKey] = true;
            $calendarEvents[] = $event;
        };

        if (!empty($cobro['fecha_cobro'])) {
            $appendEvent([
                'id' => 'cobro-' . (int) $cobro['id'],
                'title' => sprintf('%s · %s', $cliente, $servicio),
                'start' => $cobro['fecha_cobro'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tipo' => 'Vencimiento',
                    'cliente' => $cliente,
                    'servicio' => $servicio,
                    'fecha' => $cobro['fecha_cobro'],
                ],
            ]);
        }
        if (!empty($cobro['fecha_primer_aviso'])) {
            $appendEvent([
                'id' => 'aviso-1-' . (int) $cobro['id'],
                'title' => sprintf('%s · %s', $cliente, $servicio),
                'start' => $cobro['fecha_primer_aviso'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tipo' => 'Aviso 1',
                    'cliente' => $cliente,
                    'servicio' => $servicio,
                    'fecha' => $cobro['fecha_primer_aviso'],
                ],
            ]);
        }
        if (!empty($cobro['fecha_segundo_aviso'])) {
            $appendEvent([
                'id' => 'aviso-2-' . (int) $cobro['id'],
                'title' => sprintf('%s · %s', $cliente, $servicio),
                'start' => $cobro['fecha_segundo_aviso'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tipo' => 'Aviso 2',
                    'cliente' => $cliente,
                    'servicio' => $servicio,
                    'fecha' => $cobro['fecha_segundo_aviso'],
                ],
            ]);
        }
        if (!empty($cobro['fecha_tercer_aviso'])) {
            $appendEvent([
                'id' => 'aviso-3-' . (int) $cobro['id'],
                'title' => sprintf('%s · %s', $cliente, $servicio),
                'start' => $cobro['fecha_tercer_aviso'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tipo' => 'Aviso 3',
                    'cliente' => $cliente,
                    'servicio' => $servicio,
                    'fecha' => $cobro['fecha_tercer_aviso'],
                ],
            ]);
        }
    }
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
                    <p class="mb-2"><span class="text-muted">Tipo:</span> <strong id="event-detail-type">-</strong></p>
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
