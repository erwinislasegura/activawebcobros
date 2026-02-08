<?php
require __DIR__ . '/app/bootstrap.php';

$statusClasses = [
    'publicado' => 'bg-success-subtle text-success',
    'borrador' => 'bg-warning-subtle text-warning',
    'revision' => 'bg-info-subtle text-info',
    'finalizado' => 'bg-secondary-subtle text-secondary',
    'cancelado' => 'bg-danger-subtle text-danger',
];

$stmt = db()->query('SELECT id, titulo, fecha_inicio, fecha_fin, tipo, estado FROM events WHERE habilitado = 1 ORDER BY fecha_inicio');
$calendarEvents = [];
foreach ($stmt->fetchAll() as $evento) {
    $start = DateTime::createFromFormat('Y-m-d H:i:s', $evento['fecha_inicio']);
    $end = DateTime::createFromFormat('Y-m-d H:i:s', $evento['fecha_fin']);
    $calendarEvents[] = [
        'id' => (int) $evento['id'],
        'title' => $evento['titulo'],
        'start' => $start ? $start->format('Y-m-d\\TH:i:s') : $evento['fecha_inicio'],
        'end' => $end ? $end->format('Y-m-d\\TH:i:s') : $evento['fecha_fin'],
        'className' => $statusClasses[$evento['estado']] ?? 'bg-primary-subtle text-primary',
        'url' => 'eventos-detalle.php?id=' . (int) $evento['id'],
        'extendedProps' => [
            'tipo' => $evento['tipo'],
        ],
    ];
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

                <?php $subtitle = "Eventos Municipales"; $title = "Calendario"; include('partials/page-title.php'); ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Calendario de eventos</h5>
                        <p class="text-muted mb-0">Selecciona un evento para ver el detalle.</p>
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
