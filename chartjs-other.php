<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Other Charts"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Charts"; $title = "Other Charts"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Bubble</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="bubble-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Combo Bar & Line</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="combo-bar-line-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Stacked Bar & Line</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="stacked-bar-line-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Doughnut</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="doughnut-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Multi Series Pie</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="multi-pie-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pie</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="pie-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Polar Area</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="polar-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Radar</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="radar-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Scatter</h5>
                            </div>
                            <div class="card-body">
                                <div dir="ltr">
                                    <div class="mt-3" style="height: 300px;">
                                        <canvas id="scatter-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body-->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col-->
                </div>
                <!-- end row-->

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

    <!-- Chartjs -->
    <script src="assets/plugins/chartjs/chart.umd.js"></script>

    <!-- Other chart Demo js -->
    <script src="assets/js/pages/chartjs-other.js"></script>

</body>

</html>