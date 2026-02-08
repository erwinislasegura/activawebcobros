<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Gestión de sesiones"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Seguridad y Acceso"; $title = "Gestión de sesiones"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Sesiones activas</h5>
                                    <p class="text-muted mb-0">Monitorea y administra las sesiones del sistema.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>IP</th>
                                                <th>Dispositivo</th>
                                                <th>Inicio</th>
                                                <th>Última actividad</th>
                                                <th>Estado</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Super User</td>
                                                <td>186.12.34.56</td>
                                                <td>Chrome · Windows</td>
                                                <td>22/01/2026 09:12</td>
                                                <td>22/01/2026 10:03</td>
                                                <td><span class="badge text-bg-success">Activa</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-danger">Cerrar sesión</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>María Soto</td>
                                                <td>200.10.55.21</td>
                                                <td>Safari · iOS</td>
                                                <td>22/01/2026 08:45</td>
                                                <td>22/01/2026 09:50</td>
                                                <td><span class="badge text-bg-warning">Inactiva</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-secondary">Finalizar</button>
                                                </td>
                                            </tr>
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

</body>

</html>
