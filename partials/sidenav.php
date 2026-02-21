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

    <button class="button-on-hover">
        <i class="ti ti-menu-4 fs-22 align-middle"></i>
    </button>

    <button class="button-close-offcanvas">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="side-nav">
            <li class="side-nav-title mt-2" data-lang="menu-title">Flujo de cobros</li>

            <li class="side-nav-item">
                <a href="calendar.php" class="side-nav-link">
                    <span class="menu-icon"><i data-lucide="calendar"></i></span>
                    <span class="menu-text">Agenda</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="#modulo-clientes" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-clientes">
                    <span class="menu-icon"><i data-lucide="users-2"></i></span>
                    <span class="menu-text">1. Clientes</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="modulo-clientes">
                    <ul class="side-nav sub-menu">
                        <li class="side-nav-item"><a href="clientes-crear.php" class="side-nav-link">Alta cliente</a></li>
                        <li class="side-nav-item"><a href="clientes-servicios-asociar.php" class="side-nav-link">Asociar servicio</a></li>
                        <li class="side-nav-item"><a href="clientes-servicios.php" class="side-nav-link">Suspensiones</a></li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="#modulo-servicios" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-servicios">
                    <span class="menu-icon"><i data-lucide="briefcase"></i></span>
                    <span class="menu-text">2. Servicios</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="modulo-servicios">
                    <ul class="side-nav sub-menu">
                        <li class="side-nav-item"><a href="cobros-servicios-agregar.php" class="side-nav-link">Catálogo</a></li>
                        <li class="side-nav-item"><a href="tipos-servicios.php" class="side-nav-link">Tipos</a></li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a href="#modulo-cobros-servicios" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-cobros-servicios">
                    <span class="menu-icon"><i data-lucide="receipt"></i></span>
                    <span class="menu-text">3. Cobranza</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="modulo-cobros-servicios">
                    <ul class="side-nav sub-menu">
                        <li class="side-nav-item"><a href="cobros-servicios-registros.php" class="side-nav-link">Cobros</a></li>
                        <li class="side-nav-item"><a href="cobros-pagos.php" class="side-nav-link">Pagos</a></li>
                        <li class="side-nav-item"><a href="cobros-avisos.php" class="side-nav-link">Avisos</a></li>
                        <li class="side-nav-item"><a href="cobros-totales.php" class="side-nav-link">Totales</a></li>
                    </ul>
                </div>
            </li>

            <?php if (flow_user_can_access() && has_permission('flow', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-flow" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-flow">
                        <span class="menu-icon"><i data-lucide="credit-card"></i></span>
                        <span class="menu-text">4. Flow</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-flow">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item"><a href="flow/config/index.php" class="side-nav-link">Ajustes</a></li>
                            <li class="side-nav-item"><a href="flow/payments/new.php" class="side-nav-link">Nuevo pago</a></li>
                            <li class="side-nav-item"><a href="flow/orders/index.php" class="side-nav-link">Órdenes</a></li>
                            <li class="side-nav-item"><a href="flow/webhook/logs.php" class="side-nav-link">Webhook</a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

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
                                <li class="side-nav-item"><a href="usuarios-crear.php" class="side-nav-link">Alta usuario</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('roles', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-roles" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-roles">
                        <span class="menu-icon"><i data-lucide="key-round"></i></span>
                        <span class="menu-text">Roles</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-roles">
                        <ul class="side-nav sub-menu">
                            <?php if (has_permission('roles', 'create')) : ?>
                                <li class="side-nav-item"><a href="roles-editar.php" class="side-nav-link">Nuevo rol</a></li>
                            <?php endif; ?>
                            <li class="side-nav-item"><a href="roles-permisos.php" class="side-nav-link">Permisos</a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (has_permission('mantenedores', 'view')) : ?>
                <li class="side-nav-item">
                    <a href="#modulo-mantenedores" class="side-nav-link" data-bs-toggle="collapse" aria-expanded="false" aria-controls="modulo-mantenedores">
                        <span class="menu-icon"><i data-lucide="settings-2"></i></span>
                        <span class="menu-text">Ajustes</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="modulo-mantenedores">
                        <ul class="side-nav sub-menu">
                            <li class="side-nav-item"><a href="municipalidad.php" class="side-nav-link">Empresa</a></li>
                            <li class="side-nav-item"><a href="notificaciones-correo.php" class="side-nav-link">Correo</a></li>
                            <li class="side-nav-item"><a href="notificaciones-whatsapp.php" class="side-nav-link">WhatsApp</a></li>
                            <li class="side-nav-item"><a href="configuracion-avisos.php" class="side-nav-link">Plantillas avisos</a></li>
                            <li class="side-nav-item"><a href="configuracion-suspension-correo.php" class="side-nav-link">Plantilla suspensión</a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
