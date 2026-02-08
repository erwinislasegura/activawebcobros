<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Email Templates"; include('partials/title-meta.php'); ?>

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
                
                <?php $subtitle = "Email"; $title = "Email Templates"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Basic Action</h4>
                                        <img src="assets/images/emails/confirm.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Activated Account</h4>
                                        <img src="assets/images/emails/activated.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Marketing</h4>
                                        <img src="assets/images/emails/marketing.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Invoice</h4>
                                        <img src="assets/images/emails/invoice.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Reports</h4>
                                        <img src="assets/images/emails/reports.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Reset Password</h4>
                                        <img src="assets/images/emails/reset-password.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <h4 class="header-title mb-3">Warning</h4>
                                        <img src="assets/images/emails/warning-mail.png" class="img-fluid" alt="">
                                    </div>
                                </div> <!-- end row-->
                            </div>
                        </div> <!-- end card -->
                    </div> <!-- end col-->
                </div>    
                <!-- end row-->

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