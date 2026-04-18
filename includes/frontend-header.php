<?php
if (!isset($pageTitle)) {
    $pageTitle = $siteSettings['site_name'] ?? 'Welding Company';
}

$frontendCssFiles = $frontendCssFiles ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>

    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/frontend/shared.css">

    <?php foreach ($frontendCssFiles as $cssFile): ?>
        <link rel="stylesheet" href="<?php echo ASSETS_URL . 'css/frontend/' . e($cssFile); ?>">
    <?php endforeach; ?>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?php echo BASE_URL; ?>" class="site-logo">
            <?php echo e($siteSettings['site_name'] ?: 'Welding Company'); ?>
        </a>

        <button class="mobile-nav-toggle" id="mobileNavToggle" type="button" aria-label="Open menu">
            ☰
        </button>

        <nav class="site-nav" id="siteNav">
            <a href="<?php echo BASE_URL; ?>">Home</a>
            <a href="<?php echo BASE_URL; ?>products.php">Products</a>
            <a href="<?php echo BASE_URL; ?>portfolio.php">Portfolio</a>
            <a href="<?php echo BASE_URL; ?>about.php">About</a>
            <a href="<?php echo BASE_URL; ?>contact.php" class="nav-cta">Contact</a>
        </nav>
    </div>
</header>