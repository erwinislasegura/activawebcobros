<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Proposals"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Proposals"; include('partials/page-title.php'); ?>

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-2">

                    <!-- Total Proposals Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">38</h5>
                                    <span>+12.4% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Total proposals submitted</p>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Proposals Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">19</h5>
                                    <span>+9.8% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Approved proposals</p>
                            </div>
                        </div>
                    </div>

                    <!-- Declined Proposals Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">7</h5>
                                    <span>-4.2% <i class="ti ti-arrow-down text-danger"></i></span>
                                </div>
                                <p class="text-muted mb-0">Declined proposals</p>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Proposal Value Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">$112,000</h5>
                                    <span>Top value <i class="ti ti-currency-dollar text-success"></i></span>
                                </div>
                                <p class="text-muted mb-0">Highest proposal value</p>
                            </div>
                        </div>
                    </div>

                    <!-- Avg. Proposal Review Time Widget -->
                    <div class="col-lg col-md-auto">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">3.2 <small class="fs-6">days</small></h5>
                                    <span>+0.8% <i class="ti ti-clock text-warning"></i></span>
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
                                        <input data-table-search type="search" class="form-control" placeholder="Search proposal...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProposalModal">
                                        <i class="ti ti-plus me-1"></i> New Proposal
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
                                                    <input data-table-select-all class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" id="select-all-products" value="option">
                                                </th>
                                                <th data-table-sort>Proposal ID</th>
                                                <th>Subject</th>
                                                <th>Send To</th>
                                                <th data-table-sort data-column="value">Value</th>
                                                <th data-table-sort>Created</th>
                                                <th data-table-sort>Open Till</th>
                                                <th data-table-sort data-column="status">Status</th>
                                                <th class="text-center" style="width: 1%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <!-- Row 1 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008101</a></td>
                                                <td>SEO Optimization Campaign</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/google.svg" alt="Google" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Google Inc.</a>
                                                    </div>
                                                </td>
                                                <td>$18,900</td>
                                                <td>12 Jul, 2025 <small class="text-muted">10:00 AM</small></td>
                                                <td>30 Jul, 2025 <small class="text-muted">11:59 PM</small></td>
                                                <td><span class="badge badge-label bg-warning-subtle text-warning fs-xs">Pending</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008102</a></td>
                                                <td>New Mobile App Development</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/apple.svg" alt="Apple" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Apple Inc.</a>
                                                    </div>
                                                </td>
                                                <td>$35,000</td>
                                                <td>18 Jul, 2025 <small class="text-muted">3:15 PM</small></td>
                                                <td>15 Aug, 2025 <small class="text-muted">12:00 PM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008103</a></td>
                                                <td>Marketing Retargeting Plan</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/meta.svg" alt="Meta" height="12">
                                                        </div>
                                                        <a href="#!" class="link-reset">Meta Platforms</a>
                                                    </div>
                                                </td>
                                                <td>$22,750</td>
                                                <td>22 Jul, 2025 <small class="text-muted">9:30 AM</small></td>
                                                <td>10 Aug, 2025 <small class="text-muted">5:00 PM</small></td>
                                                <td><span class="badge badge-label bg-danger-subtle text-danger fs-xs">Declined</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008104</a></td>
                                                <td>UI/UX Redesign for SaaS App</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/figma.svg" alt="Figma" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Figma Inc.</a>
                                                    </div>
                                                </td>
                                                <td>$9,800</td>
                                                <td>24 Jul, 2025 <small class="text-muted">11:20 AM</small></td>
                                                <td>07 Aug, 2025 <small class="text-muted">6:00 PM</small></td>
                                                <td><span class="badge badge-label bg-primary-subtle text-primary fs-xs">In Review</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008105</a></td>
                                                <td>Cloud Infrastructure Setup</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/airbnb.svg" alt="Azure" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">AirBNB</a>
                                                    </div>
                                                </td>
                                                <td>$45,000</td>
                                                <td>26 Jul, 2025 <small class="text-muted">9:10 AM</small></td>
                                                <td>09 Aug, 2025 <small class="text-muted">5:00 PM</small></td>
                                                <td><span class="badge badge-label bg-success-subtle text-success fs-xs">Approved</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008106</a></td>
                                                <td>Digital Marketing Strategy</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/asana.svg" alt="Hubspot" height="16">
                                                        </div>
                                                        <a href="#!" class="link-reset">Asana</a>
                                                    </div>
                                                </td>
                                                <td>$27,600</td>
                                                <td>25 Jul, 2025 <small class="text-muted">2:30 PM</small></td>
                                                <td>08 Aug, 2025 <small class="text-muted">3:00 PM</small></td>
                                                <td><span class="badge badge-label bg-warning-subtle text-warning fs-xs">Pending</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008107</a></td>
                                                <td>Backend API Integration</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/microsoft.svg" alt="GitHub" height="16" class="rounded-circle">
                                                        </div>
                                                        <a href="#!" class="link-reset">Microsoft Enterprise</a>
                                                    </div>
                                                </td>
                                                <td>$14,200</td>
                                                <td>23 Jul, 2025 <small class="text-muted">1:00 PM</small></td>
                                                <td>01 Aug, 2025 <small class="text-muted">2:00 PM</small></td>
                                                <td><span class="badge badge-label bg-info-subtle text-info fs-xs">Sent</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008108</a></td>
                                                <td>Performance Audit Report</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/dropbox.svg" alt="Stripe" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Dropbox</a>
                                                    </div>
                                                </td>
                                                <td>$6,500</td>
                                                <td>20 Jul, 2025 <small class="text-muted">4:45 PM</small></td>
                                                <td>28 Jul, 2025 <small class="text-muted">11:00 AM</small></td>
                                                <td><span class="badge badge-label bg-danger-subtle text-danger fs-xs">Declined</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008109</a></td>
                                                <td>Data Migration Strategy</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/dropbox.svg" alt="Dropbox" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Dropbox Inc.</a>
                                                    </div>
                                                </td>
                                                <td>$19,400</td>
                                                <td>19 Jul, 2025 <small class="text-muted">3:20 PM</small></td>
                                                <td>03 Aug, 2025 <small class="text-muted">10:00 AM</small></td>
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
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008110</a></td>
                                                <td>Customer Portal UI Design</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/openai.svg" alt="Salesforce" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">ChatGPT</a>
                                                    </div>
                                                </td>
                                                <td>$31,000</td>
                                                <td>18 Jul, 2025 <small class="text-muted">12:15 PM</small></td>
                                                <td>02 Aug, 2025 <small class="text-muted">6:00 PM</small></td>
                                                <td><span class="badge badge-label bg-success-subtle text-success fs-xs">Approved</span></td>
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
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008111</a></td>
                                                <td>Mobile Analytics Dashboard</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/h&m.svg" alt="Mixpanel" height="14">
                                                        </div>
                                                        <a href="#!" class="link-reset">Mixpanel</a>
                                                    </div>
                                                </td>
                                                <td>$15,900</td>
                                                <td>16 Jul, 2025 <small class="text-muted">4:00 PM</small></td>
                                                <td>30 Jul, 2025 <small class="text-muted">5:00 PM</small></td>
                                                <td><span class="badge badge-label bg-info-subtle text-info fs-xs">Sent</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 12 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008112</a></td>
                                                <td>AI-Powered Lead Generator</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/openai.svg" alt="OpenAI" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">OpenAI</a>
                                                    </div>
                                                </td>
                                                <td>$52,300</td>
                                                <td>21 Jul, 2025 <small class="text-muted">1:45 PM</small></td>
                                                <td>04 Aug, 2025 <small class="text-muted">11:00 AM</small></td>
                                                <td><span class="badge badge-label bg-primary-subtle text-primary fs-xs">In Review</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Row 13 -->
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td><a href="#!" class="fw-semibold link-reset">#PS008113</a></td>
                                                <td>Enterprise Security Audit</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/shell.svg" alt="Cloudflare" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Cloudflare</a>
                                                    </div>
                                                </td>
                                                <td>$40,750</td>
                                                <td>22 Jul, 2025 <small class="text-muted">9:15 AM</small></td>
                                                <td>06 Aug, 2025 <small class="text-muted">4:00 PM</small></td>
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


                <!-- Create Campaign Modal -->
                <div class="modal fade" id="createProposalModal" tabindex="-1" aria-labelledby="createProposalModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="createProposalModalLabel">Create New Proposal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="createProposalForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="proposalID" class="form-label">Proposal ID</label>
                                            <input type="text" class="form-control" id="proposalID" placeholder="Enter proposal ID (e.g. #PS008120)" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="proposalSubject" class="form-label">Subject</label>
                                            <input type="text" class="form-control" id="proposalSubject" placeholder="Enter proposal subject" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="clientName" class="form-label">Send To (Client)</label>
                                            <input type="text" class="form-control" id="clientName" placeholder="Enter client name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="proposalValue" class="form-label">Value (USD)</label>
                                            <input type="number" class="form-control" id="proposalValue" placeholder="e.g. 15000" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="proposalStatus" class="form-label">Status</label>
                                            <select class="form-select" id="proposalStatus" required>
                                                <option value="">Select status</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Declined">Declined</option>
                                                <option value="Sent">Sent</option>
                                                <option value="In Review">In Review</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="proposalTags" class="form-label">Tags</label>
                                            <input type="text" class="form-control" id="proposalTags" placeholder="e.g. Marketing, Development, Design">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="createdDate" class="form-label">Created Date</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="createdDate" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="openTill" class="form-label">Open Till</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="openTill" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Proposal</button>
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