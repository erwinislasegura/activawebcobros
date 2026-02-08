<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Embed Videos"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Videos"; include('partials/page-title.php'); ?>
                
                <div class="row">

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 21:9</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- 21:9 aspect ratio -->
                                <div class="ratio ratio-21x9">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 1:1</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- 21:9 aspect ratio -->
                                <div class="ratio ratio-1x1">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 16:9</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- 21:9 aspect ratio -->
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Responsive embed video 4:3</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- 21:9 aspect ratio -->
                                <div class="ratio ratio-4x3">
                                    <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0"></iframe>
                                </div>
                            </div> <!-- end card-body-->
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