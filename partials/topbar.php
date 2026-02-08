<!-- Topbar Start -->
<header class="app-topbar">
    <?php $municipalidad = get_municipalidad(); ?>
    <?php
    $logoTopbarHeight = (int) ($municipalidad['logo_topbar_height'] ?? 56);
    $logoTopbarHeightSm = min($logoTopbarHeight, 56);
    ?>
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-2">
            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="index.php" class="logo-light">
                    <span class="logo-lg d-none d-sm-inline">
                        <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoTopbarHeight; ?>px;">
                    </span>
                    <span class="logo-sm d-inline d-sm-none">
                        <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoTopbarHeightSm; ?>px;">
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="index.php" class="logo-dark">
                    <span class="logo-lg d-none d-sm-inline">
                        <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoTopbarHeight; ?>px;">
                    </span>
                    <span class="logo-sm d-inline d-sm-none">
                        <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoTopbarHeightSm; ?>px;">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button btn btn-default btn-icon">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>

        </div> <!-- .d-flex-->

        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">

            <!-- Search -->
            <form class="app-search d-none d-md-flex me-2 flex-grow-1 flex-lg-grow-0" action="busqueda.php" method="get">
                <input type="search" class="form-control topbar-search rounded-pill" name="q" placeholder="Buscar en el sistema" aria-label="Buscar en el sistema">
                <i data-lucide="search" class="app-search-icon text-muted"></i>
            </form>

            <!-- Theme Mode Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link" data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" aria-haspopup="false" aria-expanded="false">
                        <i data-lucide="sun" class="fs-xxl"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end thememode-dropdown">

                        <li>
                            <label class="dropdown-item">
                                <i data-lucide="sun" class="align-middle me-1 fs-16"></i>
                                <span class="align-middle">Light</span>
                                <input class="form-check-input" type="radio" name="data-bs-theme" value="light">
                            </label>
                        </li>

                        <li>
                            <label class="dropdown-item">
                                <i data-lucide="moon" class="align-middle me-1 fs-16"></i>
                                <span class="align-middle">Dark</span>
                                <input class="form-check-input" type="radio" name="data-bs-theme" value="dark">
                            </label>
                        </li>

                        <li>
                            <label class="dropdown-item">
                                <i data-lucide="monitor-cog" class="align-middle me-1 fs-16"></i>
                                <span class="align-middle">System</span>
                                <input class="form-check-input" type="radio" name="data-bs-theme" value="system">
                            </label>
                        </li>

                    </ul> <!-- end dropdown-menu-->
                </div> <!-- end dropdown-->
            </div> <!-- end topbar item-->

            <!-- FullScreen -->
            <div class="topbar-item">
                <button class="topbar-link" type="button" data-toggle="fullscreen">
                    <i data-lucide="maximize" class="fs-xxl fullscreen-off"></i>
                    <i data-lucide="minimize" class="fs-xxl fullscreen-on"></i>
                </button>
            </div>

            <!-- Light/Dark Mode Button -->
            <div class="topbar-item d-none">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i data-lucide="moon" class="fs-xxl mode-light-moon"></i>
                </button>
            </div>

            <!-- Monocrome Mode Button -->
            <div class="topbar-item">
                <button class="topbar-link" type="button" id="monochrome-mode">
                    <i data-lucide="palette" class="fs-xxl"></i>
                </button>
            </div>

            <!-- User Dropdown -->
            <div class="topbar-item nav-user">
                <?php
                $userName = $_SESSION['user']['nombre'] ?? 'Usuario';
                $userLastName = $_SESSION['user']['apellido'] ?? '';
                $userRole = $_SESSION['user']['rol'] ?? 'Sin rol';
                $userFullName = trim($userName . ' ' . $userLastName);
                $userAvatar = $_SESSION['user']['avatar_path'] ?? '';
                if ($userAvatar === '') {
                    $userAvatar = 'assets/images/users/user-1.jpg';
                }
                ?>
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" data-bs-offset="0,19" href="#!" aria-haspopup="false" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($userAvatar, ENT_QUOTES, 'UTF-8'); ?>" width="32" class="rounded-circle me-lg-2 d-flex" alt="user-image">
                        <div class="d-lg-flex align-items-center gap-1 d-none">
                            <h5 class="my-0"><?php echo htmlspecialchars($userFullName ?: 'Usuario', ENT_QUOTES, 'UTF-8'); ?></h5>
                            <i class="ti ti-chevron-down align-middle"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- Header -->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Usuario activo</h6>
                            <span class="fs-12 fw-semibold text-muted"><?php echo htmlspecialchars($userFullName ?: 'Usuario', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="d-block fs-12 text-muted"><?php echo htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <!-- Edit Profile -->
                        <a href="usuarios-editar.php?id=<?php echo (int) ($_SESSION['user']['id'] ?? 0); ?>" class="dropdown-item">
                            <i class="ti ti-user-circle me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Editar perfil</span>
                        </a>

                        <!-- Change Password -->
                        <a href="auth-new-pass.php" class="dropdown-item">
                            <i class="ti ti-lock me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Cambiar contraseña</span>
                        </a>

                        <!-- Divider -->
                        <div class="dropdown-divider"></div>

                        <!-- Logout -->
                        <a href="logout.php" class="dropdown-item fw-semibold">
                            <i class="ti ti-logout-2 me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Cerrar aplicación</span>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</header>
<!-- Topbar End -->
