<?php
if (!isset($pageTitle)) {
    $pageTitle = $siteSettings['site_name'] ?? 'Welding Company';
}

$frontendCssFiles = $frontendCssFiles ?? [];
$currentPage = basename($_SERVER['PHP_SELF']);

$isHome = $currentPage === 'index.php';
$isProducts = in_array($currentPage, ['products.php', 'product.php'], true);
$isPortfolio = in_array($currentPage, ['portfolio.php', 'portfolio-item.php'], true);
$isAbout = $currentPage === 'about.php';
$isContact = $currentPage === 'contact.php';
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
            <a href="<?php echo BASE_URL; ?>" class="<?php echo $isHome ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo BASE_URL; ?>products.php" class="<?php echo $isProducts ? 'active' : ''; ?>">Products</a>
            <a href="<?php echo BASE_URL; ?>portfolio.php" class="<?php echo $isPortfolio ? 'active' : ''; ?>">Portfolio</a>
            <a href="<?php echo BASE_URL; ?>about.php" class="<?php echo $isAbout ? 'active' : ''; ?>">About</a>
            <a href="<?php echo BASE_URL; ?>contact.php" class="nav-cta <?php echo $isContact ? 'active-cta' : ''; ?>">Contact</a>
        </nav>
    </div>
</header>