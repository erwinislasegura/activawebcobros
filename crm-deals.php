<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Deals"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "CRM"; $title = "Deals"; include('partials/page-title.php'); ?>

                <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-2">

                    <!-- Total Deals Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">1,230</h5>
                                    <span>9.85% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-2">Total deals created</p>
                                <div class="progress progress-sm mb-0">
                                    <div class="progress-bar" role="progressbar" style="width: 9.85%" aria-valuenow="9.85" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deals Won Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">860</h5>
                                    <span>5.20% <i class="ti ti-arrow-up text-success"></i></span>
                                </div>
                                <p class="text-muted mb-2">Deals won</p>
                                <div class="progress bg-success bg-opacity-25 progress-sm mb-0">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 5.2%" aria-valuenow="5.2" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deals Lost Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">270</h5>
                                    <span>2.45% <i class="ti ti-arrow-down text-danger"></i></span>
                                </div>
                                <p class="text-muted mb-2">Deals lost</p>
                                <div class="progress bg-danger bg-opacity-25 progress-sm mb-0">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 2.45%" aria-valuenow="2.45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Deal Value Widget -->
                    <div class="col">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">$220,000</h5>
                                    <span>Top value <i class="ti ti-currency-dollar text-success"></i></span>
                                </div>
                                <p class="text-muted mb-2">Highest deal closed</p>
                                <div class="progress bg-warning bg-opacity-25 progress-sm mb-0">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Avg. Deal Close Time Widget -->
                    <div class="col-lg col-md-auto">
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-between align-items-center">
                                    <h5 class="fs-xl mb-0">5.2 <small class="fs-6">days</small></h5>
                                    <span>+1.1% <i class="ti ti-clock text-warning"></i></span>
                                </div>
                                <p class="text-muted mb-2">Avg. close time</p>
                                <div class="progress progress-sm mb-0">
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 1.1%" aria-valuenow="1.1" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
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
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDealModal">
                                        <i class="ti ti-plus me-1"></i> Create Deal
                                    </button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <!-- Stage Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="stage" class="form-select form-control my-1 my-md-0">
                                            <option value="">Stage</option>
                                            <option value="Qualification">Qualification</option>
                                            <option value="Proposal Sent">Proposal Sent</option>
                                            <option value="Negotiation">Negotiation</option>
                                            <option value="Won">Won</option>
                                            <option value="Lost">Lost</option>
                                        </select>
                                        <i data-lucide="shuffle" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Price Range Filter -->
                                    <div class="app-search">
                                        <select data-table-range-filter="amount" class="form-select form-control my-1 my-md-0">
                                            <option value="">Amount Range</option>
                                            <option value="0-1000">$0 - $10000</option>
                                            <option value="10001-25000">$10001 - $25000</option>
                                            <option value="25001-50000">$25001 - $50000</option>
                                            <option value="50000+">$50000+</option>
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
                                                <th data-table-sort>Deal Name</th>
                                                <th>Company</th>
                                                <th data-table-sort data-column="amount">Amount (USD)</th>
                                                <th data-table-sort data-column="stage">Stage</th>
                                                <th data-table-sort>Probability</th>
                                                <th data-table-sort>Closing Date</th>
                                                <th class="text-center" style="width: 1%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Enterprise Software</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/google.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Google Inc</a>
                                                    </div>
                                                </td>
                                                <td>$102,000</td>
                                                <td>Proposal Sent</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-50"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">65%</strong>
                                                    </div>
                                                </td>
                                                <td>15 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row="" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Marketing Automation</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/airbnb.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Airbnb</a>
                                                    </div>
                                                </td>
                                                <td>$85,000</td>
                                                <td>Qualified</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-75"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">55%</strong>
                                                    </div>
                                                </td>
                                                <td>10 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row="" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Cloud Storage Deal</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/dropbox.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Dropbox</a>
                                                    </div>
                                                </td>
                                                <td>$47,000</td>
                                                <td>Negotiation</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">80%</strong>
                                                    </div>
                                                </td>
                                                <td>18 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row="" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>AI Chatbot Integration</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/openai.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">OpenAI</a>
                                                    </div>
                                                </td>
                                                <td>$59,500</td>
                                                <td>Proposal Sent</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-50"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">65%</strong>
                                                    </div>
                                                </td>
                                                <td>22 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row="" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>eCommerce Platform</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/apple.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Apple</a>
                                                    </div>
                                                </td>
                                                <td>$71,200</td>
                                                <td>Qualification</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-50"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">45%</strong>
                                                    </div>
                                                </td>
                                                <td>25 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row="" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Sales CRM Deal</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/shell.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Shell</a>
                                                    </div>
                                                </td>
                                                <td>$95,000</td>
                                                <td>Won</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                        </div>
                                                        <strong class="text-dark">100%</strong>
                                                    </div>
                                                </td>
                                                <td>30 Aug, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row="" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Video Conferencing License</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/figma.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Figma Inc</a>
                                                    </div>
                                                </td>
                                                <td>$38,000</td>
                                                <td>Lost</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-danger opacity-25"></div>
                                                            <div class="prob-bar bg-danger opacity-25"></div>
                                                            <div class="prob-bar bg-danger opacity-25"></div>
                                                            <div class="prob-bar bg-danger opacity-25"></div>
                                                            <div class="prob-bar bg-danger opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">0%</strong>
                                                    </div>
                                                </td>
                                                <td>12 Sep, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Customer Support Suite</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/starbucks.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Starbucks</a>
                                                    </div>
                                                </td>
                                                <td>$52,000</td>
                                                <td>Proposal Sent</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-50"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">70%</strong>
                                                    </div>
                                                </td>
                                                <td>03 Sep, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Project Management SaaS</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/slack.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Slack</a>
                                                    </div>
                                                </td>
                                                <td>$44,000</td>
                                                <td>Negotiation</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">60%</strong>
                                                    </div>
                                                </td>
                                                <td>06 Sep, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Data Visualization Tool</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/asana.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Asana</a>
                                                    </div>
                                                </td>
                                                <td>$67,300</td>
                                                <td>Qualified</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-50"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                            <div class="prob-bar bg-success opacity-25"></div>
                                                        </div>
                                                        <strong class="text-dark">50%</strong>
                                                    </div>
                                                </td>
                                                <td>10 Sep, 2025</td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                        <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                        <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="ps-3">
                                                    <input class="form-check-input form-check-input-light fs-14 product-item-check mt-0" type="checkbox" value="option">
                                                </td>
                                                <td>Team Chat Platform</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded-circle me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/slack.svg" alt="Product" height="20">
                                                        </div>
                                                        <a href="#!" class="link-reset">Slack</a>
                                                    </div>
                                                </td>
                                                <td>$62,000</td>
                                                <td>Won</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex gap-1">
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                            <div class="prob-bar bg-success opacity-100"></div>
                                                        </div>
                                                        <strong class="text-dark">100%</strong>
                                                    </div>
                                                </td>
                                                <td>15 Sep, 2025</td>
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
                                    <div data-table-pagination-info="Deals"></div>
                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div> <!-- end row-->


                <!-- Create Deal Modal -->
                <div class="modal fade" id="createDealModal" tabindex="-1" aria-labelledby="createDealModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="createDealModalLabel">Create New Deal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="createDealForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="dealName" class="form-label">Deal Name</label>
                                            <input type="text" class="form-control" id="dealName" placeholder="Enter deal name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="companyName" class="form-label">Company</label>
                                            <input type="text" class="form-control" id="companyName" placeholder="Enter company name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="amount" class="form-label">Amount (USD)</label>
                                            <input type="number" class="form-control" id="amount" placeholder="e.g. 100000" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="stage" class="form-label">Stage</label>
                                            <select class="form-select" id="stage" required>
                                                <option value="">Select stage</option>
                                                <option value="Qualification">Qualification</option>
                                                <option value="Proposal Sent">Proposal Sent</option>
                                                <option value="Negotiation">Negotiation</option>
                                                <option value="Won">Won</option>
                                                <option value="Lost">Lost</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="probability" class="form-label">Probability (%)</label>
                                            <input type="number" class="form-control" id="probability" min="0" max="100" placeholder="e.g. 75" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="closingDate" class="form-label">Expected Closing Date</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="closingDate" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Deal</button>
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