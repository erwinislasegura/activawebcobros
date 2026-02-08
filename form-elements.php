<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Form Elements"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "Forms"; $title = "Basic Elements"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Input Textfield Type</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <!-- Simple Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="simpleinput" class="col-form-label">Simple Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="simpleinput" class="form-control">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Floating Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label" for="floatingInput">Floating Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="floatingInput" placeholder="name">
                                                    <label>Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Validation Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="validInput" class="col-form-label">Valid Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="validInput" class="form-control is-valid">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Placeholder -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-rounded" class="col-form-label">Rounded Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-rounded" class="form-control rounded-pill" placeholder="Rounded Input">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Text Area -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-textarea" class="col-form-label">Text area</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <textarea class="form-control" id="example-textarea" rows="5"></textarea>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Disabled -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-disable" class="col-form-label">Disabled</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" id="example-disable" disabled value="Disabled value">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Helping Text -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-helping" class="col-form-label">Helping text</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-helping" class="form-control" placeholder="Helping text">
                                                <small class="form-text text-muted">
                                                    A block of help text that breaks onto a new line and may extend beyond one line.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Default select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Select with Icon</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="app-search">
                                                    <select class="form-select form-control" id="discount">
                                                        <option selected>Choose Discount</option>
                                                        <option value="No Discount">No Discount</option>
                                                        <option value="Flat Discount">Flat Discount</option>
                                                        <option value="Percentage Discount">Percentage Discount</option>
                                                    </select>
                                                    <i data-lucide="percent" class="app-search-icon text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                    <div class="col-lg-6">
                                        <!-- with Label Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Label Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div>
                                                    <label for="labelInputInput1" class="form-label">Label Input</label>
                                                    <input type="email" class="form-control" id="labelInputInput1" placeholder="name@example.com">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Search Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="SearchInput" class="col-form-label">Search Style</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="app-search">
                                                    <input type="search" class="form-control" id="SearchInput" placeholder="Search for something...">
                                                    <i data-lucide="search" class="app-search-icon text-muted"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Invalidation Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="inValidationInput" class="col-form-label">Invalid Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="inValidationInput" class="form-control is-invalid">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Placeholder -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-placeholder" class="col-form-label">Placeholder</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-placeholder" class="form-control" placeholder="placeholder">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Readonly -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-readonly" class="col-form-label">Readonly</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-readonly" class="form-control" readonly value="Readonly value">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Static Control -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-static" class="col-form-label">Static control</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" readonly class="form-control-plaintext" id="example-static" value="email@example.com">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Default select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Default Select</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-select">
                                                    <option selected>Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Multiple Select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-multiselect" class="col-form-label">Multiple Select</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select id="example-multiselect" multiple class="form-control">
                                                    <option>1</option>
                                                    <option>2</option>
                                                    <option>3</option>
                                                    <option>4</option>
                                                    <option>5</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                            </div> <!-- end card-body -->
                        </div> <!-- end card -->

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">Input Types</h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <!-- Email Input -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-email" class="col-form-label">Email</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="email" id="example-email" name="example-email" class="form-control" placeholder="Email">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Show/Hide Password -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="password" class="col-form-label">Show/Hide Password</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group" data-password>
                                                    <input type="password" id="password" class="form-control form-password" placeholder="Enter your password">
                                                    <button class="btn btn-light btn-icon" type="button">
                                                        <i class="ti ti-eye password-eye-on"></i>
                                                        <i class="ti ti-eye-closed d-none password-eye-off"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Time -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-time" class="col-form-label">Time</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" id="example-time" type="time" name="time">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Number -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-number" class="col-form-label">Number</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" id="example-number" type="number" name="number">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Range -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-range" class="col-form-label">Range</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-range" id="example-range" type="range" name="range" min="0" max="100">
                                            </div>
                                        </div>

                                    </div> <!-- end col -->

                                    <div class="col-lg-6">

                                        <!-- Password -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-password" class="col-form-label">Password</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="password" id="example-password" class="form-control" value="password">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Month -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-month" class="col-form-label">Month</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" id="example-month" type="month" name="month">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Week -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-week" class="col-form-label">Week</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" id="example-week" type="week" name="week">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Color -->
                                        <div class="row g-lg-4 g-2 mb-3">
                                            <div class="col-lg-4">
                                                <label for="example-color" class="col-form-label">Color</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" id="example-color" type="color" name="color" value="#3a6c8f">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                    </div> <!-- end col -->
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">Input Group </h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <!-- Basic Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Username</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1">@</span>
                                                    <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Currency Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Amount</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                                                    <span class="input-group-text">.00</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Textarea with Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Textarea</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">With textarea</span>
                                                    <textarea class="form-control" aria-label="With textarea" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Flex-nowrap Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Wrapping</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group flex-nowrap">
                                                    <span class="input-group-text" id="addon-wrapping">@</span>
                                                    <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="addon-wrapping">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Input group with text input and button -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Input + Button</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username">
                                                    <button class="btn btn-dark" type="button">Button</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Multiple Files  -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="formFileMultiple01" class="col-form-label">Multiple Files</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" type="file" id="formFileMultiple01" multiple>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                    <div class="col-lg-6">

                                        <!-- Email-like Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Recipient</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">@example.com</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Multi-field Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Email Login</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Username">
                                                    <span class="input-group-text">@</span>
                                                    <input type="text" class="form-control" placeholder="Server">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Vanity URL Input Group -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="basic-url" class="col-form-label">Vanity URL</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
                                                    <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Input group with dropdown and text input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Dropdown + Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Dropdown
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="!#">Action</a>
                                                        <a class="dropdown-item" href="!#">Another action</a>
                                                        <a class="dropdown-item" href="!#">Something else here</a>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- File input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="inputGroupFile04" class="col-form-label">File Input</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input class="form-control" type="file" id="inputGroupFile04">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Input Group Select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="inputGroupSelect01" class="col-form-label">Input Group Select</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="inputGroupSelect01">Options</label>
                                                    <select class="form-select" id="inputGroupSelect01">
                                                        <option selected>Choose...</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">Floating Labels </h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <!-- Floating Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Email address</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="floatingInputEmail" placeholder="name@example.com">
                                                    <label for="floatingInputEmail">Email address</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Floating Textarea -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="floatingTextarea" class="col-form-label">Comments</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-floating">
                                                    <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" style="height: 100px"></textarea>
                                                    <label for="floatingTextarea">Comments</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                    <div class="col-lg-6">

                                        <!-- Floating Password -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="floatingPassword" class="col-form-label">Password</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-floating">
                                                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                                                    <label for="floatingPassword">Password</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Floating Select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="floatingSelect" class="col-form-label">Select Menu</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-floating">
                                                    <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                                                        <option selected>Open this select menu</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                    </select>
                                                    <label for="floatingSelect">Works with selects</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">Input Sizes </h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <!-- Small Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-input-small" class="col-form-label">Small</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-input-small" name="example-input-small" class="form-control form-control-sm" placeholder=".input-sm">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Large Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-input-large" class="col-form-label">Large</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-input-large" name="example-input-large" class="form-control form-control-lg" placeholder=".input-lg">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Large Select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Large Select</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-select form-select-lg">
                                                    <option selected>Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                    <div class="col-lg-6">

                                        <!-- Normal Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-input-normal" class="col-form-label">Normal</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" id="example-input-normal" name="example-input-normal" class="form-control" placeholder="Normal">
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Grid Size Input -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label for="example-gridsize" class="col-form-label">Grid Sizes</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <input type="text" id="example-gridsize" class="form-control" placeholder=".col-sm-4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Small Select -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Small Select</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-select form-select-sm">
                                                    <option selected>Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">Checks, Radios and Switches </h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <!-- Default Checkboxes -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Checkboxes</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input" id="checkDefault">
                                                    <label class="form-check-label" for="checkDefault">Default Checkbox</label>
                                                </div>

                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input form-check-input-light" id="checkLight">
                                                    <label class="form-check-label" for="checkLight">Light Checkbox</label>
                                                </div>

                                                <div class="mb-2">
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="checkInline1" checked>
                                                        <label class="form-check-label" for="checkInline1">Inline 1</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" id="checkInline2">
                                                        <label class="form-check-label" for="checkInline2">Inline 2</label>
                                                    </div>
                                                </div>

                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" id="checkIndeterminate">
                                                    <label class="form-check-label" for="checkIndeterminate">
                                                        Disabled indeterminate checkbox
                                                    </label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="checkCheckedDisabled" checked disabled>
                                                    <label class="form-check-label" for="checkCheckedDisabled">
                                                        Disabled checked checkbox
                                                    </label>
                                                </div>

                                                <h5 class="mt-3">Sizes</h5>

                                                <div class="form-check fs-lg mb-2">
                                                    <input type="checkbox" class="form-check-input mt-1" id="checkSize1" checked>
                                                    <label class="form-check-label fs-base" for="checkSize1">I'm 16px Checkbox</label>
                                                </div>

                                                <div class="form-check form-check-secondary fs-xxl mb-2">
                                                    <input type="checkbox" class="form-check-input mt-1" id="checkSize2" checked>
                                                    <label class="form-check-label fs-base" for="checkSize2">i'm 20px Checkbox</label>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Switches -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Switches</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-check form-switch mb-2">
                                                    <input type="checkbox" class="form-check-input" id="switch1" checked>
                                                    <label class="form-check-label" for="switch1">Enabled Switch</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input type="checkbox" class="form-check-input" id="switch2" disabled>
                                                    <label class="form-check-label" for="switch2">Disabled Switch</label>
                                                </div>

                                                <h5 class="mt-3">Sizes</h5>

                                                <div class="form-check form-switch fs-lg mb-2">
                                                    <input type="checkbox" class="form-check-input mt-1" id="checkboxSize16" checked>
                                                    <label class="form-check-label fs-base" for="checkboxSize16">I'm 16px Switch</label>
                                                </div>

                                                <div class="form-check form-switch form-check-secondary fs-xxl mb-2">
                                                    <input type="checkbox" class="form-check-input mt-1" id="checkboxSize20" checked>
                                                    <label class="form-check-label fs-base" for="checkboxSize20">I'm 20px Switch</label>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Colored Checkboxes -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Colored Checkboxes</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="d-flex flex-wrap gap-4">
                                                    <div>
                                                        <div class="form-check form-check-primary mb-2">
                                                            <input type="checkbox" class="form-check-input" id="checkPrimary" checked>
                                                            <label class="form-check-label" for="checkPrimary">Primary</label>
                                                        </div>
                                                        <div class="form-check form-check-secondary mb-2">
                                                            <input type="checkbox" class="form-check-input" id="checkSecondary" checked>
                                                            <label class="form-check-label" for="checkSecondary">Secondary</label>
                                                        </div>
                                                        <div class="form-check form-check-success mb-2">
                                                            <input type="checkbox" class="form-check-input" id="checkSuccess" checked>
                                                            <label class="form-check-label" for="checkSuccess">Success</label>
                                                        </div>
                                                        <div class="form-check form-check-info mb-2">
                                                            <input type="checkbox" class="form-check-input" id="checkInfo" checked>
                                                            <label class="form-check-label" for="checkInfo">Info</label>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="form-check form-check-warning mb-2">
                                                            <input type="checkbox" class="form-check-input" id="checkWarning" checked>
                                                            <label class="form-check-label" for="checkWarning">Warning</label>
                                                        </div>
                                                        <div class="form-check form-check-danger mb-2">
                                                            <input type="checkbox" class="form-check-input" id="checkDanger" checked>
                                                            <label class="form-check-label" for="checkDanger">Danger</label>
                                                        </div>
                                                        <div class="form-check form-check-dark">
                                                            <input type="checkbox" class="form-check-input" id="checkDark" checked>
                                                            <label class="form-check-label" for="checkDark">Dark</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Colored Checkboxes -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Colored Switches</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="d-flex flex-wrap gap-4">
                                                    <div>
                                                        <div class="form-check form-check-primary form-switch mb-2">
                                                            <input type="checkbox" class="form-check-input" id="switchPrimary" checked>
                                                            <label class="form-check-label" for="switchPrimary">Primary</label>
                                                        </div>
                                                        <div class="form-check form-check-secondary form-switch mb-2">
                                                            <input type="checkbox" class="form-check-input" id="switchSecondary" checked>
                                                            <label class="form-check-label" for="switchSecondary">Secondary</label>
                                                        </div>
                                                        <div class="form-check form-check-success form-switch mb-2">
                                                            <input type="checkbox" class="form-check-input" id="switchSuccess" checked>
                                                            <label class="form-check-label" for="switchSuccess">Success</label>
                                                        </div>
                                                        <div class="form-check form-check-info form-switch mb-2">
                                                            <input type="checkbox" class="form-check-input" id="switchInfo" checked>
                                                            <label class="form-check-label" for="switchInfo">Info</label>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="form-check form-check-warning form-switch mb-2">
                                                            <input type="checkbox" class="form-check-input" id="switchWarning" checked>
                                                            <label class="form-check-label" for="switchWarning">Warning</label>
                                                        </div>
                                                        <div class="form-check form-check-danger form-switch mb-2">
                                                            <input type="checkbox" class="form-check-input" id="switchDanger" checked>
                                                            <label class="form-check-label" for="switchDanger">Danger</label>
                                                        </div>
                                                        <div class="form-check form-check-dark form-switch">
                                                            <input type="checkbox" class="form-check-input" id="switchDark" checked>
                                                            <label class="form-check-label" for="switchDark">Dark</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                    <div class="col-lg-6">

                                        <!-- Default Radios -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Radios</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="form-check mb-2">
                                                    <input type="radio" class="form-check-input" name="gridRadio" id="radio1" checked>
                                                    <label class="form-check-label" for="radio1">Option 1</label>
                                                </div>

                                                <div class="form-check mb-2">
                                                    <input type="radio" class="form-check-input" name="gridRadio" id="radio2">
                                                    <label class="form-check-label" for="radio2">Option 2</label>
                                                </div>

                                                <div class="mb-2">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
                                                        <label class="form-check-label" for="inlineRadio1">Inline 1</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                                        <label class="form-check-label" for="inlineRadio2">Inline 2</label>
                                                    </div>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="disabledRadioOptions" id="inlineRadio3" value="option3" checked disabled>
                                                    <label class="form-check-label" for="inlineRadio3">Disabled Checked Radio</label>
                                                </div>

                                                <h5 class="mt-3">Sizes</h5>

                                                <div class="mb-2">
                                                    <div class="form-check fs-lg form-check-inline">
                                                        <input class="form-check-input" type="radio" name="paymentMethod" id="radioCash" value="cash" checked>
                                                        <label class="form-check-label fs-base" for="radioCash">Cash</label>
                                                    </div>
                                                    <div class="form-check fs-lg form-check-inline">
                                                        <input class="form-check-input" type="radio" name="paymentMethod" id="radioCard" value="card">
                                                        <label class="form-check-label fs-base" for="radioCard">Card</label>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <div class="form-check fs-xxl form-check-inline">
                                                        <input class="form-check-input" type="radio" name="deliveryOption" id="radioPickup" value="pickup" checked>
                                                        <label class="form-check-label fs-base" for="radioPickup">Pickup</label>
                                                    </div>
                                                    <div class="form-check fs-xxl form-check-inline">
                                                        <input class="form-check-input" type="radio" name="deliveryOption" id="radioHome" value="home">
                                                        <label class="form-check-label fs-base" for="radioHome">Home Delivery</label>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Reverse -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Reverse</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="w-lg-50">
                                                    <div class="form-check form-check-reverse mb-2">
                                                        <input class="form-check-input" type="checkbox" value="" id="reverseCheck1" checked>
                                                        <label class="form-check-label" for="reverseCheck1">
                                                            Reverse checkbox
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-reverse mb-2">
                                                        <input class="form-check-input" type="radio" value="" id="reverseCheck2">
                                                        <label class="form-check-label" for="reverseCheck2">
                                                            Disabled reverse radio
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch form-check-reverse">
                                                        <input class="form-check-input" type="checkbox" id="switchCheckReverse" checked>
                                                        <label class="form-check-label" for="switchCheckReverse">Reverse switch checkbox input</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Colored Radios -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Colored Radios</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="d-flex flex-wrap gap-4">
                                                    <div>
                                                        <div class="form-check form-check-primary mb-2">
                                                            <input type="radio" class="form-check-input" name="radioPrimary" id="radioPrimary" checked>
                                                            <label class="form-check-label" for="radioPrimary">Primary</label>
                                                        </div>
                                                        <div class="form-check form-check-secondary mb-2">
                                                            <input type="radio" class="form-check-input" name="radioSecondary" id="radioSecondary" checked>
                                                            <label class="form-check-label" for="radioSecondary">Secondary</label>
                                                        </div>
                                                        <div class="form-check form-check-success mb-2">
                                                            <input type="radio" class="form-check-input" name="radioSuccess" id="radioSuccess" checked>
                                                            <label class="form-check-label" for="radioSuccess">Success</label>
                                                        </div>
                                                        <div class="form-check form-check-info mb-2">
                                                            <input type="radio" class="form-check-input" name="radioInfo" id="radioInfo" checked>
                                                            <label class="form-check-label" for="radioInfo">Info</label>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="form-check form-check-warning mb-2">
                                                            <input type="radio" class="form-check-input" name="radioWarning" id="radioWarning" checked>
                                                            <label class="form-check-label" for="radioWarning">Warning</label>
                                                        </div>
                                                        <div class="form-check form-check-danger mb-2">
                                                            <input type="radio" class="form-check-input" name="radioDanger" id="radioDanger" checked>
                                                            <label class="form-check-label" for="radioDanger">Danger</label>
                                                        </div>
                                                        <div class="form-check form-check-dark">
                                                            <input type="radio" class="form-check-input" name="radioDark" id="radioDark" checked>
                                                            <label class="form-check-label" for="radioDark">Dark</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-top border-dashed my-3"></div>

                                        <!-- Toggle Checkboxes -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Checkbox Toggle</label>
                                            </div>

                                            <div class="col-lg-8">
                                                <div class="mb-2">
                                                    <input type="checkbox" class="btn-check" id="btncheck1">
                                                    <label class="btn btn-outline-primary" for="btncheck1">Single Toggle</label>
                                                </div>

                                                <div class="btn-group" role="group" aria-label="Checkbox toggle group">
                                                    <input type="checkbox" class="btn-check" id="btncheck2">
                                                    <label class="btn btn-outline-primary" for="btncheck2">One</label>

                                                    <input type="checkbox" class="btn-check" id="btncheck3">
                                                    <label class="btn btn-outline-primary" for="btncheck3">Two</label>

                                                    <input type="checkbox" class="btn-check" id="btncheck4">
                                                    <label class="btn btn-outline-primary" for="btncheck4">Three</label>
                                                </div>
                                            </div>
                                        </div>

                                          <div class="border-top border-dashed my-3"></div>
                                        
                                        <!-- Toggle Radios -->
                                        <div class="row g-lg-4 g-2">
                                            <div class="col-lg-4">
                                                <label class="col-form-label">Radio Toggle</label>
                                            </div>

                                            <div class="col-lg-8">
                                                <div class="btn-group" role="group" aria-label="Radio toggle group">
                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" checked>
                                                    <label class="btn btn-outline-secondary" for="btnradio1">Left</label>

                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2">
                                                    <label class="btn btn-outline-secondary" for="btnradio2">Middle</label>

                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3">
                                                    <label class="btn btn-outline-secondary" for="btnradio3">Right</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->


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
        document.addEventListener("DOMContentLoaded", function () {
            const checkbox = document.getElementById('checkIndeterminate');
            checkbox.indeterminate = true;
        });
    </script>

</body>

</html>