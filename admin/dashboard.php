<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard - ' . APP_NAME;
$pageHeading = 'Dashboard';
$pageSubheading = 'Manage categories, products, portfolio items, inquiries, and website settings.';
$pageCssFiles = ['dashboard.css'];

$successMessage = get_flash('success');

$totalCategories = 0;
$totalProducts = 0;
$totalPortfolioItems = 0;
$totalInquiries = 0;
$newInquiries = 0;

try {
    $totalCategories = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
} catch (Throwable $e) {
    $totalCategories = 0;
}

try {
    $totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
} catch (Throwable $e) {
    $totalProducts = 0;
}

try {
    $totalPortfolioItems = (int)$pdo->query("SELECT COUNT(*) FROM portfolio_items")->fetchColumn();
} catch (Throwable $e) {
    $totalPortfolioItems = 0;
}

try {
    $totalInquiries = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
} catch (Throwable $e) {
    $totalInquiries = 0;
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inquiries WHERE status = ?");
    $stmt->execute(['new']);
    $newInquiries = (int)$stmt->fetchColumn();
} catch (Throwable $e) {
    $newInquiries = 0;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/includes/topbar.php'; ?>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo e($successMessage); ?></div>
        <?php endif; ?>

        <section class="stats-grid">
            <div class="stat-card">
                <span class="stat-title">Categories</span>
                <strong class="stat-value"><?php echo e($totalCategories); ?></strong>
                <p class="stat-text">Total product categories in the system.</p>
            </div>

            <div class="stat-card">
                <span class="stat-title">Products</span>
                <strong class="stat-value"><?php echo e($totalProducts); ?></strong>
                <p class="stat-text">Total products currently added.</p>
            </div>

            <div class="stat-card">
                <span class="stat-title">Portfolio Items</span>
                <strong class="stat-value"><?php echo e($totalPortfolioItems); ?></strong>
                <p class="stat-text">Showcased completed work entries.</p>
            </div>

            <div class="stat-card">
                <span class="stat-title">Inquiries</span>
                <strong class="stat-value"><?php echo e($totalInquiries); ?></strong>
                <p class="stat-text">All customer inquiries received.</p>
            </div>
        </section>

        <section class="dashboard-row">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Quick Actions</h3>
                </div>

                <div class="quick-actions-grid">
                    <a href="<?php echo ADMIN_URL; ?>categories/index.php" class="quick-action-card">
                        <h4>Manage Categories</h4>
                        <p>Create and organize product categories.</p>
                    </a>

                    <a href="<?php echo ADMIN_URL; ?>category-properties/index.php" class="quick-action-card">
                        <h4>Manage Category Properties</h4>
                        <p>Define reusable properties for each category.</p>
                    </a>

                    <a href="<?php echo ADMIN_URL; ?>products/index.php" class="quick-action-card">
                        <h4>Manage Products</h4>
                        <p>Add products and assign specifications.</p>
                    </a>

                    <a href="<?php echo ADMIN_URL; ?>portfolio/index.php" class="quick-action-card">
                        <h4>Manage Portfolio</h4>
                        <p>Showcase previous metal work projects.</p>
                    </a>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3>Admin Overview</h3>
                </div>

                <div class="info-list">
                    <div class="info-row">
                        <span>Logged in as</span>
                        <strong><?php echo e($admin['full_name']); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Email</span>
                        <strong><?php echo e($admin['email']); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>New inquiries</span>
                        <strong><?php echo e($newInquiries); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Account created</span>
                        <strong><?php echo e(format_datetime($admin['created_at'])); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Session timeout</span>
                        <strong>2 hours</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-card full-width-card">
            <div class="dashboard-card-header">
                <h3>System Modules</h3>
            </div>

            <div class="module-grid">
                <a href="<?php echo ADMIN_URL; ?>categories/index.php" class="module-card">
                    <h4>Categories</h4>
                    <p>Manage the main product categories such as tables, chairs, staircases, racks, and other metal works.</p>
                </a>

                <a href="<?php echo ADMIN_URL; ?>category-properties/index.php" class="module-card">
                    <h4>Category Properties</h4>
                    <p>Attach reusable fields to categories so each product can inherit relevant specifications.</p>
                </a>

                <a href="<?php echo ADMIN_URL; ?>products/index.php" class="module-card">
                    <h4>Products</h4>
                    <p>Add products with category-based properties, extra custom properties, colors, and product details.</p>
                </a>

                <a href="<?php echo ADMIN_URL; ?>portfolio/index.php" class="module-card">
                    <h4>Portfolio</h4>
                    <p>Manage completed works and previous custom fabrication projects to build trust with customers.</p>
                </a>

                <a href="<?php echo ADMIN_URL; ?>inquiries/index.php" class="module-card">
                    <h4>Inquiries</h4>
                    <p>See customer inquiries, quote requests, and messages from product or contact forms.</p>
                </a>

                <a href="<?php echo ADMIN_URL; ?>settings/index.php" class="module-card">
                    <h4>Site Settings</h4>
                    <p>Manage business name, phone, WhatsApp, email, address, and homepage hero text.</p>
                </a>
            </div>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>