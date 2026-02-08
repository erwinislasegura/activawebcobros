<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Bitácora"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Seguridad y Acceso"; $title = "Bitácora"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Bitácora</h5>
                                    <p class="text-muted mb-0">Auditoría de movimientos y acciones registradas.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <form class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label" for="log-usuario">Usuario</label>
                                        <input type="text" id="log-usuario" class="form-control" placeholder="Buscar usuario">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="log-modulo">Módulo</label>
                                        <select id="log-modulo" class="form-select">
                                            <option value="">Todos</option>
                                            <option>Usuarios</option>
                                            <option>Roles</option>
                                            <option>Eventos</option>
                                            <option>Autoridades</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="log-accion">Acción</label>
                                        <select id="log-accion" class="form-select">
                                            <option value="">Todas</option>
                                            <option>Crear</option>
                                            <option>Editar</option>
                                            <option>Eliminar</option>
                                            <option>Publicar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>Módulo</th>
                                                <th>Acción</th>
                                                <th>Detalle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>22/01/2026 09:40</td>
                                                <td>Super User</td>
                                                <td>Usuarios</td>
                                                <td><span class="badge text-bg-success">Crear</span></td>
                                                <td>Registro de usuario Juan Pérez</td>
                                            </tr>
                                            <tr>
                                                <td>21/01/2026 18:12</td>
                                                <td>María Soto</td>
                                                <td>Eventos</td>
                                                <td><span class="badge text-bg-info">Editar</span></td>
                                                <td>Actualizó evento “Operativo Salud”</td>
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
