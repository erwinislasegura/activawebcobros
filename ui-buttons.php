<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Buttons"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Buttons"; include('partials/page-title.php'); ?> 
            
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Default Buttons</h4>
                                </div>

                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use any of the available <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code>, or <code>&lt;input&gt;</code> classes <code>.btn</code> to quickly create a styled button.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-default">Default</button>
                                    <button type="button" class="btn btn-primary">Primary</button>
                                    <button type="button" class="btn btn-secondary">Secondary</button>
                                    <button type="button" class="btn btn-success">Success</button>
                                    <button type="button" class="btn btn-danger">Danger</button>
                                    <button type="button" class="btn btn-warning">Warning</button>
                                    <button type="button" class="btn btn-info">Info</button>
                                    <button type="button" class="btn btn-light">Light</button>
                                    <button type="button" class="btn btn-dark">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Rounded</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use <code>.rounded-pill</code> with a default button to give it pill-shaped rounded corners.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-default rounded-pill">Default</button>
                                    <button type="button" class="btn btn-primary rounded-pill">Primary</button>
                                    <button type="button" class="btn btn-secondary rounded-pill">Secondary</button>
                                    <button type="button" class="btn btn-success rounded-pill">Success</button>
                                    <button type="button" class="btn btn-danger rounded-pill">Danger</button>
                                    <button type="button" class="btn btn-warning rounded-pill">Warning</button>
                                    <button type="button" class="btn btn-info rounded-pill">Info</button>
                                    <button type="button" class="btn btn-light rounded-pill">Light</button>
                                    <button type="button" class="btn btn-dark rounded-pill">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Outline</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use the <code>.btn-outline-**</code> classes to quickly create buttons with borders.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary">Primary</button>
                                    <button type="button" class="btn btn-outline-secondary">Secondary</button>
                                    <button type="button" class="btn btn-outline-success"> Success</button>
                                    <button type="button" class="btn btn-outline-danger">Danger</button>
                                    <button type="button" class="btn btn-outline-warning">Warning</button>
                                    <button type="button" class="btn btn-outline-info">Info</button>
                                    <button type="button" class="btn btn-outline-light">Light</button>
                                    <button type="button" class="btn btn-outline-dark">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Outline Rounded</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use <code>.rounded-pill</code> with an outline button to give it pill-shaped rounded corners.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary rounded-pill">Primary</button>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill">Secondary</button>
                                    <button type="button" class="btn btn-outline-success rounded-pill">Success</button>
                                    <button type="button" class="btn btn-outline-danger rounded-pill">Danger</button>
                                    <button type="button" class="btn btn-outline-warning rounded-pill">Warning</button>
                                    <button type="button" class="btn btn-outline-info rounded-pill">Info</button>
                                    <button type="button" class="btn btn-outline-light rounded-pill">Light</button>
                                    <button type="button" class="btn btn-outline-dark rounded-pill">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Soft Buttons</h4>
                                </div>

                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use <code>btn-soft-**</code> class with the below-mentioned variation to create a button with the soft background.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-soft-primary">Primary</button>
                                    <button type="button" class="btn btn-soft-secondary">Secondary</button>
                                    <button type="button" class="btn btn-soft-success">Success</button>
                                    <button type="button" class="btn btn-soft-danger">Danger</button>
                                    <button type="button" class="btn btn-soft-warning">Warning</button>
                                    <button type="button" class="btn btn-soft-info">Info</button>
                                    <button type="button" class="btn btn-soft-dark">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Soft Rounded Buttons</h4>
                                </div>

                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use the <code>btn-soft-**</code> class along with <code>.rounded-pill</code> to create a softly styled button with rounded corners.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-soft-primary rounded-pill">Primary</button>
                                    <button type="button" class="btn btn-soft-secondary rounded-pill">Secondary</button>
                                    <button type="button" class="btn btn-soft-success rounded-pill">Success</button>
                                    <button type="button" class="btn btn-soft-danger rounded-pill">Danger</button>
                                    <button type="button" class="btn btn-soft-warning rounded-pill">Warning</button>
                                    <button type="button" class="btn btn-soft-info rounded-pill">Info</button>
                                    <button type="button" class="btn btn-soft-dark rounded-pill">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Ghost Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use the <code>btn-ghost-**</code> class to create buttons with a transparent background that highlight with color on hover.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-ghost-primary">Primary</button>
                                    <button type="button" class="btn btn-ghost-secondary">Secondary</button>
                                    <button type="button" class="btn btn-ghost-success">Success</button>
                                    <button type="button" class="btn btn-ghost-danger">Danger</button>
                                    <button type="button" class="btn btn-ghost-warning">Warning</button>
                                    <button type="button" class="btn btn-ghost-info">Info</button>
                                    <button type="button" class="btn btn-ghost-dark">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Ghost Rounded Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use <code>btn-ghost-**</code> with <code>.rounded-pill</code> for rounded ghost buttons that highlight on hover.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-ghost-primary rounded-pill">Primary</button>
                                    <button type="button" class="btn btn-ghost-secondary rounded-pill">Secondary</button>
                                    <button type="button" class="btn btn-ghost-success rounded-pill">Success</button>
                                    <button type="button" class="btn btn-ghost-danger rounded-pill">Danger</button>
                                    <button type="button" class="btn btn-ghost-warning rounded-pill">Warning</button>
                                    <button type="button" class="btn btn-ghost-info rounded-pill">Info</button>
                                    <button type="button" class="btn btn-ghost-dark rounded-pill">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Gradient Buttons</h4>
                                </div>

                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use the <code>.bg-gradient</code> class to apply a gradient style to buttons.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-default bg-gradient">Default</button>
                                    <button type="button" class="btn btn-primary bg-gradient">Primary</button>
                                    <button type="button" class="btn btn-secondary bg-gradient">Secondary</button>
                                    <button type="button" class="btn btn-success bg-gradient">Success</button>
                                    <button type="button" class="btn btn-danger bg-gradient">Danger</button>
                                    <button type="button" class="btn btn-warning bg-gradient">Warning</button>
                                    <button type="button" class="btn btn-info bg-gradient">Info</button>
                                    <button type="button" class="btn btn-light bg-gradient">Light</button>
                                    <button type="button" class="btn btn-dark bg-gradient">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Gradient Rounded Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use the <code>.bg-gradient</code> and <code>.rounded-pill</code> classes to apply a gradient style with rounded edges to buttons.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-default rounded-pill bg-gradient">Default</button>
                                    <button type="button" class="btn btn-primary rounded-pill bg-gradient">Primary</button>
                                    <button type="button" class="btn btn-secondary rounded-pill bg-gradient">Secondary</button>
                                    <button type="button" class="btn btn-success rounded-pill bg-gradient">Success</button>
                                    <button type="button" class="btn btn-danger rounded-pill bg-gradient">Danger</button>
                                    <button type="button" class="btn btn-warning rounded-pill bg-gradient">Warning</button>
                                    <button type="button" class="btn btn-info rounded-pill bg-gradient">Info</button>
                                    <button type="button" class="btn btn-light rounded-pill bg-gradient">Light</button>
                                    <button type="button" class="btn btn-dark rounded-pill bg-gradient">Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Sizes</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Want larger or smaller buttons? Use <code>.btn-lg</code> or <code>.btn-sm</code> to adjust the button size.</p>

                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <button type="button" class="btn btn-primary btn-lg">Large</button>
                                    <button type="button" class="btn btn-info">Normal</button>
                                    <button type="button" class="btn btn-success btn-sm">Small</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Disabled Buttons</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use the <code>disabled</code> attribute on a <code>&lt;button&gt;</code> to make it inactive and non-interactive.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-info" disabled>Info</button>
                                    <button type="button" class="btn btn-soft-success" disabled>Success</button>
                                    <button type="button" class="btn btn-danger" disabled>Danger</button>
                                    <button type="button" class="btn btn-dark" disabled>Dark</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Block Button</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted font-14">To create block-level buttons, add the <code>.d-grid</code> class to the parent <code>&lt;div&gt;</code>.</p>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-sm btn-primary">Block Button</button>
                                    <button type="button" class="btn btn-lg btn-success">Block Button</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Toggle Button</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Add <code>data-bs-toggle="button"</code> to toggle a button’s <code>active</code> state. For pre-toggled buttons, also add <code>.active</code> and <code>aria-pressed="true"</code>.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="button">Toggle button</button>
                                    <button type="button" class="btn btn-primary active" data-bs-toggle="button" aria-pressed="true">Active toggle button</button>
                                    <button type="button" class="btn btn-primary" disabled data-bs-toggle="button">Disabled toggle button</button>
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div><!--end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Icon Buttons</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Icon only button. Use it when you want a button with just an icon and no text, ideal for compact UI elements or toolbars.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-success btn-icon">
                                        <i class="ti ti-check fs-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-icon">
                                        <i class="ti ti-trash fs-xl"></i>
                                    </button>
                                    <button type="button" class="btn btn-soft-primary rounded-circle btn-icon">
                                        <i class="ti ti-message fs-xxl"></i>
                                    </button>
                                    <button type="button" class="btn btn-info rounded-circle btn-icon">
                                        <i class="ti ti-share fs-xxl"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning rounded-circle btn-icon">
                                        <i class="ti ti-alert-triangle fs-xl"></i>
                                    </button>
                                    <button type="button" class="btn btn-soft-dark btn-icon">
                                        <i class="ti ti-user fs-xl"></i>
                                    </button>

                                    <button type="button" class="btn btn-light">
                                        <i class="ti ti-arrow-left align-middle me-1 fs-xl"></i> Back
                                    </button>
                                    <button type="button" class="btn btn-primary">
                                        <i data-lucide="rocket" class="avatar-xxs me-1"></i> Launch
                                    </button>

                                    <button type="button" class="btn btn-outline-success">
                                        <i class="ti ti-wallet align-middle me-1 fs-xl"></i> Wallet
                                    </button>
                                    <button type="button" class="btn btn-secondary">
                                        <i class="ti ti-settings fs-xl me-1"></i> <span>Settings</span>
                                    </button>
                                </div>


                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <!-- Small buttons -->
                                    <button class="btn btn-sm btn-outline-secondary btn-icon" type="button">
                                        <i class="ti ti-user"></i> <!-- Profile -->
                                    </button>
                                    <button class="btn btn-sm btn-primary btn-icon" type="button">
                                        <i class="ti ti-message-circle"></i> <!-- Message -->
                                    </button>
                                    <button class="btn btn-sm btn-success btn-icon rounded-circle" type="button">
                                        <i class="ti ti-send"></i> <!-- Share/Send -->
                                    </button>
                                    <button class="btn btn-sm btn-info btn-icon rounded-circle" type="button">
                                        <i class="ti ti-bell"></i> <!-- Notification -->
                                    </button>
                                    <button class="btn btn-sm btn-warning btn-icon" type="button">
                                        <i class="ti ti-alert-triangle"></i> <!-- Warning/Alert -->
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-icon" type="button">
                                        <i class="ti ti-trash"></i> <!-- Delete -->
                                    </button>

                                    <!-- Large buttons -->
                                    <button class="btn btn-outline-secondary btn-icon btn-lg" type="button">
                                        <i class="ti ti-user"></i>
                                    </button>
                                    <button class="btn btn-primary btn-icon btn-lg rounded-circle" type="button">
                                        <i class="ti ti-message-circle"></i>
                                    </button>
                                    <button class="btn btn-success btn-icon btn-lg rounded-circle" type="button">
                                        <i class="ti ti-send"></i>
                                    </button>
                                    <button class="btn btn-info btn-icon btn-lg" type="button">
                                        <i class="ti ti-bell"></i>
                                    </button>
                                    <button class="btn btn-warning btn-icon btn-lg" type="button">
                                        <i class="ti ti-alert-triangle"></i>
                                    </button>
                                    <button class="btn btn-danger btn-icon btn-lg" type="button">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-icon btn-lg" type="button">
                                        <i class="ti ti-heart"></i>
                                    </button>
                                </div>


                            </div> <!-- end card-body -->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Tags</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Use <code>.btn</code> classes with <code>&lt;button&gt;</code>, <code>&lt;a&gt;</code>, or <code>&lt;input&gt;</code> elements, though rendering may vary slightly across browsers.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-primary" href="#" role="button">Link</a>
                                    <button class="btn btn-primary" type="submit">Button</button>
                                    <input class="btn btn-primary" type="button" value="Input">
                                    <input class="btn btn-primary" type="submit" value="Submit">
                                    <input class="btn btn-primary" type="reset" value="Reset">
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Button Group</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Group multiple buttons together by wrapping them with the <code>.btn</code> class inside a <code>.btn-group</code> container. This helps align buttons side by side with consistent spacing and styling.</p>

                                <div class="btn-group mb-2">
                                    <button type="button" class="btn btn-light">Left</button>
                                    <button type="button" class="btn btn-light">Middle</button>
                                    <button type="button" class="btn btn-light">Right</button>
                                </div>

                                <br>

                                <div class="btn-group mb-2">
                                    <button type="button" class="btn btn-light">1</button>
                                    <button type="button" class="btn btn-light">2</button>
                                    <button type="button" class="btn btn-light">3</button>
                                    <button type="button" class="btn btn-light">4</button>
                                </div>

                                <div class="btn-group mb-2">
                                    <button type="button" class="btn btn-light">5</button>
                                    <button type="button" class="btn btn-light">6</button>
                                    <button type="button" class="btn btn-light">7</button>
                                </div>

                                <div class="btn-group mb-2">
                                    <button type="button" class="btn btn-light">8</button>
                                </div>

                                <br>

                                <div class="btn-group mb-2">
                                    <button type="button" class="btn btn-light">1</button>
                                    <button type="button" class="btn btn-primary">2</button>
                                    <button type="button" class="btn btn-light">3</button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> Dropdown <span class="caret"></span> </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">Dropdown link</a>
                                            <a class="dropdown-item" href="#">Dropdown link</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="btn-group-vertical mb-2">
                                            <button type="button" class="btn btn-light">Top</button>
                                            <button type="button" class="btn btn-light">Middle</button>
                                            <button type="button" class="btn btn-light">Bottom</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="btn-group-vertical mb-2">
                                            <button type="button" class="btn btn-light">Button 1</button>
                                            <button type="button" class="btn btn-light">Button 2</button>
                                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> Button 3 <span class="caret"></span> </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">Dropdown link</a>
                                                <a class="dropdown-item" href="#">Dropdown link</a>
                                            </div>
                                        </div>
                                    </div>
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