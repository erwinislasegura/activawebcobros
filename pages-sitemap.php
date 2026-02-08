<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Sitemap"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Pages"; $title = "Sitemap"; include('partials/page-title.php'); ?>

                <div class="row">
                    <!-- Column 1: Dashboard & Pages -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="fw-bold text-uppercase">
                                    <i class="ti ti-circle-dot me-1"></i> Dashboard & Pages
                                </h5>
                                <ul class="list-unstyled sitemap-list mt-3">
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-layout-dashboard me-1 text-muted"></i> Dashboards</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">Analytics</a></li>
                                            <li><a href="#" class="link-reset">CRM</a></li>
                                            <li><a href="#" class="link-reset">Sales</a></li>
                                            <li><a href="#" class="link-reset">Minimal</a></li>
                                            <li><a href="#" class="link-reset">eCommerce</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-user me-1 text-muted"></i> Profile</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">Overview</a></li>
                                            <li><a href="#" class="link-reset">Edit</a></li>
                                            <li><a href="#" class="link-reset">Security</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#" class="link-reset fw-semibold"><i class="ti ti-help me-1 text-muted"></i> Help Center</a></li>
                                    <li><a href="#" class="link-reset fw-semibold"><i class="ti ti-lock me-1 text-muted"></i> Login</a></li>
                                    <li><a href="#" class="link-reset fw-semibold"><i class="ti ti-user-plus me-1 text-muted"></i> Register</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Apps & Management -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="fw-bold text-uppercase">
                                    <i class="ti ti-circle-dot me-1"></i> Applications
                                </h5>
                                <ul class="list-unstyled sitemap-list mt-3">
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-calendar me-1 text-muted"></i> Calendar</a>
                                    </li>
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-mail me-1 text-muted"></i> Email</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">Inbox</a></li>
                                            <li><a href="#" class="link-reset">Read</a></li>
                                            <li><a href="#" class="link-reset">Compose</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-users me-1 text-muted"></i> Users</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">List</a></li>
                                            <li><a href="#" class="link-reset">Add User</a></li>
                                            <li><a href="#" class="link-reset">Roles</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-notebook me-1 text-muted"></i> Projects</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">Overview</a></li>
                                            <li><a href="#" class="link-reset">Create</a></li>
                                            <li><a href="#" class="link-reset">Tasks</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Settings & Reports -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="fw-bold text-uppercase">
                                    <i class="ti ti-circle-dot me-1"></i> Reports & Settings
                                </h5>
                                <ul class="list-unstyled sitemap-list mt-3">
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-chart-bar me-1 text-muted"></i> Reports</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">Sales</a></li>
                                            <li><a href="#" class="link-reset">Users</a></li>
                                            <li><a href="#" class="link-reset">Performance</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-wallet me-1 text-muted"></i> Billing</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">Invoices</a></li>
                                            <li><a href="#" class="link-reset">Payments</a></li>
                                            <li><a href="#" class="link-reset">Methods</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#" class="link-reset fw-semibold"><i class="ti ti-settings me-1 text-muted"></i> Settings</a>
                                        <ul>
                                            <li><a href="#" class="link-reset">General</a></li>
                                            <li><a href="#" class="link-reset">Appearance</a></li>
                                            <li><a href="#" class="link-reset">Integrations</a></li>
                                            <li><a href="#" class="link-reset">Audit Logs</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#" class="link-reset fw-semibold"><i class="ti ti-logout me-1 text-muted"></i> Logout</a></li>
                                </ul>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
                <!-- end row-->

            </div>
            <!-- container -->

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