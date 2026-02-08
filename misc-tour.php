<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Tour"; include('partials/title-meta.php'); ?>

    <!-- Ladda style -->
    <link href="assets/plugins/tourguidejs/tour.min.css" rel="stylesheet">


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

                <?php $subtitle = "Miscellaneous"; $title = "Tour"; include('partials/page-title.php'); ?>

                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="text-center mt-4 mb-5">
                            <div class="auth-brand text-center mb-4">
                                <a href="index.php" class="logo-dark">
                                    <img src="assets/images/logo-black.png" alt="dark logo" height="32">
                                </a>
                                <a href="index.php" class="logo-light">
                                    <img src="assets/images/logo.png" alt="logo" height="32">
                                </a>
                            </div>

                            <h5 class="fs-lg mb-2">Versatile & Scalable Admin Panel Template</h5>
                            <p class="text-muted fs-sm">
                                Build modern web applications faster with our feature-rich admin panel. Compatible with multiple frameworks and packed with diverse demos, it offers seamless customization and a consistent UI across all your projects.
                            </p>

    
                            <!-- Buttons -->
                            <div class="d-flex justify-content-center mt-4 flex-wrap gap-2">
                                <button id="tourTrigger" onclick="startTour()" class="btn btn-primary">
                                    <i class="ti ti-player-play me-1"></i> Start Guided Tour
                                </button>

                                <a href="#" class="btn btn-dark" data-tg-order="1" data-tg-tour="Click here to get started and explore our framework-rich admin panel. 🚀" data-tg-title="Getting Started">
                                    <i class="ti ti-compass me-1"></i> Discover Features
                                </a>

                                <a href="#!" class="btn btn-danger" data-tg-order="2" data-tg-tour="Ready to supercharge your project? Click here to purchase the template!" data-tg-title="Buy Now">
                                    <i class="ti ti-shopping-cart me-1"></i> Get the Template
                                </a>
                            </div>
                                                
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="container" data-tg-order="3" data-tg-tour="Learn more about the versatile services and modules we provide to enhance development." data-tg-title="Core Features">
                    <div class="row">
                        <div class="col-xl-3">
                            <div class="card border-0 p-2 card-h-100">
                                <div class="card-body pb-0">
                                    <div class="avatar-xl mb-3">
                                        <span class="avatar-title text-bg-secondary rounded-circle fs-22">
                                            <i class="ti ti-device-desktop"></i>
                                        </span>
                                    </div>
                                    <h4 class="fw-semibold mb-2">Multiple Frameworks</h4>
                                    <p class="text-muted mb-3">Support for Bootstrap, Tailwind, React, Vue, Angular, Laravel, and more — use what suits your stack.</p>
                                </div>
                                <div class="card-footer border-0 pt-0">
                                    <a class="link-primary fw-semibold" href="#">Know more<i class="ti ti-arrow-right ms-2 align-middle"></i></a>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-xl-3">
                            <div class="card border-0 p-2 card-h-100">
                                <div class="card-body pb-0">
                                    <div class="avatar-xl mb-3">
                                        <span class="avatar-title text-bg-secondary rounded-circle fs-22">
                                            <i class="ti ti-layout"></i>
                                        </span>
                                    </div>
                                    <h4 class="fw-semibold mb-2">Multiple Demos</h4>
                                    <p class="text-muted mb-3">Choose from a variety of pre-built demos to match your use case — from CRM to SaaS dashboards.</p>
                                </div>
                                <div class="card-footer border-0 pt-0">
                                    <a class="link-primary fw-semibold" href="#">Know more<i class="ti ti-arrow-right ms-2 align-middle"></i></a>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-xl-3">
                            <div class="card border-0 p-2 card-h-100">
                                <div class="card-body pb-0">
                                    <div class="avatar-xl mb-3">
                                        <span class="avatar-title text-bg-secondary rounded-circle fs-22">
                                            <i class="ti ti-brush"></i>
                                        </span>
                                    </div>
                                    <h4 class="fw-semibold mb-2">Customizable UI</h4>
                                    <p class="text-muted mb-3">Easily tailor colors, layouts, and components to match your branding and requirements.</p>
                                </div>
                                <div class="card-footer border-0 pt-0">
                                    <a class="link-primary fw-semibold" href="#">Know more<i class="ti ti-arrow-right ms-2 align-middle"></i></a>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-xl-3">
                            <div class="card border-0 p-2 card-h-100">
                                <div class="card-body pb-0">
                                    <div class="avatar-xl mb-3">
                                        <span class="avatar-title text-bg-secondary rounded-circle fs-22">
                                            <i class="ti ti-rocket"></i>
                                        </span>
                                    </div>
                                    <h4 class="fw-semibold mb-2">High Performance</h4>
                                    <p class="text-muted mb-3">Optimized for speed and efficiency, our admin panel ensures a seamless experience for developers and users alike.</p>
                                </div>
                                <div class="card-footer border-0 pt-0">
                                    <a class="link-primary fw-semibold" href="#">Know more<i class="ti ti-arrow-right ms-2 align-middle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
                 
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

    <!-- Tour Plugin Js-->
    <script src="assets/plugins/tourguidejs/tour.js"></script>

    <!-- Tour Page Js-->
    <script src="assets/js/pages/misc-tour.js"></script>

</body>

</html>