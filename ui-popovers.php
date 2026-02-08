<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Popovers"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Popovers"; include('partials/page-title.php'); ?> 

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Simple Popover</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <button type="button" class="btn btn-info" data-bs-toggle="popover" title="Need Help?" data-bs-content="Click here to get support from our team. We're here 24/7 to assist you.">
                                    Get Support Info
                                </button>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Dismiss on Next Click</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <button type="button" tabindex="0" class="btn btn-primary" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="Get quick tips and tricks to improve your workflow instantly." title="Quick Tips">
                                    Show Tips
                                </button>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Hover</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <button type="button" tabindex="0" class="btn btn-dark" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="Discover features you didn’t know existed. Hover to explore more!" title="Exciting Features!">
                                    Please Hover Me
                                </button>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Four Directions</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Popover on top -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="popover" data-bs-placement="top" title="Top Popover" data-bs-content="This popover appears above the button. Great for tips or info.">
                                        Popover on top
                                    </button>

                                    <!-- Popover on bottom -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="popover" data-bs-placement="bottom" title="Bottom Popover" data-bs-content="This popover shows below. Perfect for additional details.">
                                        Popover on bottom
                                    </button>

                                    <!-- Popover on right -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="popover" data-bs-placement="right" title="Right Popover" data-bs-content="Slide in from the right to provide quick insights.">
                                        Popover on right
                                    </button>

                                    <!-- Popover on left -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="popover" data-bs-placement="left" title="Left Popover" data-bs-content="Appears on the left side. Great for tooltips or notes.">
                                        Popover on left
                                    </button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Custom Popovers </h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Primary Popover -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-primary" data-bs-title="Primary Popover" data-bs-content="This is a primary-themed popover styled using CSS variables.">
                                        Primary Popover
                                    </button>

                                    <!-- Success Popover -->
                                    <button type="button" class="btn btn-success" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-success" data-bs-title="Success Popover" data-bs-content="This is a success-themed popover styled using CSS variables.">
                                        Success Popover
                                    </button>

                                    <!-- Danger Popover -->
                                    <button type="button" class="btn btn-danger" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-danger" data-bs-title="Danger Popover" data-bs-content="This is a danger-themed popover styled using CSS variables.">
                                        Danger Popover
                                    </button>

                                    <!-- Info Popover -->
                                    <button type="button" class="btn btn-info" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-info" data-bs-title="Info Popover" data-bs-content="This is an info-themed popover styled using CSS variables.">
                                        Info Popover
                                    </button>

                                    <!-- Dark Popover -->
                                    <button type="button" class="btn btn-dark" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-dark" data-bs-title="Dark Popover" data-bs-content="This is a dark-themed popover styled using CSS variables.">
                                        Dark Popover
                                    </button>

                                    <!-- Secondary Popover -->
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="popover" data-bs-placement="right" data-bs-custom-class="popover-secondary" data-bs-title="Secondary Popover" data-bs-content="This is a secondary-themed popover styled using CSS variables.">
                                        Secondary Popover
                                    </button>
                                </div>

                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Disabled Elements</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <span class="d-inline-block" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="This button is disabled, but the popover still works.">
                                    <button class="btn btn-primary" type="button" disabled style="pointer-events: none;">
                                        Disabled Button
                                    </button>
                                </span>
                            </div> <!-- end card-body -->
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