<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Estimations"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Estimations"; include('partials/page-title.php'); ?>

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-2">

                    <!-- Total Estimations Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">52</h5>
                                    <span>+15.7% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Total estimations created</p>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Estimations Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">24</h5>
                                    <span>+10.2% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Approved estimations</p>
                            </div>
                        </div>
                    </div>

                    <!-- Declined Estimations Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">8</h5>
                                    <span>-3.9% <i class="ti ti-arrow-down text-danger"></i></span>
                                </div>
                                <p class="text-muted mb-0">Declined estimations</p>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Estimation Value Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">$138,500</h5>
                                    <span>Top value <i class="ti ti-currency-dollar text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Highest estimation value</p>
                            </div>
                        </div>
                    </div>

                    <!-- Avg. Estimation Review Time Widget -->
                    <div class="col-lg col-md-auto">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">2.8 <small class="fs-6">days</small></h5>
                                    <span>+1.1% <i class="ti ti-clock text-warning"></i></span>
                                </div>
                                <p class="text-muted mb-0">Avg. review time</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div data-table data-table-rows-per-page="8" class="card">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input data-table-search type="search" class="form-control" placeholder="Search deals...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEstimationModal">
                                        <i class="ti ti-plus me-1"></i> New Estimation
                                    </button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <!-- Status Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Status</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Declined">Declined</option>
                                            <option value="Sent">Sent</option>
                                            <option value="In Review">In Review</option>
                                        </select>
                                        <i data-lucide="shuffle" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Value Range Filter -->
                                    <div class="app-search">
                                        <select data-table-range-filter="value" class="form-select form-control my-1 my-md-0">
                                            <option value="">Value Range</option>
                                            <option value="0-5000">$0 - $5,000</option>
                                            <option value="5001-10000">$5,001 - $10,000</option>
                                            <option value="10001-20000">$10,001 - $20,000</option>
                                            <option value="20001-50000">$20,001 - $50,000</option>
                                            <option value="50000+">$50,000+</option>
                                        </select>
                                        <i data-lucide="dollar-sign" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Records Per Page -->
                                    <div>
                                        <select data-table-set-rows-per-page class="form-select form-control my-1 my-md-0">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                        <thead class="bg-light align-middle bg-opacity-25 thead-sm text-nowrap">
                                            <tr class="text-uppercase fs-xxs">
                                                <th class="ps-3" style="width: 1%;">
                                                    <input data-table-select-all class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" id="select-all-estimations" value="option">
                                                </th>
                                                <th data-table-sort>Estimate ID</th>
                                                <th>Project</th>
                                                <th>Client</th>
                                                <th data-table-sort data-column="value">Estimated Value</th>
                                                <th>Estimated By</th>
                                                <th data-table-sort>Created</th>
                                                <th data-table-sort>Expected Close</th>
                                                <th data-table-sort data-column="status">Status</th>
                                                <th class="text-center" style="width: 1%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <!-- Row 1 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00042</a></td>
                                                <td>CRM System Redesign</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/airbnb.svg" alt="Salesforce" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Airbnb</a>
                                                    </div>
                                                </td>
                                                <td>$25,000</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-5.jpg" alt="Jason Miller" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Jason Miller</a>
                                                    </div>
                                                </td>
                                                <td>15 Jul, 2025 <small class="text-muted">09:30 AM</small></td>
                                                <td>05 Aug, 2025 <small class="text-muted">06:00 PM</small></td>
                                                <td><span class="badge badge-label bg-info-subtle text-info fs-xs">In Review</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 2 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00043</a></td>
                                                <td>Lead Tracking Module</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/amazon.svg" alt="Zendesk" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Amazon</a>
                                                    </div>
                                                </td>
                                                <td>$14,200</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-4.jpg" alt="Ava Green" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Ava Green</a>
                                                    </div>
                                                </td>
                                                <td>17 Jul, 2025 <small class="text-muted">10:15 AM</small></td>
                                                <td>02 Aug, 2025 <small class="text-muted">04:30 PM</small></td>
                                                <td><span class="badge badge-label bg-success-subtle text-success fs-xs">Approved</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 3 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00044</a></td>
                                                <td>Marketing Automation</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/apple.svg" alt="HubSpot" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Apple Inc.</a>
                                                    </div>
                                                </td>
                                                <td>$32,000</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-2.jpg" alt="Liam Scott" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Liam Scott</a>
                                                    </div>
                                                </td>
                                                <td>18 Jul, 2025 <small class="text-muted">01:40 PM</small></td>
                                                <td>10 Aug, 2025 <small class="text-muted">01:00 PM</small></td>
                                                <td><span class="badge badge-label bg-warning-subtle text-warning fs-xs">Pending</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 4 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00045</a></td>
                                                <td>Sales Pipeline Setup</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/asana.svg" alt="Pipedrive" height="16">
                                                        </div>
                                                        <a href="#!" class="link-reset">Asana Studio</a>
                                                    </div>
                                                </td>
                                                <td>$21,800</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-3.jpg" alt="Noah Carter" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Noah Carter</a>
                                                    </div>
                                                </td>
                                                <td>19 Jul, 2025 <small class="text-muted">03:00 PM</small></td>
                                                <td>08 Aug, 2025 <small class="text-muted">05:45 PM</small></td>
                                                <td><span class="badge badge-label bg-danger-subtle text-danger fs-xs">Declined</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 5 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00046</a></td>
                                                <td>Support Portal Development</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/dropbox.svg" alt="Intercom" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Dropbox Inc.</a>
                                                    </div>
                                                </td>
                                                <td>$27,500</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-1.jpg" alt="Emily Stone" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Emily Stone</a>
                                                    </div>
                                                </td>
                                                <td>20 Jul, 2025 <small class="text-muted">11:20 AM</small></td>
                                                <td>15 Aug, 2025 <small class="text-muted">09:00 AM</small></td>
                                                <td><span class="badge badge-label bg-info-subtle text-info fs-xs">In Review</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 6 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00047</a></td>
                                                <td>Client Onboarding System</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/figma.svg" alt="Freshdesk" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Figma Design</a>
                                                    </div>
                                                </td>
                                                <td>$16,600</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-6.jpg" alt="Sophia White" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Sophia White</a>
                                                    </div>
                                                </td>
                                                <td>21 Jul, 2025 <small class="text-muted">02:00 PM</small></td>
                                                <td>06 Aug, 2025 <small class="text-muted">12:00 PM</small></td>
                                                <td><span class="badge badge-label bg-secondary-subtle text-secondary fs-xs">Sent</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 7 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00048</a></td>
                                                <td>Customer Insights Dashboard</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 d-flex justify-content-center align-items-center">
                                                            <img src="assets/images/logos/meta.svg" alt="SAP" height="14">
                                                        </div>
                                                        <a href="#!" class="link-reset">Meta</a>
                                                    </div>
                                                </td>
                                                <td>$29,900</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-7.jpg" alt="Mason Lee" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Mason Lee</a>
                                                    </div>
                                                </td>
                                                <td>22 Jul, 2025 <small class="text-muted">11:00 AM</small></td>
                                                <td>10 Aug, 2025 <small class="text-muted">04:00 PM</small></td>
                                                <td><span class="badge badge-label bg-info-subtle text-info fs-xs">In Review</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 8 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00049</a></td>
                                                <td>Workflow Automation Engine</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 d-flex justify-content-center align-items-center">
                                                            <img src="assets/images/logos/slack.svg" alt="Slack" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Slack</a>
                                                    </div>
                                                </td>
                                                <td>$33,750</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-8.jpg" alt="Olivia James" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Olivia James</a>
                                                    </div>
                                                </td>
                                                <td>23 Jul, 2025 <small class="text-muted">09:30 AM</small></td>
                                                <td>14 Aug, 2025 <small class="text-muted">11:30 AM</small></td>
                                                <td><span class="badge badge-label bg-success-subtle text-success fs-xs">Approved</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 9 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00050</a></td>
                                                <td>Partner API Integration</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 d-flex justify-content-center align-items-center">
                                                            <img src="assets/images/logos/microsoft.svg" alt="Zoom" height="16">
                                                        </div>
                                                        <a href="#!" class="link-reset">Microsoft</a>
                                                    </div>
                                                </td>
                                                <td>$17,600</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-9.jpg" alt="Ella Kim" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Ella Kim</a>
                                                    </div>
                                                </td>
                                                <td>24 Jul, 2025 <small class="text-muted">10:45 AM</small></td>
                                                <td>12 Aug, 2025 <small class="text-muted">03:15 PM</small></td>
                                                <td><span class="badge badge-label bg-warning-subtle text-warning fs-xs">Pending</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 10 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00051</a></td>
                                                <td>Mobile CRM App Build</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 d-flex justify-content-center align-items-center">
                                                            <img src="assets/images/logos/openai.svg" alt="Airtable" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">ChatGPT</a>
                                                    </div>
                                                </td>
                                                <td>$40,000</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-10.jpg" alt="Daniel Park" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Daniel Park</a>
                                                    </div>
                                                </td>
                                                <td>25 Jul, 2025 <small class="text-muted">02:30 PM</small></td>
                                                <td>20 Aug, 2025 <small class="text-muted">05:00 PM</small></td>
                                                <td><span class="badge badge-label bg-secondary-subtle text-secondary fs-xs">Sent</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 11 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 estimation-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#EST00052</a></td>
                                                <td>Smart Contact Syncing</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 d-flex justify-content-center align-items-center">
                                                            <img src="assets/images/logos/shell.svg" alt="Zoho" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Shell Petro.</a>
                                                    </div>
                                                </td>
                                                <td>$13,250</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-4.jpg" alt="Chloe Nguyen" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Chloe Nguyen</a>
                                                    </div>
                                                </td>
                                                <td>26 Jul, 2025 <small class="text-muted">01:10 PM</small></td>
                                                <td>16 Aug, 2025 <small class="text-muted">11:00 AM</small></td>
                                                <td><span class="badge badge-label bg-danger-subtle text-danger fs-xs">Declined</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>


                                </div> <!-- end table-respo.-->

                            </div> <!-- end card-body-->

                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="proposals"></div>
                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row-->


                <!-- Create Estimation Modal -->
                <div class="modal fade" id="createEstimationModal" tabindex="-1" aria-labelledby="createEstimationModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="createEstimationModalLabel">Create New Estimation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="createEstimationForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="estimationTitle" class="form-label">Project Name</label>
                                            <input type="text" class="form-control" id="estimationTitle" placeholder="Enter project name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="clientName" class="form-label">Client</label>
                                            <input type="text" class="form-control" id="clientName" placeholder="Enter client name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="estimatedValue" class="form-label">Estimated Value (USD)</label>
                                            <input type="number" class="form-control" id="estimatedValue" placeholder="e.g. 25000" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="estimator" class="form-label">Estimated By</label>
                                            <input type="text" class="form-control" id="estimator" placeholder="Enter team member name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="estimationStatus" class="form-label">Status</label>
                                            <select class="form-select" id="estimationStatus" required>
                                                <option value="">Select status</option>
                                                <option value="Approved">Approved</option>
                                                <option value="In Review">In Review</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Declined">Declined</option>
                                                <option value="Sent">Sent</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="estimationTags" class="form-label">Tags</label>
                                            <input type="text" class="form-control" id="estimationTags" placeholder="e.g. CRM, Mobile, API">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="createdDate" class="form-label">Created Date</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="createdDate" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="expectedCloseDate" class="form-label">Expected Close</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="expectedCloseDate" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Estimation</button>
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

    <!-- Custom table -->
    <script src="assets/js/pages/custom-table.js"></script>

</body>

</html>