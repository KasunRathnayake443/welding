<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Category Properties - ' . APP_NAME;
$pageHeading = 'Category Properties';
$pageSubheading = 'Manage reusable properties for each product category.';
$pageCssFiles = ['category-properties.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$selectedCategoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

$categoriesStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll();

$sql = "
    SELECT cp.*, c.name AS category_name,
           (
               SELECT COUNT(*)
               FROM product_property_values ppv
               WHERE ppv.category_property_id = cp.id
           ) AS usage_count
    FROM category_properties cp
    INNER JOIN categories c ON c.id = cp.category_id
";

$params = [];

if ($selectedCategoryId > 0) {
    $sql .= " WHERE cp.category_id = ? ";
    $params[] = $selectedCategoryId;
}

$sql .= " ORDER BY c.name ASC, cp.sort_order ASC, cp.id DESC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

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

        <div class="page-toolbar">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label for="category_id">Filter by Category</label>
                    <select name="category_id" id="category_id" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int)$category['id']; ?>" <?php echo $selectedCategoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                                <?php echo e($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <a href="<?php echo ADMIN_URL; ?>category-properties/create.php" class="btn btn-primary">Add Property</a>
        </div>

        <section class="content-card">
            <div class="content-card-header">
                <h3>All Category Properties</h3>
            </div>

            <?php if (!empty($properties)): ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="70">ID</th>
                                <th width="170">Category</th>
                                <th width="180">Property Name</th>
                                <th width="120">Field Type</th>
                                <th>Placeholder</th>
                                <th width="100">Sort Order</th>
                                <th width="100">Used In</th>
                                <th width="100">Status</th>
                                <th width="180">Created</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td><?php echo e($property['id']); ?></td>
                                    <td><?php echo e($property['category_name']); ?></td>
                                    <td><?php echo e($property['property_name']); ?></td>
                                    <td><?php echo e(ucfirst($property['field_type'])); ?></td>
                                    <td><?php echo e($property['placeholder'] ?: '-'); ?></td>
                                    <td><?php echo e($property['sort_order']); ?></td>
                                    <td><?php echo e($property['usage_count']); ?></td>
                                    <td>
                                        <?php if ((int)$property['status'] === 1): ?>
                                            <span class="status-badge active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(format_datetime($property['created_at'])); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="<?php echo ADMIN_URL; ?>category-properties/edit.php?id=<?php echo (int)$property['id']; ?>" class="btn btn-dark btn-sm">
                                                Edit
                                            </a>

                                            <a href="<?php echo ADMIN_URL; ?>category-properties/delete.php?id=<?php echo (int)$property['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this property?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No category properties found yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>