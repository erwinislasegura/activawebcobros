<?php include('partials/html.php'); ?>

<head>
    <?php $title = "User Permissions"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Users"; $title = "Permissions"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">

                        <div data-table data-table-rows-per-page="8" class="card">
                            <div class="card-header border-light justify-content-between">
                                <div class="d-flex gap-2">
                                    <div class="app-search">
                                        <input data-table-search type="search" class="form-control" placeholder="Search permissions...">
                                        <i data-lucide="search" class="app-search-icon text-muted"></i>
                                    </div>
                                    <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
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


                            <div class="table-responsive">
                                <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th data-table-sort>Name</th>
                                            <th>Assign To</th>
                                            <th data-table-sort>Created Date</th>
                                            <th data-table-sort>Users</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>User Management</td>
                                            <td><span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                            </td>
                                            <td>24 Jun 2025, <span class="text-muted">6:43 am</span></td>
                                            <td>12</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Content Management</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-danger-subtle text-danger badge-label fs-xxs fw-semibold">Developer</span>
                                                <span class="badge bg-info-subtle text-info badge-label fs-xxs fw-semibold">Analyst</span>
                                                <span class="badge bg-secondary-subtle text-secondary badge-label fs-xxs fw-semibold">Support</span>
                                                <span class="badge bg-warning-subtle text-warning badge-label fs-xxs fw-semibold">Trial</span>
                                            </td>
                                            <td>21 Feb 2025, <span class="text-muted">11:05 am</span></td>
                                            <td>5</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Financial Management</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-info-subtle text-info badge-label fs-xxs fw-semibold">Analyst</span>
                                            </td>
                                            <td>24 Jun 2025, <span class="text-muted">5:30 pm</span></td>
                                            <td>8</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reporting</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-info-subtle text-info badge-label fs-xxs fw-semibold">Analyst</span>
                                            </td>
                                            <td>21 Feb 2025, <span class="text-muted">5:20 pm</span></td>
                                            <td>6</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Payroll</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-info-subtle text-info badge-label fs-xxs fw-semibold">Analyst</span>
                                            </td>
                                            <td>20 Jun 2025, <span class="text-muted">6:05 pm</span></td>
                                            <td>4</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Disputes Management</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-danger-subtle text-danger badge-label fs-xxs fw-semibold">Developer</span>
                                                <span class="badge bg-secondary-subtle text-secondary badge-label fs-xxs fw-semibold">Support</span>
                                            </td>
                                            <td>24 Jun 2025, <span class="text-muted">5:20 pm</span></td>
                                            <td>7</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Audit Logs</td>
                                            <td><span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                            </td>
                                            <td>23 Jun 2025, <span class="text-muted">4:00 pm</span></td>
                                            <td>9</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>API Access</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-warning-subtle text-warning badge-label fs-xxs fw-semibold">Trial</span>
                                                <span class="badge bg-info-subtle text-info badge-label fs-xxs fw-semibold">DevOps</span>
                                            </td>
                                            <td>22 Jun 2025, <span class="text-muted">2:35 pm</span></td>
                                            <td>3</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Notification Center</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-info-subtle text-info badge-label fs-xxs fw-semibold">Support</span>
                                            </td>
                                            <td>22 Jun 2025, <span class="text-muted">8:45 am</span></td>
                                            <td>2</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Access Logs</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary badge-label fs-xxs fw-semibold">Administrator</span>
                                                <span class="badge bg-secondary-subtle text-secondary badge-label fs-xxs fw-semibold">Support</span>
                                            </td>
                                            <td>19 Jun 2025, <span class="text-muted">6:10 pm</span></td>
                                            <td>5</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editPermissionModal" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div data-table-pagination-info="permissions"></div>
                                    <div data-table-pagination></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row-->


                <!-- Edit Permission Modal -->
                <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="editPermissionForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <!-- Permission Name -->
                                        <div class="col-md-6">
                                            <label for="editPermissionName" class="form-label">Permission Name</label>
                                            <input type="text" class="form-control" id="editPermissionName" value="User Management" required>
                                        </div>

                                        <!-- Assigned Roles -->
                                        <div class="col-md-6">
                                            <label for="editAssignedRoles" class="form-label">Assigned To</label>
                                            <select class="form-select" id="editAssignedRoles" multiple required>
                                                <option value="Administrator" selected>Administrator</option>
                                                <option value="Developer">Developer</option>
                                                <option value="Analyst">Analyst</option>
                                                <option value="Support">Support</option>
                                                <option value="Trial">Trial</option>
                                            </select>
                                            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple roles</small>
                                        </div>

                                        <!-- Created Date -->
                                        <div class="col-md-6">
                                            <label for="editCreatedDate" class="form-label">Created Date</label>
                                            <input type="text" data-provider="flatpickr" data-date-format="d M, Y" data-enable-time class="form-control" id="editCreatedDate" value="2025-06-24T06:43" required>
                                        </div>

                                        <!-- Number of Users -->
                                        <div class="col-md-6">
                                            <label for="editUserCount" class="form-label">Users</label>
                                            <input type="number" class="form-control" id="editUserCount" value="12" readonly disabled>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
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