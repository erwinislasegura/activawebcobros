<?php
$municipalidadName = $municipalidad['nombre'] ?? 'Go Muni';
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$pageTitle = isset($title) ? $title : 'Go Muni';
$metaDescription = 'Go Muni · tecnologia escalable para la gestión de eventos, autoridades y validaciones ciudadanas.';
$metaKeywords = 'municipalidad, gestión municipal, eventos municipales, autoridades, validación ciudadana, administración pública, gobierno local';
?>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($municipalidadName, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="author" content="<?php echo htmlspecialchars($municipalidadName, ENT_QUOTES, 'UTF-8'); ?>">

<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($municipalidadName, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($municipalidadName, ENT_QUOTES, 'UTF-8'); ?>">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($municipalidadName, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>">

<!-- App favicon -->
<link rel="shortcut icon" href="<?php echo htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="manifest" href="manifest.webmanifest">
