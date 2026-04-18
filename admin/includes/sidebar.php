<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <h2>Welding Admin</h2>
        <p>Management Panel</p>
    </div>

    <nav class="sidebar-nav">
        <a class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>dashboard.php">
            Dashboard
        </a>

        <a class="<?php echo $currentDir === 'categories' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>categories/index.php">
            Categories
        </a>

        <a class="<?php echo $currentDir === 'category-properties' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>category-properties/index.php">
            Category Properties
        </a>

        <a class="<?php echo $currentDir === 'products' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>products/index.php">
            Products
        </a>

        <a class="<?php echo $currentDir === 'product-images' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>product-images/index.php">
            Product Images
        </a>

        <a class="<?php echo $currentDir === 'portfolio' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>portfolio/index.php">
            Portfolio
        </a>

        <a class="<?php echo $currentDir === 'inquiries' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>inquiries/index.php">
            Inquiries
        </a>

        <a class="<?php echo $currentDir === 'settings' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>settings/index.php">
            Settings
        </a>
    </nav>
</aside>