<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Collapse"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Collapse"; include('partials/page-title.php'); ?> 

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Collapse</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p>
                                    <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                        Link with href
                                    </a>
                                    <button class="btn btn-primary ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                        Button with data-bs-target
                                    </button>
                                </p>
                                <div class="collapse show" id="collapseExample">
                                    <div class="card  border border-dashed border-light card-body mb-0">
                                        Anim pariatur cliche reprehenderit, enim eiusmod high life
                                        accusamus terry
                                        richardson ad squid. Nihil anim keffiyeh helvetica, craft beer
                                        labore wes
                                        anderson cred nesciunt sapiente ea proident.
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Multiple Targets</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <a class="btn btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">Toggle first element</a>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">Toggle second element</button>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapseExample1 multiCollapseExample2">Toggle both elements</button>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="collapse multi-collapse" id="multiCollapseExample1">
                                            <div class="card border border-dashed border-light card-body mb-0">
                                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="collapse multi-collapse" id="multiCollapseExample2">
                                            <div class="card border border-dashed border-light card-body mb-0">
                                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row-->
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->


                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Collapse Horizontal</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
                                        Toggle width collapse
                                    </button>
                                </p>
                                <div style="height: 100px;">
                                    <div class="collapse collapse-horizontal" id="collapseWidthExample">
                                        <div class="card border border-dashed border-light card-body mb-0" style="width: 300px;">
                                            This is some placeholder content for a horizontal collapse. It's hidden by default and shown when triggered.
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row-->

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