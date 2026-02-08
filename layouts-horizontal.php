<!DOCTYPE html>
<html lang="en" data-layout="topnav" data-topbar-color="dark" data-menu-color="light">

<head>
    <?php $title = "Horizontal Menu"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/topbar.php'); ?>

        <?php include('partials/horizontal-nav.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="container-fluid">
                <?php $title = "Horizontal"; include('partials/page-title.php'); ?>

                
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