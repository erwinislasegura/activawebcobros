<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Create New Ticket"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Support"; $title = "New Ticket"; include('partials/page-title.php'); ?>

                <div class="row justify-content-center">
                    <div class="col-xxl-10">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Create New Support Ticket</h5>
                            </div>

                            <form action="#" method="post">
                                <div class="card-body">

                                    <!-- Requester Name -->
                                    <div class="mb-3">
                                        <label for="requester" class="form-label">Requester Name</label>
                                        <input type="text" class="form-control" id="requester" placeholder="Enter your full name">
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" placeholder="you@example.com">
                                    </div>

                                    <!-- Subject -->
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Ticket Subject</label>
                                        <input type="text" class="form-control" id="subject" placeholder="Brief summary of the issue">
                                    </div>

                                    <!-- Priority & Status -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="priority" class="form-label">Priority</label>
                                            <select id="priority" class="form-select">
                                                <option selected disabled>Choose...</option>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Urgent">Urgent</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tickstatus" class="form-label">Status</label>
                                            <select id="tickstatus" class="form-select">
                                                <option selected disabled>Choose...</option>
                                                <option value="Pending">Pending</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Resolved">Resolved</option>
                                                <option value="Closed">Closed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Tags -->
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags (comma-separated)</label>
                                        <input type="text" class="form-control" id="tags" placeholder="e.g. login, error, payment">
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea id="description" class="form-control" rows="5" placeholder="Describe the issue in detail..."></textarea>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Submit Ticket</button>
                                        <button type="reset" class="btn btn-outline-secondary"><i class="ti ti-x me-1"></i>Reset</button>
                                    </div>

                                </div>
                            </form>
                        </div>
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

</body>

</html>