<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Carousel"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Carousel"; include('partials/page-title.php'); ?> 

                <div class="row">
                    
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Slides Only</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner" role="listbox">
                                        <div class="carousel-item active">
                                            <!-- First slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-1.jpg" alt="First slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Second slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-2.jpg" alt="Second slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Third slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-3.jpg" alt="Third slide">
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body -->
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">With Controls</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- START carousel-->
                                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner" role="listbox">
                                        <div class="carousel-item active">
                                            <!-- First slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-4.jpg" alt="First slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Second slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-1.jpg" alt="Second slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Third slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-2.jpg" alt="Third slide">
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            </div> <!-- end card body -->
                        </div>
                        <!-- END carousel-->
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">With Indicators</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                      </div>
                                    <div class="carousel-inner" role="listbox">
                                        <div class="carousel-item active">
                                            <!-- First slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-3.jpg" alt="First slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Second slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-2.jpg" alt="Second slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Third slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-1.jpg" alt="Third slide">
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            </div> <!-- end card body -->
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">With Captions</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="carouselExampleCaption" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner" role="listbox">
                                        <div class="carousel-item active">
                                            <!-- first slide  -->
                                            <img src="assets/images/stock/small-1.jpg" alt="..." class="d-block img-fluid">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h3 class="text-white">First slide label</h3>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <!-- secound slide  -->
                                            <img src="assets/images/stock/small-3.jpg" alt="..." class="d-block img-fluid">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h3 class="text-white">Second slide label</h3>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <!-- third slide  -->
                                            <img src="assets/images/stock/small-2.jpg" alt="..." class="d-block img-fluid">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h3 class="text-white">Third slide label</h3>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleCaption" role="button" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleCaption" role="button" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            </div> <!-- end card body -->
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Crossfade</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <!-- First slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-1.jpg" alt="First slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Second slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-2.jpg" alt="Second slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Third slide -->
                                            <img class="d-block img-fluid" src="assets/images/stock/small-3.jpg" alt="Third slide">
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            </div> <!-- end card body -->
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Individual Interval</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active" data-bs-interval="1000">
                                            <!-- First slide -->
                                            <img src="assets/images/stock/small-4.jpg" class="img-fluid d-block w-100" alt="First slide">
                                        </div>
                                        <div class="carousel-item" data-bs-interval="2000">
                                            <!-- Second slide -->
                                            <img src="assets/images/stock/small-2.jpg" class="img-fluid d-block w-100" alt="Second slide">
                                        </div>
                                        <div class="carousel-item">
                                            <!-- Third slide -->
                                            <img src="assets/images/stock/small-1.jpg" class="img-fluid d-block w-100" alt="Third slide">
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleInterval" role="button" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleInterval" role="button" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </a>
                                </div>
                            </div> <!-- end card body -->
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Dark Variant</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="carouselExampleDark" class="carousel carousel-dark slide">
                                    <div class="carousel-indicators">
                                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                    </div>
                                    <div class="carousel-inner">
                                        <div class="carousel-item active" data-bs-interval="10000">
                                            <!-- first slide  -->
                                            <img src="assets/images/stock/small-8.jpg" class="img-fluid" alt="Images">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h4 class="fw-bold">First slide label</h4>
                                                <p>Some representative placeholder content for the first slide.</p>
                                            </div>
                                        </div>
                                        <div class="carousel-item" data-bs-interval="2000">
                                            <!-- secound slide  -->
                                            <img src="assets/images/stock/small-9.jpg" class="img-fluid" alt="Images">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h4 class="fw-bold">Second slide label</h4>
                                                <p>Some representative placeholder content for the second slide.</p>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <!-- third slide  -->
                                            <img src="assets/images/stock/small-10.jpg" class="img-fluid" alt="Images">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h4 class="fw-bold">Third slide label</h4>
                                                <p>Some representative placeholder content for the third slide.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div> <!-- end card body -->
                        </div>
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