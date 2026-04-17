<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Categories - ' . APP_NAME;
$pageHeading = 'Categories';
$pageSubheading = 'Manage product categories for tables, chairs, staircases, racks, decorative items, and more.';

$stmt = $pdo->query("SELECT c.*, 
    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
    FROM categories c
    ORDER BY c.created_at DESC");
$categories = $stmt->fetchAll();

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo e($successMessage); ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-error"><?php echo e($errorMessage); ?></div>
        <?php endif; ?>

        <div class="page-actions">
            <a href="<?php echo ADMIN_URL; ?>categories/create.php" class="btn btn-primary">Add New Category</a>
        </div>

        <section class="admin-card">
            <div class="admin-card-header">
                <h3>All Categories</h3>
            </div>

            <?php if (!empty($categories)): ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="70">ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th width="120">Products</th>
                                <th width="120">Status</th>
                                <th width="190">Created</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo e($category['id']); ?></td>
                                    <td><?php echo e($category['name']); ?></td>
                                    <td><?php echo e($category['slug']); ?></td>
                                    <td><?php echo e($category['description'] ?: '-'); ?></td>
                                    <td><?php echo e($category['product_count']); ?></td>
                                    <td>
                                        <?php if ((int)$category['status'] === 1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(date('M d, Y h:i A', strtotime($category['created_at']))); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a class="btn btn-secondary btn-sm" href="<?php echo ADMIN_URL; ?>categories/edit.php?id=<?php echo (int)$category['id']; ?>">Edit</a>
                                            <a class="btn btn-danger btn-sm" href="<?php echo ADMIN_URL; ?>categories/delete.php?id=<?php echo (int)$category['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No categories found yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>