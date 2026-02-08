<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Placeholders"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Placeholders"; include('partials/page-title.php'); ?> 
                
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Placeholders</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    In the example below, we take a typical card component and recreate it with placeholders applied to create a “loading card”. Size and proportions are the same between the two.
                                </p>

                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="card border shadow-none mb-md-0">
                                            <!-- card-img-top -->
                                            <img src="assets/images/stock/small-1.jpg" class="card-img-top" alt="...">

                                            <div class="card-body">
                                                <h5 class="card-title mb-2">Card Title</h5>
                                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's
                                                    content.</p>
                                                <a href="#" class="btn btn-primary">Go somewhere</a>
                                            </div> <!-- end card-body-->
                                        </div> <!-- end card-->
                                    </div> <!-- end col-->

                                    <div class="col-md-5">
                                        <div class="card border shadow-none mb-0" aria-hidden="true">
                                            <svg class="card-img-top" width="100%" style="aspect-ratio: 16 / 10;" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder" preserveAspectRatio="xMidYMid slice" viewBox="0 0 16 10">
                                                <title>Placeholder</title>
                                                <rect width="16" height="10" fill="#20c997"></rect>
                                            </svg>

                                            <div class="card-body">
                                                <h5 class="card-title mb-2 placeholder-glow">
                                                    <span class="placeholder col-6">&nbsp;</span>
                                                </h5>
                                                <p class="card-text placeholder-glow">
                                                    <span class="placeholder col-7"></span>
                                                    <span class="placeholder col-4"></span>
                                                    <span class="placeholder col-4"></span>
                                                    <span class="placeholder col-6"></span>
                                                    <span class="placeholder col-3"></span>
                                                </p>
                                                <a class="btn btn-primary disabled placeholder col-6" aria-disabled="true"> <span class="invisible">Read Only</span></a>
                                            </div> <!-- end card-body-->
                                        </div> <!-- end card-->
                                    </div> <!-- end col-->
                                </div> <!-- end row-->
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Color</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    By default, the <code>placeholder</code> uses <code>currentColor</code>. This can be overriden with a custom color or utility class.
                                </p>

                                <span class="placeholder col-12"></span>
                                <span class="placeholder col-12 bg-primary"></span>
                                <span class="placeholder col-12 bg-secondary"></span>
                                <span class="placeholder col-12 bg-success"></span>
                                <span class="placeholder col-12 bg-danger"></span>
                                <span class="placeholder col-12 bg-warning"></span>
                                <span class="placeholder col-12 bg-info"></span>
                                <span class="placeholder col-12 bg-light"></span>
                                <span class="placeholder col-12 bg-dark"></span>
                            </div> <!-- end card-body-->
                        </div> <!-- end card -->
                    </div>
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Width</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    You can change the <code>width</code> through grid column classes, width utilities, or inline styles.
                                </p>

                                <span class="placeholder col-6"></span>
                                <span class="placeholder w-75"></span>
                                <span class="placeholder" style="width: 25%;"></span>
                                <span class="placeholder" style="width: 10%;"></span>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Sizing</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    The size of <code>.placeholder</code>s are based on the typographic style of the parent element. Customize them with sizing modifiers: <code>.placeholder-lg</code>, <code>.placeholder-sm</code>, or <code>.placeholder-xs</code>.
                                </p>

                                <span class="placeholder col-12 placeholder-lg"></span>
                                <span class="placeholder col-12"></span>
                                <span class="placeholder col-12 placeholder-sm"></span>
                                <span class="placeholder col-12 placeholder-xs"></span>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">How it works</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    Create placeholders with the <code>.placeholder</code> class and a grid column class (e.g., <code>.col-6</code>) to set the <code>width</code>. They can replace the text inside an element or as be added as a modifier class to an existing component.
                                </p>

                                <p aria-hidden="true">
                                    <span class="placeholder col-6"></span>
                                </p>

                                <a href="#" class="btn btn-primary disabled placeholder col-4" aria-hidden="true"></a>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Animation</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    Animate placehodlers with <code>.placeholder-glow</code> or <code>.placeholder-wave</code> to better convey the perception of something being <em>actively</em> loaded.
                                </p>

                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>

                                <p class="placeholder-wave mb-0">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
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