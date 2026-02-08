<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Password Meter"; include('partials/title-meta.php'); ?>

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

                <?php $title = "Password Meter"; $subtitle = "Miscellaneous"; include('partials/page-title.php'); ?>

                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">with Progress Bar</h4>

                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <!-- HTML -->
                                <input type="password" class="form-control mb-2" placeholder="Password">
                                <div class="password-bar mb-2"></div>

                                <p class="text-muted fs-xs mb-0">Use 8 or more characters with a mix of letters, numbers & symbols.</p>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">with Password Condition</h4>

                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <!-- HTML -->
                                <div>
                                    <label class="form-label" for="password-input">Magic Password ✨ (Click Here)</label>
                                    <input type="password" class="form-control" placeholder="Enter password" id="password-input" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">

                                    <div class="form-text">Use 8 or more characters with a mix of letters, numbers & symbols.</div>

                                    <div class="password-box collapse bg-light-subtle border border-light mt-2 rounded">
                                        <div class="p-3">
                                            <h5 class="fs-sm mb-2">Password Recipe:</h5>
                                            <p id="pass-length" class="invalid fs-xs mb-2">Minimum <b>8 characters</b></p>
                                            <p id="pass-lower" class="invalid fs-xs mb-2">At <b>lowercase</b> letter (a-z)</p>
                                            <p id="pass-upper" class="invalid fs-xs mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                            <p id="pass-number" class="invalid fs-xs mb-0">A least <b>number</b> (0-9)</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

    <script src="assets/js/pages/misc-pass-meter.js"></script>

    <script>

    </script>

</body>

</html>