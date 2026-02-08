<?php
require_once __DIR__ . '/../app/bootstrap.php';
$municipalidad = get_municipalidad();
$primaryColor = $municipalidad['color_primary'] ?? '#6658dd';
$secondaryColor = $municipalidad['color_secondary'] ?? '#4a81d4';
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$logoTopbarHeight = $municipalidad['logo_topbar_height'] ?? 56;
$logoSidenavHeight = $municipalidad['logo_sidenav_height'] ?? 48;
$logoSidenavHeightSm = $municipalidad['logo_sidenav_height_sm'] ?? 36;
$primaryRgb = hex_to_rgb($primaryColor) ?? [102, 88, 221];
$secondaryRgb = hex_to_rgb($secondaryColor) ?? [74, 129, 212];
?>

<!-- Theme Config Js -->
<script src="assets/js/config.js"></script>

<!-- Vendor css -->
<link href="assets/css/vendors.min.css" rel="stylesheet" type="text/css">

<!-- App css -->
<link href="assets/css/app.min.css" rel="stylesheet" type="text/css">
<link href="assets/css/custom.css" rel="stylesheet" type="text/css">

<!-- Favicon -->
<link rel="icon" href="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>" type="image/png">
<link rel="apple-touch-icon" href="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="theme-color" content="<?php echo htmlspecialchars($primaryColor, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="apple-mobile-web-app-capable" content="yes">

<style>
    :root {
        --ins-primary: <?php echo htmlspecialchars($primaryColor, ENT_QUOTES, 'UTF-8'); ?>;
        --ins-primary-rgb: <?php echo (int) $primaryRgb[0]; ?>, <?php echo (int) $primaryRgb[1]; ?>, <?php echo (int) $primaryRgb[2]; ?>;
        --ins-secondary: <?php echo htmlspecialchars($secondaryColor, ENT_QUOTES, 'UTF-8'); ?>;
        --ins-secondary-rgb: <?php echo (int) $secondaryRgb[0]; ?>, <?php echo (int) $secondaryRgb[1]; ?>, <?php echo (int) $secondaryRgb[2]; ?>;
        --bs-primary: <?php echo htmlspecialchars($primaryColor, ENT_QUOTES, 'UTF-8'); ?>;
        --bs-primary-rgb: <?php echo (int) $primaryRgb[0]; ?>, <?php echo (int) $primaryRgb[1]; ?>, <?php echo (int) $primaryRgb[2]; ?>;
        --bs-secondary: <?php echo htmlspecialchars($secondaryColor, ENT_QUOTES, 'UTF-8'); ?>;
        --bs-secondary-rgb: <?php echo (int) $secondaryRgb[0]; ?>, <?php echo (int) $secondaryRgb[1]; ?>, <?php echo (int) $secondaryRgb[2]; ?>;
        --ins-topbar-logo-height: <?php echo (int) $logoTopbarHeight; ?>px;
        --ins-logo-lg-height: <?php echo (int) $logoSidenavHeight; ?>px;
        --ins-logo-sm-height: <?php echo (int) $logoSidenavHeightSm; ?>px;
    }

    .side-nav-title {
        font-size: 0.7rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(var(--ins-body-color-rgb), 0.6);
    }

    .side-nav .side-nav-item .side-nav-link {
        border-radius: 0.5rem;
        padding: 0.55rem 0.85rem;
        margin: 0.15rem 0.5rem;
    }

    .side-nav .side-nav-item .side-nav-link:hover {
        background-color: rgba(var(--ins-primary-rgb), 0.08);
    }

    .side-nav .side-nav-item.active > .side-nav-link {
        background-color: rgba(var(--ins-primary-rgb), 0.15);
    }

    .app-topbar .logo-topbar a {
        display: flex;
        align-items: center;
    }

    .app-topbar .logo-topbar img {
        max-width: 100%;
        height: auto;
        max-height: var(--ins-topbar-logo-height);
        object-fit: contain;
    }

    @media (max-width: 768px) {
        .app-topbar .topbar-menu {
            flex-wrap: wrap;
            row-gap: 0.35rem;
            padding: 0.5rem 0.75rem;
        }

        .app-topbar .topbar-menu > .d-flex:first-child,
        .app-topbar .topbar-menu > .d-flex:last-child {
            flex: 1 1 100%;
        }

        .app-topbar .topbar-menu > .d-flex:last-child {
            justify-content: space-between;
        }

        .app-topbar .logo-topbar {
            max-width: calc(100vw - 120px);
        }

        .app-topbar .topnav-toggle-button {
            margin-left: auto;
        }
    }

    @media (max-width: 576px) {
        .app-topbar .topbar-menu {
            flex-wrap: nowrap;
            align-items: center;
        }

        .app-topbar .topbar-menu > .d-flex:last-child {
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.25rem;
            flex: 0 0 auto;
        }

        .app-topbar .topbar-item .topbar-link {
            padding: 0.25rem;
        }

        .app-topbar .topbar-item .fs-xxl {
            font-size: 1.1rem;
        }

        .app-topbar [data-toggle="fullscreen"],
        .app-topbar #monochrome-mode,
        .app-topbar .topbar-item:not(.nav-user) {
            display: none;
        }

        .app-topbar .logo-topbar {
            max-width: calc(100vw - 170px);
        }

        .app-topbar .logo-topbar img {
            max-height: 32px;
        }

        .app-topbar .nav-user img {
            width: 28px;
            height: 28px;
        }
    }

    .wrapper .form-label {
        font-size: 0.78rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .wrapper .form-control,
    .wrapper .form-select {
        min-height: 32px;
        padding: 0.35rem 0.6rem;
        font-size: 0.85rem;
        line-height: 1.2;
        border-radius: 0.6rem;
        border-color: #e2e8f0;
        background-color: #ffffff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .wrapper textarea.form-control {
        min-height: 72px;
    }

    .wrapper .form-text {
        font-size: 0.74rem;
        color: #94a3b8;
    }

    .wrapper .mb-3 {
        margin-bottom: 0.6rem !important;
    }

    .wrapper .row {
        --bs-gutter-y: 0.6rem;
    }

    .wrapper .table {
        --bs-table-bg: transparent;
        border-color: #e2e8f0;
        font-size: 0.85rem;
    }

    .wrapper .table > :not(caption) > * > * {
        padding: 0.6rem 0.75rem;
        vertical-align: middle;
    }

    .wrapper .table thead th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        background-color: rgba(var(--ins-primary-rgb), 0.08);
        border-bottom: 1px solid rgba(var(--ins-primary-rgb), 0.12);
    }

    .wrapper .table tbody tr:hover {
        background-color: rgba(var(--ins-primary-rgb), 0.05);
    }

    .wrapper .table-responsive {
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        overflow: hidden;
        background-color: #ffffff;
    }

    @media (max-width: 992px) {
        .wrapper .table-responsive {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .wrapper .table {
            white-space: nowrap;
        }

        .wrapper .list-group {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .wrapper .list-group-item {
            white-space: nowrap;
        }
    }

    .wrapper .list-group-item {
        border-color: #e2e8f0;
        padding: 0.7rem 0.9rem;
    }

    .wrapper .list-group-item + .list-group-item {
        border-top: 1px solid #e2e8f0;
    }

    .wrapper .card {
        border-radius: 0.85rem;
        border-color: #e2e8f0;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .wrapper .gm-section {
        border-radius: 0.85rem;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .wrapper .gm-section + .gm-section {
        margin-top: 1rem;
    }

    .wrapper .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }

    .wrapper .card-header .card-title {
        font-size: 1rem;
    }

    .wrapper .form-control:focus,
    .wrapper .form-select:focus {
        border-color: rgba(var(--ins-primary-rgb), 0.45);
        box-shadow: 0 0 0 0.2rem rgba(var(--ins-primary-rgb), 0.15);
    }

    .wrapper .badge {
        border-radius: 999px;
    }

    .app-topbar .app-search {
        position: relative;
    }

    .app-topbar .topbar-search {
        padding-left: 2.6rem;
    }

    .app-topbar .app-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
</style>
