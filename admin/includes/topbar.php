<header class="admin-topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle-btn" type="button" id="sidebarToggle" aria-label="Open Menu">
            ☰
        </button>

        <div class="topbar-left-text">
            <h1><?php echo e($pageHeading ?? 'Dashboard'); ?></h1>
            <p><?php echo e($pageSubheading ?? 'Welcome to the admin panel.'); ?></p>
        </div>
    </div>

    <div class="topbar-right">
        <div class="admin-user-box">
            <span class="admin-user-name"><?php echo e($admin['full_name']); ?></span>
            <span class="admin-user-email"><?php echo e($admin['email']); ?></span>
        </div>

        <a class="logout-btn" href="<?php echo ADMIN_URL; ?>logout.php">Logout</a>
    </div>
</header>