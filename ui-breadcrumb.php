<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Breadcrumb"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Breadcrumb"; include('partials/page-title.php'); ?> 

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Basic</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 py-2">
                                        <li class="breadcrumb-item active" aria-current="page">Home</li>
                                    </ol>
                                </nav>

                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 py-2">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Library</li>
                                    </ol>
                                </nav>

                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 py-2">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item"><a href="#">Library</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Data</li>
                                    </ol>
                                </nav>
                            </div> <!-- end code body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">With Icons</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb bg-light bg-opacity-50 p-2 mb-2">
                                        <li class="breadcrumb-item active" aria-current="page"><i class="ti ti-smart-home me-1"></i>Home</li>
                                    </ol>
                                </nav>

                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb bg-light bg-opacity-50 p-2 mb-2">
                                        <li class="breadcrumb-item"><a href="#"><i class="ti ti-smart-home"></i>
                                                Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Library</li>
                                    </ol>
                                </nav>

                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb bg-light bg-opacity-50 p-2 mb-0">
                                        <li class="breadcrumb-item"><a href="#"><i class="ti ti-smart-home"></i>
                                                Home</a></li>
                                        <li class="breadcrumb-item"><a href="#">Library</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Data</li>
                                    </ol>
                                </nav>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                </div> <!-- end row -->

            </div> <!-- container -->


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