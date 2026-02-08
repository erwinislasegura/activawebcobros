<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Opportunities"; include('partials/title-meta.php'); ?>

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
                
                <?php $subtitle = "Apps"; $title = "Opportunities"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div data-table data-table-rows-per-page="8" class="card">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input data-table-search type="search" class="form-control" placeholder="Search opportunity...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
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

                                    <!-- Status Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                            <option value="">Status</option>
                                            <option value="Open">Open</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                        <i data-lucide="check-circle" class="app-search-icon text-muted"></i>
                                    </div>

                                    <!-- Priority Filter -->
                                    <div class="app-search">
                                        <select data-table-filter="priority" class="form-select form-control my-1 my-md-0">
                                            <option value="">Priority</option>
                                            <option value="High">High</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Low">Low</option>
                                        </select>
                                        <i data-lucide="alert-circle" class="app-search-icon text-muted"></i>
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
                                                <th data-table-sort>ID</th>
                                                <th>Opportunity</th>
                                                <th>Contact Person</th>
                                                <th data-table-sort data-column="stage">Stage</th>
                                                <th data-table-sort>Value (USD)</th>
                                                <th data-table-sort>Close Date</th>
                                                <th data-table-sort>Lead Source</th>
                                                <th>Owner</th>
                                                <th data-table-sort data-column="status">Status</th>
                                                <th data-table-sort data-column="priority">Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-nowrap">
                                            <tr>
                                                <td>#OP007501</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm border flex-shrink-0 border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/google.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">
                                                                <a data-sort="product" href="#!" class="link-reset">Website Redesign Deal</a>
                                                            </p>
                                                            <p class="text-muted mb-0 small">By: Google Inc.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-1.jpg" alt="Product" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">
                                                                <a data-sort="product" href="#!" class="link-reset">John Carter</a>
                                                            </p>
                                                            <p class="text-muted mb-0 small">john@acme.com</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Proposal Sent</td>
                                                <td>$12,500</td>
                                                <td>15 Aug, 2025</td>
                                                <td>Referral</td>
                                                <td>Sarah Johnson</td>
                                                <td><span class="badge badge-label badge-soft-success fs-xs">Open</span></td>
                                                <td><span class="badge text-bg-danger fs-xs">High</span></td>
                                            </tr>
                                            <tr>
                                                <td>#OP007502</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/microsoft.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">
                                                                <a data-sort="product" href="#!" class="link-reset">Enterprise SaaS Suite</a>
                                                            </p>
                                                            <p class="text-muted mb-0 small">By: Microsoft Corp.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-2.jpg" alt="Product" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">
                                                                <a data-sort="product" href="#!" class="link-reset">Priya Mehta</a>
                                                            </p>
                                                            <p class="text-muted mb-0 small">priya@globex.com</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Qualification</td>
                                                <td>$50,000</td>
                                                <td>10 Sep, 2025</td>
                                                <td>LinkedIn</td>
                                                <td>Michael Chen</td>
                                                <td><span class="badge badge-label badge-soft-warning fs-xs">In Progress</span></td>
                                                <td><span class="badge text-bg-warning fs-xs">Medium</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009123</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/google.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">
                                                                <a data-sort="product" href="#!" class="link-reset">Cloud Analytics Pro</a>
                                                            </p>
                                                            <p class="text-muted mb-0 small">By: Google LLC</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-5.jpg" alt="Product" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">
                                                                <a data-sort="product" href="#!" class="link-reset">Aarav Patel</a>
                                                            </p>
                                                            <p class="text-muted mb-0 small">aarav@nextgen.io</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Negotiation</td>
                                                <td>$75,000</td>
                                                <td>18 Oct, 2025</td>
                                                <td>Website</td>
                                                <td>Sophia Lee</td>
                                                <td><span class="badge badge-label badge-soft-danger fs-xs">Closed</span></td>
                                                <td><span class="badge text-bg-success fs-xs">Low</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009201</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/amazon.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Retail Insights Pro</a></p>
                                                            <p class="text-muted mb-0 small">By: Amazon Inc.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-3.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Meena Roy</a></p>
                                                            <p class="text-muted mb-0 small">meena@retailhub.io</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Won</td>
                                                <td>$92,000</td>
                                                <td>02 Nov, 2025</td>
                                                <td>Email</td>
                                                <td>Daniel Kim</td>
                                                <td><span class="badge badge-label badge-soft-danger fs-xs">Closed</span></td>
                                                <td><span class="badge text-bg-danger fs-xs">High</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009202</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/apple.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">AI Compute Suite</a></p>
                                                            <p class="text-muted mb-0 small">By: Apple Inc.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-7.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Kabir Shah</a></p>
                                                            <p class="text-muted mb-0 small">kabir@aicore.ai</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Qualification</td>
                                                <td>$64,500</td>
                                                <td>15 Oct, 2025</td>
                                                <td>Referral</td>
                                                <td>Emily Davis</td>
                                                <td><span class="badge badge-label badge-soft-success fs-xs">Open</span></td>
                                                <td><span class="badge text-bg-danger fs-xs">High</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009203</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/dropbox.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Ad Manager Pro</a></p>
                                                            <p class="text-muted mb-0 small">By: Dropbox</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-4.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Sana Iqbal</a></p>
                                                            <p class="text-muted mb-0 small">sana@adstack.com</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Won</td>
                                                <td>$120,000</td>
                                                <td>09 Dec, 2025</td>
                                                <td>LinkedIn</td>
                                                <td>John Carter</td>
                                                <td><span class="badge badge-label badge-soft-danger fs-xs">Closed</span></td>
                                                <td><span class="badge text-bg-success fs-xs">Low</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009204</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/slack.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">TeamCollab Enterprise</a></p>
                                                            <p class="text-muted mb-0 small">By: Slack Technologies</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-6.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Jatin Desai</a></p>
                                                            <p class="text-muted mb-0 small">jatin@collabzone.net</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Proposal Sent</td>
                                                <td>$48,000</td>
                                                <td>30 Sep, 2025</td>
                                                <td>Webinar</td>
                                                <td>Ashley Moore</td>
                                                <td><span class="badge badge-label badge-soft-warning fs-xs">In Progress</span></td>
                                                <td><span class="badge text-bg-success fs-xs">Low</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009205</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/spotify.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Virtual Events Suite</a></p>
                                                            <p class="text-muted mb-0 small">By: Spotify Inc.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-8.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Lina George</a></p>
                                                            <p class="text-muted mb-0 small">lina@meetpro.io</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Lost</td>
                                                <td>$33,000</td>
                                                <td>22 Oct, 2025</td>
                                                <td>Cold Call</td>
                                                <td>Mark Evans</td>
                                                <td><span class="badge badge-label badge-soft-danger fs-xs">Closed</span></td>
                                                <td><span class="badge text-bg-warning fs-xs">Medium</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009312</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/starbucks.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Streamlytics Dashboard</a></p>
                                                            <p class="text-muted mb-0 small">By: Starbucks Inc.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-10.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Ravi Khanna</a></p>
                                                            <p class="text-muted mb-0 small">ravi@streamlytics.co</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Qualification</td>
                                                <td>$58,900</td>
                                                <td>05 Nov, 2025</td>
                                                <td>Trade Show</td>
                                                <td>Jessica Moore</td>
                                                <td><span class="badge badge-label badge-soft-warning fs-xs">In Progress</span></td>
                                                <td><span class="badge text-bg-danger fs-xs">High</span></td>
                                            </tr>

                                            <tr>
                                                <td>#OP009313</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm flex-shrink-0 border border-dashed rounded me-2 justify-content-center d-flex align-items-center">
                                                            <img src="assets/images/logos/airbnb.svg" alt="Product" height="20">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Hospitality CRM Suite</a></p>
                                                            <p class="text-muted mb-0 small">By: Airbnb Inc.</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <img src="assets/images/users/user-9.jpg" alt="User" class="img-fluid rounded-circle">
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium"><a href="#!" class="link-reset">Isabella Wang</a></p>
                                                            <p class="text-muted mb-0 small">isabella@aircrm.com</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>Proposal Sent</td>
                                                <td>$41,200</td>
                                                <td>12 Nov, 2025</td>
                                                <td>Inbound</td>
                                                <td>Thomas Blake</td>
                                                <td><span class="badge badge-label badge-soft-success fs-xs">Open</span></td>
                                                <td><span class="badge text-bg-warning fs-xs">Medium</span></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div> <!-- end table-respo.-->

                            </div> <!-- end card-body-->

                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="Opportunities"></div>
                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col-->
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

    <!-- Custom table -->
    <script src="assets/js/pages/custom-table.js"></script>

</body>

</html>