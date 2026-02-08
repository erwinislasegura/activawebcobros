<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Tooltips"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Tooltips"; include('partials/page-title.php'); ?> 
                
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
                                <p class="text-muted">Hover over the links below to see tooltips.</p>

                                <p class="mb-0">
                                    Powerful admin features like <a href="#" class="fw-medium" data-bs-toggle="tooltip" data-bs-title="Manage your dashboard easily">custom dashboards</a> and UI components help you build scalable web applications efficiently. 
                                    This template includes pre-built pages, clean layouts, and reusable code blocks to boost your development workflow. 
                                    From user management to analytics and settings, everything is modular and developer-friendly. 
                                    Create modern admin panels with <a href="#" class="fw-medium" data-bs-toggle="tooltip" data-bs-title="Built with Bootstrap 5">responsive design</a> and seamless UX. 
                                    Get started quickly with a professional-grade <a href="#" class="fw-medium" data-bs-toggle="tooltip" data-bs-title="Tailored for developers">UI toolkit</a> and supercharge your app with <a href="#" class="fw-medium" data-bs-toggle="tooltip" data-bs-title="Includes charts, tables, forms and more">powerful components</a> and flexible layouts.
                                </p>
                                
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Disabled Elements</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Elements with the <code>disabled</code> attribute
                                    aren’t interactive, meaning users cannot focus, hover, or click them to trigger
                                    a tooltip (or popover). As a workaround, you’ll want to trigger the tooltip from
                                    a wrapper <code>&lt;div&gt;</code> or <code>&lt;span&gt;</code>, ideally made
                                    keyboard-focusable using <code>tabindex="0"</code>, and override the
                                    <code>pointer-events</code> on the disabled element.
                                </p>

                                <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="Disabled tooltip">
                                    <button class="btn btn-primary pe-none" type="button" disabled>Disabled Button</button>
                                </span>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Hover Elements</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Elements with the <code>disabled</code> attribute
                                    aren’t interactive, meaning users cannot focus, hover, or click them to trigger
                                    a tooltip (or popover). As a workaround, you’ll want to trigger the tooltip from
                                    a wrapper <code>&lt;div&gt;</code> or <code>&lt;span&gt;</code>, ideally made
                                    keyboard-focusable using <code>tabindex="0"</code>, and override the
                                    <code>pointer-events</code> on the disabled element.
                                </p>

                                <button class="btn btn-primary" type="button" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-title="Tooltip appears on hover only">Hover to See Info</button>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Four Directions</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Hover over the buttons below to see the four tooltips
                                    directions: top, right, bottom, and left.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top">Tooltip ontop</button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom">Tooltip on bottom</button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Tooltip on left">Tooltip on left</button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Tooltip on right">Tooltip on right</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">HTML Tags</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">And with custom HTML added:</p>

                                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<em>New</em> <u>Tooltip</u> <b>with</b> <i>HTML</i> <br>Custom message here!">
                                    Tooltip with HTML
                                </button>
                                
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Color Tooltips</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">We set a custom class with ex.
                                    <code>data-bs-custom-class="primary-tooltip"</code> to scope our background-color
                                    primary appearance and use
                                    it to override a local CSS
                                    variable.
                                </p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" data-bs-title="This is a primary tooltip with a custom style.">
                                        Primary tooltip
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" data-bs-title="This is a danger tooltip with a custom warning message.">
                                        Danger tooltip
                                    </button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" data-bs-title="This is an info tooltip that provides extra details.">
                                        Info tooltip
                                    </button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-success" data-bs-title="This is a success tooltip to indicate completion.">
                                        Success tooltip
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-secondary" data-bs-title="This is a secondary tooltip that gives additional information.">
                                        Secondary tooltip
                                    </button>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" data-bs-title="This is a warning tooltip to alert you.">
                                        Warning tooltip
                                    </button>
                                    <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-dark" data-bs-title="This is a dark tooltip with important information.">
                                        Dark tooltip
                                    </button>                                 
                                </div>
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