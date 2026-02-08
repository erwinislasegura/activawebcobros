<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Customers"; include('partials/title-meta.php'); ?>

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
                
                <?php $subtitle = "CRM"; $title = "Customers"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div data-table data-table-rows-per-page="8" class="card">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input data-table-search type="text" class="form-control" placeholder="Search clients...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>

                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                        <i class="ti ti-plus me-1"></i> New Customer
                                    </button>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="me-2 fw-semibold">Filter By:</span>

                                    <!-- Country Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="country" class="form-select form-control my-1 my-md-0">
                                            <option value="All">Country</option>
                                            <option value="US">United States</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="BR">Brazil</option>
                                            <option value="DE">Germany</option>
                                            <option value="JP">Japan</option>
                                            <option value="FR">France</option>
                                            <option value="IN">India</option>
                                            <option value="EG">Egypt</option>
                                            <option value="CA">Canada</option>
                                        </select>
                                        <i data-lucide="globe" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Account Status</option>
                                            <option value="Active">Active</option>
                                            <option value="Verification Pending">Verification Pending</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Blocked">Blocked</option>
                                        </select>
                                        <i data-lucide="shuffle" class="app-search-icon text-muted"></i>
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
                                <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th scope="col" style="width: 1%;">
                                                <input data-table-select-all class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" id="checkAll" value="option">
                                            </th>
                                            <th data-table-sort="name">Customer Name</th>
                                            <th data-table-sort>Phone</th>
                                            <th data-table-sort data-column="country">Country</th>
                                            <th data-table-sort>Joined</th>
                                            <th data-table-sort>Type</th>
                                            <th data-table-sort>Company</th>
                                            <th data-table-sort data-column="status">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-2.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Michael Thompson</a></h5>
                                                        <p class="text-muted fs-xs mb-0">michael@breezetech.com</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+44 7911 123456</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/gb.svg" class="rounded-circle me-1" height="12"> UK</span></td>
                                            <td>Jan 15, 2024</td>
                                            <td>Lead</td>
                                            <td>BreezeTech Ltd.</td>
                                            <td><span class="badge bg-warning-subtle text-warning badge-label">Verification Pending</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-3.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Sara Mitchell</a></h5>
                                                        <p class="text-muted fs-xs mb-0">sara@novasoft.io</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+1 (646) 555-7788</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/us.svg" class="rounded-circle me-1" height="12"> US</span></td>
                                            <td>Feb 1, 2024</td>
                                            <td>Prospect</td>
                                            <td>NovaSoft</td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-4.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Ravi Deshmukh</a></h5>
                                                        <p class="text-muted fs-xs mb-0">ravi@infraview.in</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+91 98765 43210</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/in.svg" class="rounded-circle me-1" height="12"> IN</span></td>
                                            <td>Mar 10, 2024</td>
                                            <td>Client</td>
                                            <td>InfraView Pvt. Ltd.</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary badge-label">Inactive</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-5.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Laura Kim</a></h5>
                                                        <p class="text-muted fs-xs mb-0">laura.kim@pixelhive.co</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+82 10-1234-5678</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/kr.svg" class="rounded-circle me-1" height="12"> KR</span></td>
                                            <td>Dec 20, 2023</td>
                                            <td>Client</td>
                                            <td>PixelHive Co.</td>
                                            <td><span class="badge bg-danger-subtle text-danger badge-label">Blocked</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-6.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Jean Dupont</a></h5>
                                                        <p class="text-muted fs-xs mb-0">jean@parispro.fr</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+33 6 12 34 56 78</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/fr.svg" class="rounded-circle me-1" height="12"> FR</span></td>
                                            <td>Apr 5, 2024</td>
                                            <td>Prospect</td>
                                            <td>ParisPro SARL</td>
                                            <td><span class="badge bg-warning-subtle text-warning badge-label">Verification Pending</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-7.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Amanda Rivera</a></h5>
                                                        <p class="text-muted fs-xs mb-0">amanda@brightlabs.io</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+1 (213) 555-0192</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/us.svg" class="rounded-circle me-1" height="12"> US</span></td>
                                            <td>Mar 25, 2024</td>
                                            <td>Client</td>
                                            <td>BrightLabs Inc.</td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-8.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Carlos Mendes</a></h5>
                                                        <p class="text-muted fs-xs mb-0">carlos@globalreach.br</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+55 11 91234-5678</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/br.svg" class="rounded-circle me-1" height="12"> BR</span></td>
                                            <td>Feb 18, 2024</td>
                                            <td>Prospect</td>
                                            <td>GlobalReach Ltd.</td>
                                            <td><span class="badge bg-warning-subtle text-warning badge-label">Verification Pending</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-9.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Lena Hoffmann</a></h5>
                                                        <p class="text-muted fs-xs mb-0">lena@webnord.de</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+49 176 12345678</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/de.svg" class="rounded-circle me-1" height="12"> DE</span></td>
                                            <td>Apr 3, 2024</td>
                                            <td>Lead</td>
                                            <td>WebNord GmbH</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary badge-label">Inactive</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-10.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Akira Sato</a></h5>
                                                        <p class="text-muted fs-xs mb-0">akira@nippontech.jp</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+81 90-1234-5678</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/jp.svg" class="rounded-circle me-1" height="12"> JP</span></td>
                                            <td>Feb 12, 2024</td>
                                            <td>Client</td>
                                            <td>NipponTech</td>
                                            <td><span class="badge bg-danger-subtle text-danger badge-label">Blocked</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-5.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Sophie Dubois</a></h5>
                                                        <p class="text-muted fs-xs mb-0">sophie@créatis.fr</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+33 7 89 01 23 45</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/fr.svg" class="rounded-circle me-1" height="12"> FR</span></td>
                                            <td>Feb 9, 2024</td>
                                            <td>Client</td>
                                            <td>Créatis SARL</td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-1.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">Omar Farouk</a></h5>
                                                        <p class="text-muted fs-xs mb-0">omar@cairoconnect.eg</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+20 100 123 4567</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/eg.svg" class="rounded-circle me-1" height="12"> EG</span></td>
                                            <td>Apr 12, 2024</td>
                                            <td>Prospect</td>
                                            <td>CairoConnect</td>
                                            <td><span class="badge bg-warning-subtle text-warning badge-label">Verification Pending</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-sm"><img src="assets/images/users/user-2.jpg" class="img-fluid rounded-circle" alt=""></div>
                                                    <div>
                                                        <h5 class="mb-0 lh-base fs-base"><a href="users-profile.php" class="link-reset">John Smith</a></h5>
                                                        <p class="text-muted fs-xs mb-0">john@futuredevs.com</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>+1 (416) 555-3210</td>
                                            <td><span class="badge p-1 text-bg-light fs-sm"><img src="assets/images/flags/ca.svg" class="rounded-circle me-1" height="12"> CA</span></td>
                                            <td>Feb 5, 2024</td>
                                            <td>Lead</td>
                                            <td>FutureDevs</td>
                                            <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-eye fs-lg"></i></a>
                                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-edit fs-lg"></i></a>
                                                    <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm rounded"><i class="ti ti-trash fs-lg"></i></a>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>

                                </table><!-- end table -->
                            </div>
                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="clients"></div>

                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->

                <!-- Add Customer Modal -->
                <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="addCustomerForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="customerName" class="form-label">Customer Name</label>
                                            <input type="text" class="form-control" id="customerName" placeholder="Enter full name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" placeholder="Enter email" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" placeholder="e.g. +1 234 567 8900" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="company" class="form-label">Company</label>
                                            <input type="text" class="form-control" id="company" placeholder="Company name">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="country" class="form-label">Country</label>
                                            <select class="form-select" id="country" required>
                                                <option value="">Select country</option>
                                                <option value="US">United States</option>
                                                <option value="UK">United Kingdom</option>
                                                <option value="IN">India</option>
                                                <option value="CA">Canada</option>
                                                <option value="DE">Germany</option>
                                                <option value="FR">France</option>
                                                <option value="JP">Japan</option>
                                                <option value="BR">Brazil</option>
                                                <option value="EG">Egypt</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="customerType" class="form-label">Customer Type</label>
                                            <select class="form-select" id="customerType" required>
                                                <option value="">Select type</option>
                                                <option value="Lead">Lead</option>
                                                <option value="Prospect">Prospect</option>
                                                <option value="Client">Client</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="Accostatus" class="form-label">Account Status</label>
                                            <select class="form-select" id="Accostatus" required>
                                                <option value="">Select status</option>
                                                <option value="Active">Active</option>
                                                <option value="Verification Pending">Verification Pending</option>
                                                <option value="Inactive">Inactive</option>
                                                <option value="Blocked">Blocked</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="joinedDate" class="form-label">Joined Date</label>
                                            <input type="date" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" id="joinedDate" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add Customer</button>
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