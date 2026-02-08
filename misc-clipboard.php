<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Clipboard"; include('partials/title-meta.php'); ?>

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

                <?php $title = "Clipboard"; $subtitle = "Miscellaneous"; include('partials/page-title.php'); ?>

                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Examples</h4>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">

                                    <!-- Copy from Element -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy from Element</h5>
                                            <p class="text-muted mb-2">Use <code>data-clipboard-target</code> to copy text from a specific element.</p>
                                            <p class="text-primary fw-bold" id="copytext">Click the button to copy this promotional text.</p>
                                            <button class="btn btn-sm btn-primary" data-clipboard-target="#copytext">
                                                <i class="ti ti-copy me-1"></i> Copy Text
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Cut from Textarea -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Cut from Textarea</h5>
                                            <p class="text-muted mb-2">Use <code>data-clipboard-action</code> with <code>cut</code> to remove and copy content.</p>
                                            <textarea class="form-control" id="cuttext">This content will be cut and removed from this textarea.</textarea>
                                            <button class="btn btn-sm btn-primary mt-3" data-clipboard-action="cut" data-clipboard-target="#cuttext">
                                                <i class="ti ti-cut me-1"></i> Cut Content
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Copy Email Address -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Email Address</h5>
                                            <p class="text-muted mb-2">Click the button to copy this contact email:</p>
                                            <span id="emailToCopy" class="d-block text-primary fw-bold">support@example.com</span>
                                            <button class="btn btn-sm btn-primary mt-2" data-clipboard-target="#emailToCopy">
                                                <i class="ti ti-copy me-1"></i> Copy Email
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Cut Input Value -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Cut Input Value</h5>
                                            <p class="text-muted mb-2">This cuts the value from a single-line input field.</p>
                                            <input type="text" id="cutInput" class="form-control" value="Temporary token: 8GDF-393K-L99Z">
                                            <button class="btn btn-sm btn-danger mt-2" data-clipboard-action="cut" data-clipboard-target="#cutInput">
                                                <i class="ti ti-cut me-1"></i> Cut Token
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Copy Code Snippet -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Code Snippet</h5>
                                            <p class="text-muted mb-2">Copy this snippet by clicking the button:</p>
                                            <pre><code id="codeSnippet">npm install clipboard --save</code></pre>
                                            <button class="btn btn-sm btn-success mt-2" data-clipboard-target="#codeSnippet">
                                                <i class="ti ti-copy me-1"></i> Copy Command
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Copy from Input Group -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy from Input Group</h5>
                                            <p class="text-muted mb-2">Click the copy icon to copy the link here:</p>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="copyLink" value="https://example.com/invite?ref=12345" readonly>
                                                <button class="btn btn-icon btn-secondary" type="button" data-clipboard-target="#copyLink">
                                                    <i class="ti ti-copy fs-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Copy Username -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Username</h5>
                                            <p class="text-muted mb-2">Copy a predefined username from a span element.</p>
                                            <span id="copyUsername" class="d-block text-primary fw-bold">john_doe_92</span>
                                            <button class="btn btn-sm btn-primary mt-2" data-clipboard-target="#copyUsername">
                                                <i class="ti ti-copy me-1"></i> Copy Username
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Copy Discount Code -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy Discount Code</h5>
                                            <p class="text-muted mb-2">Copy a promotional discount code for checkout.</p>
                                            <div class="input-group">
                                                <input type="text" id="discountCode" class="form-control" value="SAVE20NOW" readonly>
                                                <button class="btn btn-icon btn-warning" type="button" data-clipboard-target="#discountCode">
                                                    <i class="ti ti-copy fs-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Copy HTML Template -->
                                    <div class="col-xl-6">
                                        <div class="border border-light rounded p-3 h-100">
                                            <h5 class="mb-1">Copy HTML Template</h5>
                                            <p class="text-muted mb-2">Copy a block of HTML code from a &lt;pre&gt; tag.</p>
                                            <pre><code id="htmlTemplate">&lt;button class="btn btn-primary"&gt;Click Me&lt;/button&gt;</code></pre>
                                            <button class="btn btn-sm btn-info mt-2" data-clipboard-target="#htmlTemplate">
                                                <i class="ti ti-copy me-1"></i> Copy HTML
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div> <!-- end card-body-->
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

    <!-- Clipboard js -->
    <script src="assets/plugins/clipboard/clipboard.min.js"></script>

    <!-- Clipboard Page js -->
    <script src="assets/js/pages/misc-clipboard.js"></script>

</body>

</html>