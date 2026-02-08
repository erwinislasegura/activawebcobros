<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Offcanvas"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Offcanvas"; include('partials/page-title.php'); ?> 

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Offcanvas</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted fs-base">You can trigger an offcanvas using a link with <code>href</code> or a button with <code>data-bs-target</code>, but both must include <code>data-bs-toggle="offcanvas"</code>.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-primary" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                                        Link with href
                                    </a>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                                        Button with data-bs-target
                                    </button>
                                </div> <!-- end d-flex flex-wrap gap-2-->

                                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div> <!-- end offcanvas-header-->

                                    <div class="offcanvas-body">
                                        <div>
                                            Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                            images, lists, etc.
                                        </div>
                                        <h5 class="mt-3">List</h5>
                                        <ul class="ps-3">
                                            <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                            <li class="">Neque porro quisquam est, qui dolorem</li>
                                            <li class="">Quis autem vel eum iure qui in ea</li>
                                        </ul>

                                        <ul class="ps-3">
                                            <li class="">At vero eos et accusamus et iusto odio dignissimos</li>
                                            <li class="">Et harum quidem rerum facilis</li>
                                            <li class="">Temporibus autem quibusdam et aut officiis</li>
                                        </ul>
                                    </div> <!-- end offcanvas-body-->
                                </div> <!-- end offcanvas-->
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Offcanvas Backdrop</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted fs-base">When an offcanvas and its backdrop are visible, <code>&lt;body&gt;</code> scrolling is disabled. Use <code>data-bs-scroll</code> to enable scrolling and <code>data-bs-backdrop</code> to control the backdrop visibility.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Enable body scrolling -->
                                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">Enable body scrolling</button>

                                    <!-- Enable backdrop (default) -->
                                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBackdrop" aria-controls="offcanvasWithBackdrop">Enable backdrop (default)</button>

                                    <!-- Enable both scrolling & backdrop -->
                                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Enable both scrolling & backdrop</button>

                                </div> <!-- end d-flex flex-wrap gap-2-->

                                <div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Colored with scrolling</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div> <!-- end offcanvas-header-->
                                    <div class="offcanvas-body">
                                        <div>
                                            Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                            images, lists, etc.
                                        </div>
                                        <h5 class="mt-3">List</h5>
                                        <ul class="ps-3">
                                            <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                            <li class="">Neque porro quisquam est, qui dolorem</li>
                                            <li class="">Quis autem vel eum iure qui in ea</li>
                                        </ul>

                                        <ul class="ps-3">
                                            <li class="">At vero eos et accusamus et iusto odio dignissimos</li>
                                            <li class="">Et harum quidem rerum facilis</li>
                                            <li class="">Temporibus autem quibusdam et aut officiis</li>
                                        </ul>
                                    </div> <!-- end offcanvas-body-->
                                </div> <!-- end offcanvas-->

                                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasWithBackdrop" aria-labelledby="offcanvasWithBackdropLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasWithBackdropLabel">Offcanvas with backdrop</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div> <!-- end offcanvas-header-->

                                    <div class="offcanvas-body">
                                        <div>
                                            Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                            images, lists, etc.
                                        </div>
                                        <h5 class="mt-3">List</h5>
                                        <ul class="ps-3">
                                            <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                            <li class="">Neque porro quisquam est, qui dolorem</li>
                                            <li class="">Quis autem vel eum iure qui in ea</li>
                                        </ul>

                                        <ul class="ps-3">
                                            <li class="">At vero eos et accusamus et iusto odio dignissimos</li>
                                            <li class="">Et harum quidem rerum facilis</li>
                                            <li class="">Temporibus autem quibusdam et aut officiis</li>
                                        </ul>
                                    </div> <!-- end offcanvas-body-->
                                </div> <!-- end offcanvas-->

                                <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Backdroped with scrolling</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div> <!-- end offcanvas-header-->

                                    <div class="offcanvas-body">
                                        <div>
                                            Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                            images, lists, etc.
                                        </div>
                                        <h5 class="mt-3">List</h5>
                                        <ul class="ps-3">
                                            <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                            <li class="">Neque porro quisquam est, qui dolorem</li>
                                            <li class="">Quis autem vel eum iure qui in ea</li>
                                        </ul>

                                        <ul class="ps-3">
                                            <li class="">At vero eos et accusamus et iusto odio dignissimos</li>
                                            <li class="">Et harum quidem rerum facilis</li>
                                            <li class="">Temporibus autem quibusdam et aut officiis</li>
                                        </ul>
                                    </div> <!-- end offcanvas-body-->
                                </div> <!-- end offcanvas-->
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Offcanvas Placement</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted fs-sm">
                                    <code>.offcanvas-start</code> positions the offcanvas on the left, <code>.offcanvas-end</code> on the right, <code>.offcanvas-top</code> displays it from the top, and <code>.offcanvas-bottom</code> displays it from the bottom of the viewport.
                                </p>

                                <div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <!-- Toggle Top offcanvas -->
                                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop">Toggle Top offcanvas</button>
                                        <!-- Toggle right offcanvas -->
                                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Toggle right offcanvas</button>
                                        <!-- Toggle bottom offcanvas -->
                                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">Toggle bottom offcanvas</button>
                                        <!-- Toggle Left offcanvas -->
                                        <button class="btn btn-primary mt-2 mt-lg-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft">Toggle Left offcanvas</button>
                                    </div> <!-- end d-flex flex-wrap gap-2-->

                                    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasTopLabel">Offcanvas Top</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div> <!-- end offcanvas-header-->

                                        <div class="offcanvas-body">
                                            <div>
                                                Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                                images, lists, etc.
                                            </div>
                                            <h5 class="mt-3">List</h5>
                                            <ul class="ps-3">
                                                <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                                <li class="">Neque porro quisquam est, qui dolorem</li>
                                                <li class="">Quis autem vel eum iure qui in ea</li>
                                            </ul>
                                        </div> <!-- end offcanvas-body-->
                                    </div> <!-- end offcanvas-->

                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasRightLabel">Offcanvas right</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div> <!-- end offcanvas-header-->

                                        <div class="offcanvas-body">
                                            <div>
                                                Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                                images, lists, etc.
                                            </div>
                                            <h5 class="mt-3">List</h5>
                                            <ul class="ps-3">
                                                <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                                <li class="">Neque porro quisquam est, qui dolorem</li>
                                                <li class="">Quis autem vel eum iure qui in ea</li>
                                            </ul>
                                        </div> <!-- end offcanvas-body-->
                                    </div> <!-- end offcanvas-->

                                    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
                                        <div class="offcanvas-header">
                                            <h5 class="offcanvas-title" id="offcanvasBottomLabel">Offcanvas bottom</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div> <!-- end offcanvas-header-->

                                        <div class="offcanvas-body">
                                            <div>
                                                Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                                images, lists, etc.
                                            </div>
                                            <h5 class="mt-3">List</h5>
                                            <ul class="ps-3">
                                                <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                                <li class="">Neque porro quisquam est, qui dolorem</li>
                                                <li class="">Quis autem vel eum iure qui in ea</li>
                                            </ul>
                                        </div> <!-- end offcanvas-body-->
                                    </div> <!-- end offcanvas-->

                                    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel">
                                        <div class="offcanvas-header">
                                            <h5 id="offcanvasLeftLabel">Offcanvas Left</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div> <!-- end offcanvas-header-->

                                        <div class="offcanvas-body">
                                            <div>
                                                Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                                images, lists, etc.
                                            </div>
                                            <h5 class="mt-3">List</h5>
                                            <ul class="ps-3">
                                                <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                                <li class="">Neque porro quisquam est, qui dolorem</li>
                                                <li class="">Quis autem vel eum iure qui in ea</li>
                                            </ul>
                                        </div> <!-- end offcanvas-body-->
                                    </div> <!-- end offcanvas-->
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Dark Offcanvas</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted fs-sm">Customize the look of offcanvases using utility classes to suit different themes, such as dark navbars. Add <code>.text-bg-dark</code> to <code>.offcanvas</code> and <code>.btn-close-white</code> to <code>.btn-close</code> for dark styling.</p>

                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDark" aria-controls="offcanvasDark">Dark offcanvas</button>

                                <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasDark" aria-labelledby="offcanvasDarkLabel">
                                    <div class="offcanvas-header">
                                        <h5 id="offcanvasDarkLabel">Dark Offcanvas</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div> <!-- end offcanvas-header-->

                                    <div class="offcanvas-body">
                                        <div>
                                            Some text as placeholder. In real life you can have the elements you have chosen. Like, text,
                                            images, lists, etc.
                                        </div>
                                        <h5 class="mt-3">List</h5>
                                        <ul class="ps-3">
                                            <li class="">Nemo enim ipsam voluptatem quia aspernatur</li>
                                            <li class="">Neque porro quisquam est, qui dolorem</li>
                                            <li class="">Quis autem vel eum iure qui in ea</li>
                                        </ul>
                                    </div> <!-- end offcanvas-body-->
                                </div> <!-- end offcanvas-->
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