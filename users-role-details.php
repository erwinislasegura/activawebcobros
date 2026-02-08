<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Role Details"; include('partials/title-meta.php'); ?>

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

                <div class="row">
                    <div class="col-12">

                        <div class="d-flex align-items-sm-center flex-sm-row flex-column my-3">
                            <div class="flex-grow-1">
                                <h4 class="fs-xl mb-1">Role Details</h4>
                                <p class="text-muted mb-0">Define and manage roles to streamline operations and ensure secure access control.</p>
                            </div>

                            <div class="text-end mt-3 mt-sm-0">
                                <a href="javascript:void(0);" class="btn btn-success">
                                    <i class="ti ti-plus me-1"></i> Add New Role
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-lg-3">
                                <div class="card">
                                    <div class="position-absolute top-0 end-0" style="width: 180px;">
                                        <svg style="opacity: 0.075; width: 100%; height: auto;" width="600" height="560" viewBox="0 0 600 560" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_948_1464)">
                                                <mask id="mask0_948_1464" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="600" height="1200">
                                                    <path d="M0 0L0 1200H600L600 0H0Z" fill="white" />
                                                </mask>
                                                <g mask="url(#mask0_948_1464)">
                                                    <path d="M537.448 166.697L569.994 170.892L550.644 189.578L537.448 166.697Z" fill="#FF4C3E" />
                                                </g>
                                                <mask id="mask1_948_1464" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="600" height="1200">
                                                    <path d="M0 0L0 1200H600L600 0H0Z" fill="white" />
                                                </mask>
                                                <g mask="url(#mask1_948_1464)">
                                                    <path d="M364.093 327.517L332.306 359.304C321.885 369.725 304.989 369.725 294.568 359.304L262.781 327.517C252.36 317.096 252.36 300.2 262.781 289.779L294.568 257.992C304.989 247.571 321.885 247.571 332.306 257.992L364.093 289.779C374.514 300.2 374.514 317.096 364.093 327.517Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M377.923 101.019L315.106 163.836C299.517 179.425 274.242 179.425 258.653 163.836L195.836 101.019C180.247 85.4301 180.247 60.1551 195.836 44.5661L258.653 -18.251C274.242 -33.84 299.517 -33.84 315.106 -18.251L377.923 44.5661C393.512 60.1551 393.512 85.4301 377.923 101.019Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M696.956 -50.1542L650.648 -3.84605C635.059 11.743 609.784 11.743 594.195 -3.84605L547.887 -50.1542C532.298 -65.7432 532.298 -91.0182 547.887 -106.607L594.195 -152.915C609.784 -168.504 635.059 -168.504 650.648 -152.915L696.956 -106.607C712.545 -91.0172 712.545 -65.7432 696.956 -50.1542Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M758.493 103.825L712.185 150.133C696.596 165.722 671.321 165.722 655.733 150.133L609.425 103.825C593.836 88.2359 593.836 62.9608 609.425 47.3718L655.733 1.06386C671.322 -14.5251 696.597 -14.5251 712.185 1.06386L758.493 47.3718C774.082 62.9608 774.082 88.2359 758.493 103.825Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M674.716 80.202L501.67 253.248C486.081 268.837 460.806 268.837 445.217 253.248L272.171 80.202C256.582 64.613 256.582 39.338 272.171 23.749L445.217 -149.297C460.806 -164.886 486.081 -164.886 501.67 -149.297L674.716 23.75C690.305 39.339 690.305 64.613 674.716 80.202Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M579.394 334.046L523.831 389.609C508.242 405.198 482.967 405.198 467.378 389.609L411.815 334.046C396.226 318.457 396.226 293.182 411.815 277.593L467.378 222.03C482.967 206.441 508.242 206.441 523.831 222.03L579.394 277.593C594.983 293.182 594.983 318.457 579.394 334.046Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M185.618 87.2381L158.648 114.208C146.305 126.551 126.293 126.551 113.95 114.208L86.9799 87.2381C74.6369 74.8951 74.6369 54.883 86.9799 42.539L113.95 15.569C126.293 3.22605 146.305 3.22605 158.648 15.569L185.618 42.539C197.961 54.882 197.961 74.8941 185.618 87.2381Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M249.319 23.767L228.859 44.227C221.817 51.269 210.4 51.269 203.358 44.227L182.898 23.767C175.856 16.725 175.856 5.30798 182.898 -1.73402L203.358 -22.194C210.4 -29.236 221.817 -29.236 228.859 -22.194L249.319 -1.73402C256.361 5.30798 256.361 16.725 249.319 23.767Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M375.3 217.828L354.84 238.288C347.798 245.33 336.381 245.33 329.339 238.288L308.879 217.828C301.837 210.786 301.837 199.369 308.879 192.327L329.339 171.867C336.381 164.825 347.798 164.825 354.84 171.867L375.3 192.327C382.342 199.369 382.342 210.786 375.3 217.828Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M262.326 229.367L255.702 235.991C252.281 239.412 246.734 239.412 243.313 235.991L236.689 229.367C233.268 225.946 233.268 220.399 236.689 216.978L243.313 210.354C246.734 206.933 252.281 206.933 255.702 210.354L262.326 216.978C265.747 220.399 265.747 225.946 262.326 229.367Z" stroke="#089df1" stroke-width="2" stroke-miterlimit="10" />
                                                    <path d="M403.998 311.555L372.211 343.342C361.79 353.763 344.894 353.763 334.473 343.342L302.686 311.555C292.265 301.134 292.265 284.238 302.686 273.817L334.473 242.03C344.894 231.609 361.79 231.609 372.211 242.03L403.998 273.817C414.419 284.238 414.419 301.134 403.998 311.555Z" fill="#089df1" />
                                                    <path d="M417.828 85.0572L355.011 147.874C339.422 163.463 314.147 163.463 298.558 147.874L235.741 85.0572C220.152 69.4682 220.152 44.1931 235.741 28.6051L298.558 -34.2119C314.147 -49.8009 339.422 -49.8009 355.011 -34.2119L417.828 28.6051C433.417 44.1931 433.417 69.4682 417.828 85.0572Z" fill="#7b70ef" />
                                                    <path d="M714.621 64.24L541.575 237.286C525.986 252.875 500.711 252.875 485.122 237.286L312.076 64.24C296.487 48.651 296.487 23.376 312.076 7.787L485.122 -165.259C500.711 -180.848 525.986 -180.848 541.575 -165.259L714.621 7.787C730.21 23.377 730.21 48.651 714.621 64.24Z" fill="#f9bf59" />
                                                    <path d="M619.299 318.084L563.736 373.647C548.147 389.236 522.872 389.236 507.283 373.647L451.72 318.084C436.131 302.495 436.131 277.22 451.72 261.631L507.283 206.068C522.872 190.479 548.147 190.479 563.736 206.068L619.299 261.631C634.888 277.221 634.888 302.495 619.299 318.084Z" fill="#089df1" />
                                                    <path d="M225.523 71.276L198.553 98.2459C186.21 110.589 166.198 110.589 153.854 98.2459L126.884 71.276C114.541 58.933 114.541 38.921 126.884 26.578L153.854 -0.392014C166.197 -12.735 186.209 -12.735 198.553 -0.392014L225.523 26.578C237.866 38.92 237.866 58.932 225.523 71.276Z" fill="#f7577e" />
                                                    <path d="M289.224 7.80493L268.764 28.2649C261.722 35.3069 250.305 35.3069 243.263 28.2649L222.803 7.80493C215.761 0.762926 215.761 -10.6542 222.803 -17.6962L243.263 -38.1561C250.305 -45.1981 261.722 -45.1981 268.764 -38.1561L289.224 -17.6962C296.266 -10.6542 296.266 0.762926 289.224 7.80493Z" fill="#f7577e" />
                                                    <path d="M415.205 201.866L394.745 222.326C387.703 229.368 376.286 229.368 369.244 222.326L348.784 201.866C341.742 194.824 341.742 183.407 348.784 176.365L369.244 155.905C376.286 148.863 387.703 148.863 394.745 155.905L415.205 176.365C422.247 183.407 422.247 194.824 415.205 201.866Z" fill="#f7577e" />
                                                    <path d="M302.231 213.405L295.607 220.029C292.186 223.45 286.639 223.45 283.218 220.029L276.594 213.405C273.173 209.984 273.173 204.437 276.594 201.016L283.218 194.392C286.639 190.971 292.186 190.971 295.607 194.392L302.231 201.016C305.652 204.437 305.652 209.984 302.231 213.405Z" fill="#f7577e" />
                                                </g>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_948_1464">
                                                    <rect width="560" height="600" fill="white" transform="matrix(0 -1 1 0 0 560)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xl rounded bg-primary-subtle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-shield-lock fs-24 text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <h5 class="mb-1">Security Officer</h5>
                                                <p class="text-muted mb-0 fs-base">Handles platform safety and protocol
                                                    reviews.</p>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="dropdown">
                                                    <a href="#" class="text-muted fs-xl" data-bs-toggle="dropdown">
                                                        <i class="ti ti-dots-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                                        <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Remove</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <ul class="list-unstyled mb-3">
                                            <li class="d-flex align-items-center mb-2">
                                                <span class="ti ti-check fs-lg text-success me-2"></span> Daily Risk
                                                Assessment
                                            </li>
                                            <li class="d-flex align-items-center mb-2">
                                                <span class="ti ti-check fs-lg text-success me-2"></span> Manage Security
                                                Logs
                                            </li>
                                            <li class="d-flex align-items-center mb-2">
                                                <span class="ti ti-check fs-lg text-success me-2"></span> Control Access
                                                Rights
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <span class="ti ti-check fs-lg text-success me-2"></span> Emergency
                                                Protocols
                                            </li>
                                        </ul>

                                        <p class="mb-2 text-muted">Total 17 users</p>
                                        <div class="avatar-group avatar-group-sm mb-3">
                                            <div class="avatar">
                                                <img src="assets/images/users/user-7.jpg" alt="" class="rounded-circle avatar-sm">
                                            </div>
                                            <div class="avatar">
                                                <img src="assets/images/users/user-8.jpg" alt="" class="rounded-circle avatar-sm">
                                            </div>
                                            <div class="avatar">
                                                <img src="assets/images/users/user-9.jpg" alt="" class="rounded-circle avatar-sm">
                                            </div>
                                            <div class="avatar">
                                                <img src="assets/images/users/user-10.jpg" alt="" class="rounded-circle avatar-sm">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted fs-xs"><i class="ti ti-clock me-1"></i>Updated 1 hour ago</span>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#editRoleModal" class="btn btn-sm btn-outline-primary rounded-pill">Edit Role</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 col-lg-9">
                                <div data-table data-table-rows-per-page="8" class="card">
                                    <div class="card-header border-light justify-content-between">

                                        <div class="d-flex gap-2">
                                            <div class="app-search">
                                                <input data-table-search type="search" class="form-control" placeholder="Search users...">
                                                <i data-lucide="search" class="app-search-icon text-muted"></i>
                                            </div>
                                            <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="me-2 fw-semibold">Filter By:</span>

                                            <!-- Status Filter -->
                                            <div class="app-search">
                                                <select data-table-filter="status" class="form-select form-control my-1 my-md-0">
                                                    <option value="All">Status</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                    <option value="Suspended">Suspended</option>
                                                </select>
                                                <i data-lucide="user-check" class="app-search-icon text-muted"></i>
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

                                            <button type="button" data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-secondary">Add User</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                                <tr class="text-uppercase fs-xxs">
                                                    <th class="ps-3" style="width: 1%;">
                                                        <input data-table-select-all class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox">
                                                    </th>
                                                    <th data-table-sort>ID</th>
                                                    <th data-table-sort="user">User</th>
                                                    <th data-table-sort>Joined Date</th>
                                                    <th data-table-sort data-column="status">Status</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Row 1 -->
                                                <tr>
                                                    <td class="ps-3">
                                                        <input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox">
                                                    </td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR76129</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-1.jpg" class="img-fluid rounded-circle" alt="Elena Carter"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Elena Carter</a></h5>
                                                                <p class="text-muted fs-xs mb-0">elena@webcore.dev</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>19 Jul, 2025 <small class="text-muted">11:00 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 2 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR58647</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-2.jpg" class="img-fluid rounded-circle" alt="Jordan Smith"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Jordan Smith</a></h5>
                                                                <p class="text-muted fs-xs mb-0">jordan@mediaflow.com</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>21 Jul, 2025 <small class="text-muted">9:15 AM</small></td>
                                                    <td><span class="badge bg-warning-subtle text-warning badge-label">Inactive</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 3 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR94715</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-3.jpg" class="img-fluid rounded-circle" alt="Lucas Brown"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Lucas Brown</a></h5>
                                                                <p class="text-muted fs-xs mb-0">lucas@intechlabs.com</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>20 Jul, 2025 <small class="text-muted">3:00 PM</small></td>
                                                    <td><span class="badge bg-danger-subtle text-danger badge-label">Suspended</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 4 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR40289</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-4.jpg" class="img-fluid rounded-circle" alt="Sophia Green"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Sophia Green</a></h5>
                                                                <p class="text-muted fs-xs mb-0">sophia@skygrid.org</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>18 Jul, 2025 <small class="text-muted">10:30 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 5 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR23981</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-5.jpg" class="img-fluid rounded-circle" alt="Ethan Ross"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Ethan Ross</a></h5>
                                                                <p class="text-muted fs-xs mb-0">ethan@logico.io</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>17 Jul, 2025 <small class="text-muted">5:55 PM</small></td>
                                                    <td><span class="badge bg-warning-subtle text-warning badge-label">Inactive</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 6 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR83742</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-6.jpg" class="img-fluid rounded-circle" alt="Zara Mitchell"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Zara Mitchell</a></h5>
                                                                <p class="text-muted fs-xs mb-0">zara@fusionui.com</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>22 Jul, 2025 <small class="text-muted">9:10 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 7 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR51268</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-7.jpg" class="img-fluid rounded-circle" alt="Benjamin Gray"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Benjamin Gray</a></h5>
                                                                <p class="text-muted fs-xs mb-0">benjamin@stackpulse.dev</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>20 Jul, 2025 <small class="text-muted">6:25 PM</small></td>
                                                    <td><span class="badge bg-warning-subtle text-warning badge-label">Inactive</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 8 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR17456</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-8.jpg" class="img-fluid rounded-circle" alt="Ava Patel"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Ava Patel</a></h5>
                                                                <p class="text-muted fs-xs mb-0">ava@cleardash.io</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>23 Jul, 2025 <small class="text-muted">8:45 AM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 9 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR96421</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-9.jpg" class="img-fluid rounded-circle" alt="Mason Rivera"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Mason Rivera</a></h5>
                                                                <p class="text-muted fs-xs mb-0">mason@softmeta.app</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>21 Jul, 2025 <small class="text-muted">2:10 PM</small></td>
                                                    <td><span class="badge bg-danger-subtle text-danger badge-label">Suspended</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Row 10 -->
                                                <tr>
                                                    <td class="ps-3"><input class="form-check-input form-check-input-light fs-14 file-item-check mt-0" type="checkbox"></td>
                                                    <td>
                                                        <h5 class="m-0"><a href="users-profile.php" class="link-reset">#USR71539</a></h5>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar avatar-sm"><img src="assets/images/users/user-10.jpg" class="img-fluid rounded-circle" alt="Chloe Walker"></div>
                                                            <div>
                                                                <h5 class="fs-base mb-0"><a href="users-profile.php" class="link-reset">Chloe Walker</a></h5>
                                                                <p class="text-muted fs-xs mb-0">chloe@flowbase.org</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>19 Jul, 2025 <small class="text-muted">12:35 PM</small></td>
                                                    <td><span class="badge bg-success-subtle text-success badge-label">Active</span></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-eye fs-lg"></i></a>
                                                            <a href="#" class="btn btn-default btn-icon btn-sm"><i class="ti ti-edit fs-lg"></i></a>
                                                            <a href="#" data-table-delete-row class="btn btn-default btn-icon btn-sm"><i class="ti ti-trash fs-lg"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="card-footer border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div data-table-pagination-info="roles"></div>
                                            <div data-table-pagination></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- end row-->

                    </div> <!-- end col-->
                </div> <!-- end row-->


                <!-- Edit Role Modal -->
                <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="editRoleForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="editRoleName" class="form-label">Role Name</label>
                                            <input type="text" class="form-control" id="editRoleName" value="Developer" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="editRoleDescription" class="form-label">Description</label>
                                            <input type="text" class="form-control" id="editRoleDescription" value="Builds and maintains the platform core features." required>
                                        </div>

                                        <div class="col-12">
                                            <label for="editRoleResponsibilities" class="form-label">Key Responsibilities</label>
                                            <textarea class="form-control" id="editRoleResponsibilities" rows="4" required>
Codebase Maintenance
API Integration
Unit Testing
Feature Deployment</textarea>
                                            <small class="text-muted">Separate each item by comma or line</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="editRoleUsers" class="form-label">Assign Users</label>
                                            <select class="form-select" id="editRoleUsers" multiple>
                                                <option value="1" selected>Leah Kim</option>
                                                <option value="2" selected>David Tran</option>
                                                <option value="3">Michael Brown</option>
                                                <option value="4">Emma Wilson</option>
                                            </select>
                                            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="editRoleIcon" class="form-label">Role Icon</label>
                                            <input type="text" class="form-control" id="editRoleIcon" value="ti ti-code">
                                            <small class="text-muted">Use icon class from your icon library</small>
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


                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form id="addUserForm">
                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label for="userFullName" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="userFullName" placeholder="Enter full name" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="userEmail" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="userEmail" placeholder="Enter email" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="userRole" class="form-label">Role</label>
                                            <select class="form-select" id="userRole" required>
                                                <option value="">Select role</option>
                                                <option value="Project Manager">Project Manager</option>
                                                <option value="Developer">Developer</option>
                                                <option value="Support Lead">Support Lead</option>
                                                <option value="Security Officer">Security Officer</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="userStatus" class="form-label">Status</label>
                                            <select class="form-select" id="userStatus" required>
                                                <option value="">Select status</option>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                                <option value="Suspended">Suspended</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="userAvatar" class="form-label">User Avatar</label>
                                            <input class="form-control" type="file" id="userAvatar" accept="image/*">
                                            <small class="text-muted">Optional: Upload avatar image</small>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add User</button>
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