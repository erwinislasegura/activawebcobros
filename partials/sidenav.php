<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <!-- Brand Logo -->
    <?php $municipalidad = get_municipalidad(); ?>
    <?php
    $logoSidenavHeight = (int) ($municipalidad['logo_sidenav_height'] ?? 48);
    $logoSidenavHeightSm = (int) ($municipalidad['logo_sidenav_height_sm'] ?? 36);
    ?>
    <a href="index.php" class="logo">
        <span class="logo logo-light">
            <span class="logo-lg">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeight; ?>px;">
            </span>
            <span class="logo-sm">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeightSm; ?>px;">
            </span>
        </span>

        <span class="logo logo-dark">
            <span class="logo-lg">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeight; ?>px;">
            </span>
            <span class="logo-sm">
                <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="logo" style="height: <?php echo $logoSidenavHeightSm; ?>px;">
            </span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-on-hover">
        <i class="ti ti-menu-4 fs-22 align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-offcanvas">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div class="scrollbar" data-simplebar>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title mt-2" data-lang="menu-title">Gestión</li>

            <?php if (has_permission('eventos', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-eventos" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-eventos">
                        <span class="menu-icon"><i data-lucide="calendar-check"></i></span>
                        <span class="menu-text">Gestión eventos</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-eventos">
                        <ul class="side-nav sub-menu">
                            <?php if (has_permission('eventos', 'create')) : ?>
                                <li class="side-nav-item">
                                    <a href="eventos-editar.php" class="side-nav-link">Nuevo evento</a>
                                </li>
                            <?php endif; ?>
                            <li class="side-nav-item">
                                <a href="eventos-invitacion-autoridades.php" class="side-nav-link">Invitar autoridades</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="eventos-autoridades-nueva.php" class="side-nav-link">Autoridades evento</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="eventos-acreditacion-medios.php" class="side-nav-link">Acreditación medios</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="eventos-autoridades-resumen.php" class="side-nav-link">Resumen eventos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="eventos-procesados.php" class="side-nav-link">Procesados</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('eventos', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-medios" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-medios">
                        <span class="menu-icon"><i data-lucide="megaphone"></i></span>
                        <span class="menu-text">Medios de comunicación</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-medios">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="eventos-acreditacion-medios.php" class="side-nav-link">Solicitudes de medios</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="medios-control-acceso.php" class="side-nav-link">Control de acceso</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('autoridades', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-autoridades" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-autoridades">
                        <span class="menu-icon"><i data-lucide="landmark"></i></span>
                        <span class="menu-text">Autoridades</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-autoridades">
                        <ul class="side-nav sub-menu">
                            <?php if (has_permission('autoridades', 'create')) : ?>
                                <li class="side-nav-item">
                                    <a href="autoridades-editar.php" class="side-nav-link">Crear autoridad</a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="autoridades-carga-masiva.php" class="side-nav-link">Carga masiva</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <li class="side-nav-item">
                <a href="calendar.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="calendar"></i></span>
                    <span class="menu-text">Calendario</span>
                </a>
            </li>

            <li class="side-nav-title" data-lang="settings-title">Administración</li>

            <?php if (has_permission('usuarios', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-usuarios" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-usuarios">
                        <span class="menu-icon"><i data-lucide="users"></i></span>
                        <span class="menu-text">Usuarios</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-usuarios">
                        <ul class="side-nav sub-menu">
                            <?php if (has_permission('usuarios', 'create')) : ?>
                                <li class="side-nav-item">
                                    <a href="usuarios-crear.php" class="side-nav-link">Crear usuario</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('roles', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-roles" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-roles">
                        <span class="menu-icon"><i data-lucide="key-round"></i></span>
                        <span class="menu-text">Roles y permisos</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-roles">
                        <ul class="side-nav sub-menu">
                            <?php if (has_permission('roles', 'create')) : ?>
                                <li class="side-nav-item">
                                    <a href="roles-editar.php" class="side-nav-link">Crear rol</a>
                                </li>
                            <?php endif; ?>
                            <li class="side-nav-item">
                                <a href="roles-permisos.php" class="side-nav-link">Matriz permisos</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('mantenedores', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-mantenedores" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-mantenedores">
                        <span class="menu-icon"><i data-lucide="settings-2"></i></span>
                        <span class="menu-text">Mantenedores</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-mantenedores">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item">
                                <a href="municipalidad.php" class="side-nav-link">Municipalidad</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="eventos-tipos.php" class="side-nav-link">Tipos evento</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="grupos-autoridades.php" class="side-nav-link">Grupos autoridades</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="notificaciones-correo.php" class="side-nav-link">Correo de envío</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="invitacion-correo.php" class="side-nav-link">Correo invitación</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="notificaciones-whatsapp.php" class="side-nav-link">WhatsApp envíos</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="configuracion-email.php" class="side-nav-link">Correo validación</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
