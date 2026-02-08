<!DOCTYPE html>
<html lang="en" data-sidenav-size="offcanvas">

<head>
    <?php $title = "Offcanvas Menu"; include('partials/title-meta.php'); ?>

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
                <?php $title = "Offcanvas Menu"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info alert-bordered border-start border-info d-flex align-items-start gap-2">
                            <i class="ti ti-info-circle fs-xxl"></i>
                            <div>
                                To enable the offcanvas menu, add  
                                <code>data-sidenav-size="offcanvas"</code> to the  
                                <code>&lt;html&gt;</code> tag.
                            </div>
                        </div>                                               
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