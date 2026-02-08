<?php
require __DIR__ . '/app/bootstrap.php';

$calendarEvents = [];

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
        if (!empty($cobro['fecha_cobro'])) {
            $calendarEvents[] = [
                'id' => 'cobro-' . (int) $cobro['id'],
                'title' => sprintf('Vence: %s - %s', $cobro['cliente'], $cobro['servicio'] ?? 'Servicio'),
                'start' => $cobro['fecha_cobro'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => 'cobros-servicios-registros.php',
            ];
        }
        if (!empty($cobro['fecha_primer_aviso'])) {
            $calendarEvents[] = [
                'id' => 'aviso-1-' . (int) $cobro['id'],
                'title' => sprintf('Aviso 1: %s - %s', $cobro['cliente'], $cobro['servicio'] ?? 'Servicio'),
                'start' => $cobro['fecha_primer_aviso'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => 'cobros-avisos.php',
            ];
        }
        if (!empty($cobro['fecha_segundo_aviso'])) {
            $calendarEvents[] = [
                'id' => 'aviso-2-' . (int) $cobro['id'],
                'title' => sprintf('Aviso 2: %s - %s', $cobro['cliente'], $cobro['servicio'] ?? 'Servicio'),
                'start' => $cobro['fecha_segundo_aviso'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => 'cobros-avisos.php',
            ];
        }
        if (!empty($cobro['fecha_tercer_aviso'])) {
            $calendarEvents[] = [
                'id' => 'aviso-3-' . (int) $cobro['id'],
                'title' => sprintf('Aviso 3: %s - %s', $cobro['cliente'], $cobro['servicio'] ?? 'Servicio'),
                'start' => $cobro['fecha_tercer_aviso'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => 'cobros-avisos.php',
            ];
        }
    }
} catch (Exception $e) {
} catch (Error $e) {
}

try {
    $stmtCobros = db()->query(
        'SELECT cs.id,
                cs.fecha_cobro,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                s.nombre AS servicio,
                c.color_hex AS color
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         LEFT JOIN servicios s ON s.id = cs.servicio_id
         WHERE cs.fecha_cobro IS NOT NULL
         ORDER BY cs.fecha_cobro'
    );
    foreach ($stmtCobros->fetchAll() as $cobro) {
        $calendarEvents[] = [
            'id' => 'cobro-' . (int) $cobro['id'],
            'title' => sprintf('Vence: %s - %s', $cobro['cliente'], $cobro['servicio'] ?? 'Servicio'),
            'start' => $cobro['fecha_cobro'],
            'allDay' => true,
            'backgroundColor' => $cobro['color'] ?: '#6c757d',
            'borderColor' => $cobro['color'] ?: '#6c757d',
            'url' => 'cobros-servicios-registros.php',
        ];
    }
} catch (Exception $e) {
} catch (Error $e) {
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Calendario"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Cobros de servicios"; $title = "Calendario"; include('partials/page-title.php'); ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Calendario de cobros y avisos</h5>
                        <p class="text-muted mb-0">Revisa vencimientos y avisos por cliente.</p>
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

    <!-- Calendar App Demo js -->
    <script src="assets/js/pages/apps-calendar.js"></script>

    <script>
        window.calendarLocale = 'es';
        window.calendarEvents = <?php echo json_encode($calendarEvents, JSON_UNESCAPED_UNICODE); ?>;
        window.calendarReadOnly = true;
    </script>

</body>

</html>
