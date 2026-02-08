<?php include('partials/html.php'); ?>

<head>
    <?php $title = "API Keys"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Apps"; $title = "API Keys"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div data-table data-table-rows-per-page="8" class="card">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input data-table-search type="text" class="form-control" placeholder="Search API clients...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-icon" data-bs-toggle="modal" data-bs-target="#addApiKeyModal"><i class="ti ti-plus fs-lg"></i></button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <!-- Status Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="All">Status</option>
                                            <option value="Active">Active</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Revoked">Revoked</option>
                                            <option value="Suspended">Suspended</option>
                                            <option value="Expired">Expired</option>
                                        </select>
                                        <i data-lucide="circle-check" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Region Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="region" class="form-select form-control my-1 my-md-0">
                                            <option value="All">Region</option>
                                            <option value="US">USA</option>
                                            <option value="UK">UK</option>
                                            <option value="IN">India</option>
                                            <option value="DE">Germany</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                        <i data-lucide="globe" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Records Per Page -->
                                    <div>
                                        <select data-table-set-rows-per-page class="form-select form-control my-1 my-md-0">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table text-nowrap table-custom table-centered table-hover w-100 mb-0">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th scope="col" style="width: 1%;">
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" data-table-select-all type="checkbox" value="option">
                                            </th>
                                            <th data-table-sort>Name</th>
                                            <th data-table-sort="name">Created By</th>
                                            <th>API Key</th>
                                            <th data-table-sort data-column="status">Status</th>
                                            <th>Usage</th>
                                            <th data-table-sort>Created</th>
                                            <th data-table-sort>Expires</th>
                                            <th data-table-sort data-column="region">Region</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead><!-- end table-head -->
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">APINexus</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-2.jpg" alt="avatar-2" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Mark Reynolds</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyOne" readonly value="e4A7Fc98z120XYz776abc90MNZ">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyOne">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning-subtle text-warning badge-label">Pending</span>
                                            </td>
                                            <td><span>245 / 1000</span></td>
                                            <td>Jan 10, 2025</td>
                                            <td>Nov 15, 2025</td>
                                            <td>
                                                <span class="d-flex align-items-center fs-sm fw-bold">
                                                    <img src="assets/images/flags/de.svg" alt="" class="rounded-circle me-1" height="12"> DE
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">DataPulse</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-4.jpg" alt="avatar-4" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Sophia Turner</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyTwo" readonly value="9BcD456Xy78LmN0zPQR12sA3Z">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyTwo">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger-subtle text-danger badge-label">Revoked</span>
                                            </td>
                                            <td><span>847 / 1000</span></td>
                                            <td>Mar 5, 2025</td>
                                            <td>Aug 20, 2025</td>
                                            <td>
                                                <span class="d-flex align-items-center fs-sm fw-bold">
                                                    <img src="assets/images/flags/gb.svg" alt="" class="rounded-circle me-1" height="12"> UK
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">AuthKit</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-5.jpg" alt="avatar-5" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Liam Watson</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyThree" readonly value="ZZ99xC8K23Fm10TyPLqZa17d">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyThree">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success badge-label">Active</span>
                                            </td>
                                            <td><span>105 / 700</span></td>
                                            <td>Apr 22, 2025</td>
                                            <td>Dec 31, 2025</td>
                                            <td>
                                                <span class="d-flex align-items-center fs-sm fw-bold">
                                                    <img src="assets/images/flags/in.svg" alt="" class="rounded-circle me-1" height="12"> IN
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">APIxEdge</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-2.jpg" alt="avatar-2" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Ava Turner</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyFour" readonly value="XY91kLpB42Ga98WxRTzEe55n">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyFour">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-warning-subtle text-warning badge-label">Pending</span>
                                            </td>
                                            <td><span>0 / 500</span></td>
                                            <td>Apr 10, 2025</td>
                                            <td>Oct 10, 2025</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/us.svg" alt="" class="rounded-circle me-1" height="12"> US</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">DataLinker</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-7.jpg" alt="avatar-7" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Noah Reed</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyFive" readonly value="BB22zWqT65Op90VxMLaRt18c">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyFive">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-danger-subtle text-danger badge-label">Suspended</span>
                                            </td>
                                            <td><span>369 / 1000</span></td>
                                            <td>Mar 15, 2025</td>
                                            <td>Sep 15, 2025</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/de.svg" alt="" class="rounded-circle me-1" height="12"> DE</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">WebhookMate</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-3.jpg" alt="avatar-3" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Sophia Lee</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keySix" readonly value="RM19yUlP75Kl44YvNJdHg09s">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keySix">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span>
                                            </td>
                                            <td><span>459 / 600</span></td>
                                            <td>Jan 01, 2025</td>
                                            <td>Dec 31, 2025</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/gb.svg" alt="" class="rounded-circle me-1" height="12"> UK</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">DevPortal</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-4.jpg" alt="avatar-4" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Mason Clark</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keySeven" readonly value="AA47qBcJ61Tr77WpKKzTy39k">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keySeven">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-info-subtle text-info badge-label">Trial</span></td>
                                            <td><span>0 / 100</span></td>
                                            <td>Feb 01, 2025</td>
                                            <td>May 01, 2025</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/au.svg" alt="" class="rounded-circle me-1" height="12"> AU</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">NotifyX</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-6.jpg" alt="avatar-6" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Ella James</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyEight" readonly value="ZP83mXcD28Uv11MtYYoXx03b">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyEight">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span>
                                            </td>
                                            <td><span>3584 / 5000</span></td>
                                            <td>Apr 01, 2025</td>
                                            <td>Jan 01, 2026</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/ca.svg" alt="" class="rounded-circle me-1" height="12"> CA</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">TokenVault</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-8.jpg" alt="avatar-8" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Lucas Hill</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyNine" readonly value="LK35yTrF82Lo99UiSSpPr47x">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyNine">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-secondary-subtle text-secondary badge-label">Expired</span>
                                            </td>
                                            <td><span>958 / 1000</span></td>
                                            <td>Jul 15, 2024</td>
                                            <td>Jan 15, 2025</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/fr.svg" alt="" class="rounded-circle me-1" height="12"> FR</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" value="option">
                                            </td>
                                            <td class="fw-medium">StreamAPI</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <div class="avatar avatar-xs">
                                                        <img src="assets/images/users/user-9.jpg" alt="avatar-9" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 data-sort="name" class="text-nowrap fw-medium fs-sm mb-0 lh-base">Mia Bennett</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" id="keyTen" readonly value="DW64aUvQ11Gh32PpDDjWw72t">
                                                    <button class="btn btn-sm btn-icon btn-default" type="button" data-clipboard-target="#keyTen">
                                                        <i class="ti ti-copy fs-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span>
                                            </td>
                                            <td><span>78 / 100</span></td>
                                            <td>Mar 05, 2025</td>
                                            <td>Dec 05, 2025</td>
                                            <td><span class="d-flex align-items-center fs-sm fw-bold"><img src="assets/images/flags/in.svg" alt="" class="rounded-circle me-1" height="12"> IN</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody><!-- end table-body -->
                                </table><!-- end table -->
                            </div>
                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="apis"></div>

                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->

                <!-- Add API Key Modal -->
                <div class="modal fade" id="addApiKeyModal" tabindex="-1" aria-labelledby="addApiKeyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="addApiKeyModalLabel">Add New API Key</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form>
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label">Client Name</label>
                                            <input type="text" class="form-control" placeholder="Enter client name">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Created By</label>
                                            <select class="form-select">
                                                <option selected disabled>Select user</option>
                                                <option>Mark Reynolds</option>
                                                <option>Sophia Turner</option>
                                                <option>Liam Watson</option>
                                                <option>Ava Turner</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">API Key</label>
                                            <div class="input-group">
                                                <input type="text" id="apiKeyInput" class="form-control" placeholder="Enter or generate API key">
                                                <button type="button" class="btn btn-secondary" onclick="generateApiKey()">Generate</button>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select class="form-select">
                                                <option value="Active">Active</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Revoked">Revoked</option>
                                                <option value="Suspended">Suspended</option>
                                                <option value="Trial">Trial</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Usage Limit</label>
                                            <input type="text" class="form-control" placeholder="e.g. 1000">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Region</label>
                                            <select class="form-select">
                                                <option value="DE">🇩🇪 Germany</option>
                                                <option value="UK">🇬🇧 UK</option>
                                                <option value="IN">🇮🇳 India</option>
                                                <option value="US">🇺🇸 USA</option>
                                                <option value="AU">🇦🇺 Australia</option>
                                                <option value="CA">🇨🇦 Canada</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Created On</label>
                                            <input type="date" data-provider="flatpickr" data-date-format="d M, Y" class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Expires On</label>
                                            <input type="date" data-provider="flatpickr" data-date-format="d M, Y" class="form-control">
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add API Key</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


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

    <!-- Clipboard js -->
    <script src="assets/plugins/clipboard/clipboard.min.js"></script>

    <!-- Custom table -->
    <script src="assets/js/pages/custom-table.js"></script>

    <!-- API Key Page js -->
    <script src="assets/js/pages/api-keys.js"></script>

</body>

</html>