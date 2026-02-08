<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Iniciar sesión"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>
        <?php
        $municipalidad = get_municipalidad();
        $logoAuthHeight = (int) ($municipalidad['logo_auth_height'] ?? 48);
        ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Seguridad y Acceso"; $title = "Iniciar sesión"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo municipalidad" class="img-fluid" style="max-height: <?php echo $logoAuthHeight; ?>px;">
                                </div>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="login-username">Usuario o correo</label>
                                        <input type="text" id="login-username" class="form-control" placeholder="usuario@muni.cl">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="login-password">Contraseña</label>
                                        <input type="password" id="login-password" class="form-control" placeholder="********">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="login-remember">
                                            <label class="form-check-label" for="login-remember">Mantener sesión</label>
                                        </div>
                                        <a href="recuperar-contrasena.php" class="text-muted">¿Olvidaste tu contraseña?</a>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ingresar</button>
                                    <button type="button" class="btn btn-outline-secondary ms-2">Cerrar sesión</button>
                                </form>
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
