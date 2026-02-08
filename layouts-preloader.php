<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Preloader Layout"; include('partials/title-meta.php'); ?>

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
                <?php $title = "Preloader"; include('partials/page-title.php'); ?>

                <!-- Preloader -->
                <div id="preloader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="m-0">Your custom content here</h4>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
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