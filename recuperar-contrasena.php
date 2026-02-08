<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Recuperar contraseña"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Seguridad y Acceso"; $title = "Recuperar contraseña"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo municipalidad" class="img-fluid" style="max-height: <?php echo $logoAuthHeight; ?>px;">
                                </div>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="recover-email">Correo registrado</label>
                                        <input type="email" id="recover-email" class="form-control" placeholder="usuario@muni.cl">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="recover-rut">RUT</label>
                                        <input type="text" id="recover-rut" class="form-control" placeholder="12.345.678-9">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="recover-phone">Teléfono de contacto</label>
                                        <input type="tel" id="recover-phone" class="form-control" placeholder="+56 9 1234 5678">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enviar instrucciones</button>
                                    <a href="login.php" class="btn btn-link">Volver al login</a>
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
