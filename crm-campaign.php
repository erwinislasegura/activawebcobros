<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Campaign"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Campaign"; include('partials/page-title.php'); ?>

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-2">

                    <!-- Total Campaigns Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">11</h5>
                                    <span>+22.2% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Total campaigns launched</p>
                            </div>
                        </div>
                    </div>

                    <!-- Successful Campaigns Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">4</h5>
                                    <span>+36.3% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Successful campaigns</p>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Campaigns Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">2</h5>
                                    <span>-18.1% <i class="ti ti-arrow-down text-danger"></i></span>
                                </div>
                                <p class="text-muted mb-0">Failed campaigns</p>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Campaign Budget Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">$85,000</h5>
                                    <span>Top spend <i class="ti ti-currency-dollar text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Highest campaign budget</p>
                            </div>
                        </div>
                    </div>

                    <!-- Avg. Campaign Duration Widget -->
                    <div class="col-lg col-md-auto">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">5.7 <small class="fs-6">days</small></h5>
                                    <span>+1.4% <i class="ti ti-clock text-warning"></i></span>
                                </div>
                                <p class="text-muted mb-0">Avg. campaign duration</p>
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
                                        <input data-table-search type="search" class="form-control" placeholder="Search campaign...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                                        <i class="ti ti-plus me-1"></i> Create Campaign
                                    </button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <!-- Status Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Status</option>
                                            <option value="Success">Success</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Scheduled">Scheduled</option>
                                            <option value="Failed">Failed</option>
                                            <option value="Ongoing">Ongoing</option>
                                        </select>
                                        <i data-lucide="shuffle" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Budget Range Filter -->
                                    <div class="app-search">
                                        <select data-table-range-filter="budget" class="form-select form-control my-1 my-md-0">
                                            <option value="">Budget Range</option>
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
                                                    <input data-table-select-all class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" id="select-all-products" value="option">
                                                </th>
                                                <th data-table-sort>Campaign Name</th>
                                                <th>Creator</th>
                                                <th data-table-sort data-column="budget">Budget</th>
                                                <th data-table-sort>Goals</th>
                                                <th data-table-sort data-column="status">Status</th>
                                                <th style="width: 15%;">Tags</th>
                                                <th data-table-sort>Date Created</th>
                                                <th class="text-center" style="width: 1%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <!-- Row 1 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Q4 Lead Nurture Campaign</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-5.jpg" alt="Jason Miller" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Jason Miller</a>
                                                    </div>
                                                </td>
                                                <td>$12,500</td>
                                                <td>$80,000</td>
                                                <td><span class="badge bg-warning-subtle text-warning">In Progress</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Email</span>
                                                    <span class="badge badge-label text-bg-light">Retargeting</span>
                                                    <span class="badge badge-label text-bg-light">Automation</span>
                                                </td>
                                                <td>21 Aug, 2025 <small class="text-muted">2:45 PM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Holiday Flash Sale</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-7.jpg" alt="Sandra Walton" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Sandra Walton</a>
                                                    </div>
                                                </td>
                                                <td>$6,000</td>
                                                <td>$30,000</td>
                                                <td><span class="badge bg-success-subtle text-success">Success</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Seasonal</span>
                                                    <span class="badge badge-label text-bg-light">SMS</span>
                                                </td>
                                                <td>05 Dec, 2024 <small class="text-muted">11:00 AM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Product Launch Webinar</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-4.jpg" alt="Derek Kim" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Derek Kim</a>
                                                    </div>
                                                </td>
                                                <td>$10,000</td>
                                                <td>$65,000</td>
                                                <td><span class="badge bg-info-subtle text-info">Scheduled</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Webinar</span>
                                                    <span class="badge badge-label text-bg-light">Leads</span>
                                                </td>
                                                <td>01 Sep, 2025 <small class="text-muted">9:15 AM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Back-to-School Promo</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-8.jpg" alt="Ava Nguyen" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Ava Nguyen</a>
                                                    </div>
                                                </td>
                                                <td>$4,800</td>
                                                <td>$25,000</td>
                                                <td><span class="badge bg-danger-subtle text-danger">Failed</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Email</span>
                                                    <span class="badge badge-label text-bg-light">Discount</span>
                                                </td>
                                                <td>15 Jul, 2025 <small class="text-muted">4:30 PM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Spring Email Blast</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-3.jpg" alt="Ryan Patel" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Ryan Patel</a>
                                                    </div>
                                                </td>
                                                <td>$7,200</td>
                                                <td>$40,000</td>
                                                <td><span class="badge bg-success-subtle text-success">Success</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Newsletter</span>
                                                    <span class="badge badge-label text-bg-light">Organic</span>
                                                </td>
                                                <td>18 Mar, 2025 <small class="text-muted">1:00 PM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Customer Loyalty Program</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-2.jpg" alt="Lily Chen" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Lily Chen</a>
                                                    </div>
                                                </td>
                                                <td>$9,500</td>
                                                <td>$70,000</td>
                                                <td><span class="badge bg-primary-subtle text-primary">Ongoing</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Rewards</span>
                                                    <span class="badge badge-label text-bg-light">Customer Retention</span>
                                                </td>
                                                <td>28 Aug, 2025 <small class="text-muted">10:00 AM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Referral Boost Campaign</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-9.jpg" alt="Noah Brooks" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Noah Brooks</a>
                                                    </div>
                                                </td>
                                                <td>$5,000</td>
                                                <td>$20,000</td>
                                                <td><span class="badge bg-success-subtle text-success">Success</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Referral</span>
                                                    <span class="badge badge-label text-bg-light">Growth</span>
                                                </td>
                                                <td>04 Jun, 2025 <small class="text-muted">9:00 AM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>App Download Drive</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-10.jpg" alt="Sophia Lee" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Sophia Lee</a>
                                                    </div>
                                                </td>
                                                <td>$3,200</td>
                                                <td>$15,000</td>
                                                <td><span class="badge bg-warning-subtle text-warning">In Progress</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Mobile</span>
                                                    <span class="badge badge-label text-bg-light">Ads</span>
                                                </td>
                                                <td>12 Aug, 2025 <small class="text-muted">3:15 PM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Abandoned Cart Recovery</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-1.jpg" alt="Lucas Green" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Lucas Green</a>
                                                    </div>
                                                </td>
                                                <td>$2,000</td>
                                                <td>$12,000</td>
                                                <td><span class="badge bg-danger-subtle text-danger">Failed</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Email</span>
                                                    <span class="badge badge-label text-bg-light">Recovery</span>
                                                </td>
                                                <td>29 Jul, 2025 <small class="text-muted">5:50 PM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Local Awareness Campaign</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-2.jpg" alt="Isabella Ray" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Isabella Ray</a>
                                                    </div>
                                                </td>
                                                <td>$4,700</td>
                                                <td>$28,000</td>
                                                <td><span class="badge bg-info-subtle text-info">Scheduled</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">Geo Targeting</span>
                                                    <span class="badge badge-label text-bg-light">Brand</span>
                                                </td>
                                                <td>02 Sep, 2025 <small class="text-muted">8:10 AM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Video Ad Campaign</td>
                                                <td>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <img src="assets/images/users/user-3.jpg" alt="Leo White" class="avatar-xs rounded-circle">
                                                        <a href="#!" class="link-reset">Leo White</a>
                                                    </div>
                                                </td>
                                                <td>$9,900</td>
                                                <td>$55,000</td>
                                                <td><span class="badge bg-primary-subtle text-primary">Ongoing</span></td>
                                                <td>
                                                    <span class="badge badge-label text-bg-light">YouTube</span>
                                                    <span class="badge badge-label text-bg-light">Video</span>
                                                </td>
                                                <td>14 Aug, 2025 <small class="text-muted">12:00 PM</small></td>
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
                                    <div data-table-pagination-info="Campaigns"></div>
                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row-->


                <!-- Create Campaign Modal -->
                <div class="modal fade" id="createCampaignModal" tabindex="-1" aria-labelledby="createCampaignModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="createCampaignModalLabel">Create New Campaign</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="createCampaignForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="campaignName" class="form-label">Campaign Name</label>
                                            <input type="text" class="form-control" id="campaignName" placeholder="Enter campaign name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="CreatorName" class="form-label">Creator</label>
                                            <input type="text" class="form-control" id="CreatorName" placeholder="Enter campaign creator" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="budget" class="form-label">Budget (USD)</label>
                                            <input type="number" class="form-control" id="budget" placeholder="e.g. 7500" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="goal" class="form-label">Goal (USD)</label>
                                            <input type="number" class="form-control" id="goal" placeholder="e.g. 50000" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="Campstatus" class="form-label">Status</label>
                                            <select class="form-select" id="Campstatus" required>
                                                <option value="">Select status</option>
                                                <option value="Success">Success</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Scheduled">Scheduled</option>
                                                <option value="Failed">Failed</option>
                                                <option value="Ongoing">Ongoing</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="tags" class="form-label">Tags</label>
                                            <input type="text" class="form-control" id="tags" placeholder="e.g. Email, Webinar, Retargeting" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="launchDate" class="form-label">Launch Date</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="launchDate" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="launchTime" class="form-label">Launch Time</label>
                                            <input type="text" class="form-control" data-provider="timepickr" data-time-basic="true" id="launchTime" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Campaign</button>
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