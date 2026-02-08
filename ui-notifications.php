<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Bootstrap Toasts"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Notifications"; include('partials/page-title.php'); ?>
                
                <div class="row">
                    
                    <div class="col-lg-6">

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Basic</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Toasts are as flexible as you need and
                                    have very little required markup. At a minimum, we
                                    require a single element to contain your “toasted” content and
                                    strongly encourage a dismiss button.</p>
                                <div class="p-3">

                                    <!-- Basic -->
                                    <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="toast-header">
                                            <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                            <strong class="me-auto fw-bold text-body">BRAND</strong>
                                            <small>11 mins ago</small>
                                            <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                        <div class="toast-body">
                                            Hello, world! This is a toast message.
                                        </div>
                                    </div>
                                    <!--end toast-->

                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Placement</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Place toasts with custom CSS as you
                                    need them. The top right is often used for
                                    notifications, as is the top middle. If you’re only ever going
                                    to show one toast at a time, put the positioning
                                    styles right on the <code>.toast</code>.</p>
                                <div class="p-3">
                                    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center" style="min-height: 200px;">

                                        <!-- Then put toasts within -->
                                        <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-toggle="toast">
                                            <div class="toast-header">
                                                <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                                <strong class="me-auto fw-bold text-body">BRAND</strong>
                                                <small>11 mins ago</small>
                                                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                            </div>
                                            <div class="toast-body">
                                                Hello, world! This is a toast message.
                                            </div>
                                        </div>
                                        <!--end toast-->
                                    </div>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Placement</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Place toasts with custom CSS as you need them. The top
                                    right is often used for notifications, as is the top middle. If you’re only ever
                                    going to show one toast at a time, put the positioning styles right on the
                                    <code>.toast</code>.
                                </p>

                                <form>
                                    <div class="mb-3">
                                        <label for="selectToastPlacement">Toast placement</label>
                                        <select class="form-select mt-2" id="selectToastPlacement">
                                            <option value="" selected>Select a position...</option>
                                            <option value="top-0 start-0">Top left</option>
                                            <option value="top-0 start-50 translate-middle-x">Top center
                                            </option>
                                            <option value="top-0 end-0">Top right</option>
                                            <option value="top-50 start-0 translate-middle-y">Middle left
                                            </option>
                                            <option value="top-50 start-50 translate-middle">Middle center
                                            </option>
                                            <option value="top-50 end-0 translate-middle-y">Middle right
                                            </option>
                                            <option value="bottom-0 start-0">Bottom left</option>
                                            <option value="bottom-0 start-50 translate-middle-x">Bottom
                                                center</option>
                                            <option value="bottom-0 end-0">Bottom right</option>
                                        </select>
                                    </div>
                                </form>
                                <div aria-live="polite" aria-atomic="true" class="bg-light position-relative bd-example-toasts" style="min-height:294px">
                                    <div class="toast-container position-absolute p-3" id="toastPlacement">
                                        <div class="toast show">
                                            <div class="toast-header">
                                                <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                                <strong class="me-auto fw-bold text-body">BRAND</strong>
                                                <small>11 mins ago</small>
                                            </div>
                                            <div class="toast-body">
                                                Hello, world! This is a toast message.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body -->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Live Toast</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Click the button below to show a toast (positioned with our utilities in the Toop Right corner)
                                    that has been hidden by default.
                                </p>

                                <button type="button" class="btn btn-primary" id="liveToastBtn">Show live toast</button>

                                <div class="toast-container position-fixed top-0 end-0 p-3">
                                    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="toast-header">
                                            <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                            <strong class="me-auto fw-bold text-body">BRAND</strong>
                                            <small>11 mins ago</small>
                                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                        <div class="toast-body">
                                            Hello, world! This is a toast message.
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Translucent</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Toasts are slightly translucent, too,
                                    so they blend over whatever they might appear over.
                                    For browsers that support the backdrop-filter CSS property,
                                    we’ll also attempt to blur the elements under a toast.</p>

                                <div class="p-3 bg-light bg-opacity-50">
                                    <!-- Translucent -->
                                    <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="toast-header">
                                            <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                            <strong class="me-auto fw-bold text-body">BRAND</strong>
                                            <small>11 mins ago</small>
                                            <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                        <div class="toast-body">
                                            Hello, world! This is a toast message.
                                        </div>
                                    </div>
                                    <!--end toast-->
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Stacking</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">When you have multiple toasts, we
                                    default to vertiaclly stacking them in a readable manner.</p>
                                <div class="p-3">
                                    <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
                                        <!-- Position it -->
                                        <div class="toast-container" style="position: absolute; top: 0; right: 0;">

                                            <!-- Then put toasts within -->
                                            <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                                                <div class="toast-header">
                                                    <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                                    <strong class="me-auto fw-bold text-body">BRAND</strong>
                                                    <small class="text-muted">just now</small>
                                                    <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                                </div>
                                                <div class="toast-body">
                                                    See? Just like this.
                                                </div>
                                            </div>
                                            <!--end toast-->

                                            <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
                                                <div class="toast-header">
                                                    <img src="assets/images/logo-sm.png" alt="brand-logo" height="16" class="me-1">
                                                    <strong class="me-auto fw-bold text-body">BRAND</strong>
                                                    <small class="text-muted">2 seconds ago</small>
                                                    <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                                </div>
                                                <div class="toast-body">
                                                    Heads up, toasts will stack automatically
                                                </div>
                                            </div>
                                            <!--end toast-->
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Custom content</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="toast show align-items-center mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            Hello, world! This is a toast message.
                                        </div>
                                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>

                                <div class="toast show align-items-center text-white bg-primary border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            Hello, world! This is a toast message.
                                        </div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>

                                <div class="toast show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-body">
                                        Hello, world! This is a toast message.
                                        <div class="mt-2 pt-2 border-top">
                                            <button type="button" class="btn btn-primary btn-sm">Take
                                                action</button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body -->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                </div><!-- end row-->

            </div> <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

    <script>
        const toastTrigger = document.getElementById('liveToastBtn')
        const toastLiveExample = document.getElementById('liveToast')

        if (toastTrigger) {
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
            toastTrigger.addEventListener('click', () => {
                toastBootstrap.show()
            })
        }
    </script>

</body>

</html>