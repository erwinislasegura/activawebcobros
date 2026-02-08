<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Pipeline"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Pipeline"; include('partials/page-title.php'); ?>

                <div class="outlook-box kanban-app">
                    <div class="card h-100 mb-0 flex-grow-1">
                        <div class="card-header d-none d-lg-flex border-light align-items-center gap-2">
                            <!-- Search Bar -->
                            <div class="app-search">
                                <input type="search" class="form-control" placeholder="Search deals...">
                                <i data-lucide="search" class="app-search-icon text-muted"></i>
                            </div>

                            <!-- Filter Controls -->
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <!-- Stage Filter -->
                                <div class="app-search">
                                    <select class="form-select form-control">
                                        <option selected>Stage</option>
                                        <option value="Qualification">Qualification</option>
                                        <option value="Proposal Sent">Proposal Sent</option>
                                        <option value="Negotiation">Negotiation</option>
                                        <option value="Won">Won</option>
                                        <option value="Lost">Lost</option>
                                    </select>
                                    <i data-lucide="shuffle" class="app-search-icon text-muted"></i>
                                </div>

                                <!-- Closing Date Filter -->
                                <div class="app-search">
                                    <select class="form-select form-control">
                                        <option selected>Closing Date</option>
                                        <option value="Today">Today</option>
                                        <option value="This Week">This Week</option>
                                        <option value="This Month">This Month</option>
                                    </select>
                                    <i data-lucide="calendar-check" class="app-search-icon text-muted"></i>
                                </div>
                            </div>

                            <!-- Add Deal Button -->
                            <button type="button" class="btn btn-secondary ms-lg-auto">
                                <i class="ti ti-plus me-1"></i> Add New Deal
                            </button>
                        </div>

                        <div class="card-body p-0">
                            <div class="kanban-content">

                                <!-- Lead Data-->
                                <div class="kanban-board bg-warning bg-opacity-10">
                                    <div class="kanban-item py-2 px-3 d-flex align-items-center">
                                        <h5 class="m-0">Lead (4)</h5>
                                        <a href="#!" class="ms-auto btn btn-sm btn-icon rounded-circle btn-primary"><i class="ti ti-plus fs-md"></i></a>
                                    </div>
                                    <div class="kanban-board-group px-2" data-simplebar data-simplebar-md>
                                        <ul data-plugins="sortable">
                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <!-- Title + Actions -->
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">AI Analytics Dashboard</a>
                                                                </h5>
                                                                <small class="text-muted">Amazon Web Services</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-check me-2"></i>Assign</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <!-- Team + Closing Date -->
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-4.jpg" class="rounded-circle avatar-xs" alt="Mark Allen">
                                                                <span class="fw-medium text-muted fs-sm">Mark Allen</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-time text-muted fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">30 May, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <!-- Deal Amount -->
                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-message-dots text-muted fs-lg"></i> 2
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-checklist text-muted fs-lg"></i> 3
                                                                    </span>
                                                                </div>
                                                                <span class="fw-semibold">$95,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">Mobile App Redesign</a>
                                                                </h5>
                                                                <small class="text-muted">ByteCraft Studios</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-check me-2"></i>Assign</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-2.jpg" class="rounded-circle avatar-xs" alt="Alex Carter">
                                                                <span class="fw-medium text-muted fs-sm">Alex Carter</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-time text-muted fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">12 Jun, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-message-dots text-muted fs-lg"></i> 1
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-checklist text-muted fs-lg"></i> 5
                                                                    </span>
                                                                </div>
                                                                <span class="fw-semibold">$72,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">Website Revamp</a>
                                                                </h5>
                                                                <small class="text-muted">NextGen UI</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-check me-2"></i>Assign</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-5.jpg" class="rounded-circle avatar-xs" alt="Emily Rose">
                                                                <span class="fw-medium text-muted fs-sm">Emily Rose</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-time text-muted fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">18 Jun, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-message-dots text-muted fs-lg"></i> 4
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-checklist text-muted fs-lg"></i> 2
                                                                    </span>
                                                                </div>
                                                                <span class="fw-semibold">$45,500</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">Campaign Strategy</a>
                                                                </h5>
                                                                <small class="text-muted">Visionary Labs</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-check me-2"></i>Assign</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-6.jpg" class="rounded-circle avatar-xs" alt="Ryan King">
                                                                <span class="fw-medium text-muted fs-sm">Ryan King</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-time text-muted fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">5 Jul, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-message-dots text-muted fs-lg"></i> 0
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-checklist text-muted fs-lg"></i> 1
                                                                    </span>
                                                                </div>
                                                                <span class="fw-semibold">$23,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <!-- end Lead Data-->

                                <!-- Negotiation Data-->
                                <div class="kanban-board bg-info bg-opacity-10">
                                    <div class="py-2 px-3 d-flex align-items-center">
                                        <h5 class="m-0">Negotiation (2)</h5>
                                        <a href="#!" class="ms-auto btn btn-sm btn-icon rounded-circle btn-primary"><i class="ti ti-plus fs-md"></i></a>
                                    </div>
                                    <div class="kanban-board-group px-2" data-simplebar data-simplebar-md>
                                        <ul data-plugins="sortable">

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">Product Demo Scheduling</a>
                                                                </h5>
                                                                <small class="text-muted">Innovexa</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-check me-2"></i>Assign</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-8.jpg" class="rounded-circle avatar-xs" alt="Nina White">
                                                                <span class="fw-medium text-muted fs-sm">Nina White</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-time text-muted fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">15 Jul, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-message-dots text-muted fs-lg"></i> 3
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-checklist text-muted fs-lg"></i> 4
                                                                    </span>
                                                                </div>
                                                                <span class="fw-semibold">$18,750</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">CRM Integration Task</a>
                                                                </h5>
                                                                <small class="text-muted">CoreSync Ltd.</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-check me-2"></i>Assign</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-9.jpg" class="rounded-circle avatar-xs" alt="Amit Rao">
                                                                <span class="fw-medium text-muted fs-sm">Amit Rao</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-time text-muted fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">22 Jul, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-message-dots text-muted fs-lg"></i> 1
                                                                    </span>
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="ti ti-checklist text-muted fs-lg"></i> 2
                                                                    </span>
                                                                </div>
                                                                <span class="fw-semibold">$39,900</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <!-- end Negotiation Data-->

                                <!-- Won Data-->
                                <div class="kanban-board bg-success bg-opacity-10">
                                    <div class="kanban-item py-2 px-3 d-flex align-items-center">
                                        <h5 class="m-0">Won (5)</h5>
                                        <a href="#!" class="ms-auto btn btn-sm btn-icon rounded-circle btn-primary"><i class="ti ti-plus fs-md"></i></a>
                                    </div>
                                    <div class="kanban-board-group px-2" data-simplebar data-simplebar-md>
                                        <ul data-plugins="sortable">
                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold"><a href="#!" class="link-reset">Enterprise License Upgrade</a></h5>
                                                                <small class="text-muted">Zentrix Corp</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-share me-2"></i>Share</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-1.jpg" class="rounded-circle avatar-xs" alt="Sophia Lee">
                                                                <span class="fw-medium text-muted fs-sm">Sophia Lee</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-check text-success fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">01 Jul, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-award text-success fs-lg"></i> Won
                                                                </div>
                                                                <span class="fw-semibold">$120,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold"><a href="#!" class="link-reset">Custom CRM Implementation</a></h5>
                                                                <small class="text-muted">DeltaSoft</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-2.jpg" class="rounded-circle avatar-xs" alt="Mark J.">
                                                                <span class="fw-medium text-muted fs-sm">Mark J.</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-check text-success fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">28 Jun, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-award text-success fs-lg"></i> Won
                                                                </div>
                                                                <span class="fw-semibold">$89,500</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>


                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold"><a href="#!" class="link-reset">API Subscription Expansion</a></h5>
                                                                <small class="text-muted">Netwise Solutions</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-3.jpg" class="rounded-circle avatar-xs" alt="Raj Patel">
                                                                <span class="fw-medium text-muted fs-sm">Raj Patel</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-check text-success fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">25 Jun, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-award text-success fs-lg"></i> Won
                                                                </div>
                                                                <span class="fw-semibold">$58,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold"><a href="#!" class="link-reset">Annual Cloud Retainer</a></h5>
                                                                <small class="text-muted">SkyVault Inc.</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-5.jpg" class="rounded-circle avatar-xs" alt="Tina Ray">
                                                                <span class="fw-medium text-muted fs-sm">Tina Ray</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-check text-success fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">21 Jun, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-award text-success fs-lg"></i> Won
                                                                </div>
                                                                <span class="fw-semibold">$135,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold"><a href="#!" class="link-reset">Marketing Automation Deal</a></h5>
                                                                <small class="text-muted">CloudReach</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-6.jpg" class="rounded-circle avatar-xs" alt="Mohit Chauhan">
                                                                <span class="fw-medium text-muted fs-sm">Mohit Chauhan</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-check text-success fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">18 Jun, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-award text-success fs-lg"></i> Won
                                                                </div>
                                                                <span class="fw-semibold">$62,500</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <!-- end Won Data-->

                                <!-- Lost Data-->
                                <div class="kanban-board bg-danger bg-opacity-10">
                                    <div class="py-2 px-3 d-flex align-items-center">
                                        <h5 class="m-0">Lost (2)</h5>
                                        <a href="#!" class="ms-auto btn btn-sm btn-icon rounded-circle btn-primary"><i class="ti ti-plus fs-md"></i></a>
                                    </div>
                                    <div class="kanban-board-group px-2" data-simplebar data-simplebar-md>
                                        <ul data-plugins="sortable">

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2 border-danger-subtle">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">E-commerce Platform Proposal</a>
                                                                </h5>
                                                                <small class="text-muted">QuickCart</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-7.jpg" class="rounded-circle avatar-xs" alt="Julia Mason">
                                                                <span class="fw-medium text-muted fs-sm">Julia Mason</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-x text-danger fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">14 Jul, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-x text-danger fs-lg"></i> Lost
                                                                </div>
                                                                <span class="fw-semibold text-danger">$55,000</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="kanban-item">
                                                <div class="card shadow mb-2 border-danger-subtle">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <div>
                                                                <h5 class="mb-0 fw-semibold">
                                                                    <a href="#!" class="link-reset">Social Media Integration Deal</a>
                                                                </h5>
                                                                <small class="text-muted">BuzzPro Media</small>
                                                            </div>
                                                            <div class="ms-auto dropdown">
                                                                <a href="#" class="btn btn-icon btn-sm btn-ghost-light text-muted" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <img src="assets/images/users/user-8.jpg" class="rounded-circle avatar-xs" alt="Ethan Cruz">
                                                                <span class="fw-medium text-muted fs-sm">Ethan Cruz</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-calendar-x text-danger fs-lg"></i>
                                                                <h5 class="fs-base mb-0 fw-medium">10 Jul, 2025</h5>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center gap-2 fs-sm">
                                                                    <i class="ti ti-x text-danger fs-lg"></i> Lost
                                                                </div>
                                                                <span class="fw-semibold text-danger">$38,400</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- end Done Data-->

                            </div> <!-- End Kanban-content-->
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end row-->

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

    <!-- Apex Charts js -->
    <script src="assets/plugins/sortablejs/Sortable.min.js"></script>

    <!-- Dashboard App js -->
    <script src="assets/js/pages/crm-pipeline.js"></script>

</body>

</html>