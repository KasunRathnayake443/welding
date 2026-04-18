<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Products - ' . APP_NAME;
$pageHeading = 'Products';
$pageSubheading = 'Manage products, category-based properties, and custom specifications.';
$pageCssFiles = ['products.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$selectedCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$search = trim($_GET['search'] ?? '');

$categories = get_active_categories($pdo);

$sql = "
    SELECT p.*, c.name AS category_name,
           (
               SELECT COUNT(*)
               FROM product_property_values ppv
               WHERE ppv.product_id = p.id
           ) AS property_count,
           (
               SELECT COUNT(*)
               FROM product_extra_properties pep
               WHERE pep.product_id = p.id
           ) AS extra_property_count
    FROM products p
    INNER JOIN categories c ON c.id = p.category_id
    WHERE 1 = 1
";

$params = [];

if ($selectedCategoryId > 0) {
    $sql .= " AND p.category_id = ? ";
    $params[] = $selectedCategoryId;
}

if ($search !== '') {
    $sql .= " AND (p.name LIKE ? OR p.slug LIKE ? OR p.short_description LIKE ?) ";
    $searchLike = '%' . $search . '%';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
}

$sql .= " ORDER BY p.id DESC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

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
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int)$category['id']; ?>" <?php echo $selectedCategoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                                <?php echo e($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search">Search</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="<?php echo e($search); ?>"
                        placeholder="Search products"
                    >
                </div>

                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="<?php echo ADMIN_URL; ?>products/index.php" class="btn btn-light">Reset</a>
            </form>

            <a href="<?php echo ADMIN_URL; ?>products/create.php" class="btn btn-primary">Add Product</a>
        </div>

        <section class="content-card">
            <div class="content-card-header">
                <h3>All Products</h3>
            </div>

            <?php if (!empty($products)): ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="70">ID</th>
                                <th width="180">Name</th>
                                <th width="150">Category</th>
                                <th width="160">Slug</th>
                                <th width="120">Price Text</th>
                                <th width="100">Colors</th>
                                <th width="90">Custom</th>
                                <th width="90">Featured</th>
                                <th width="100">Status</th>
                                <th width="90">Props</th>
                                <th width="90">Extra</th>
                                <th width="180">Created</th>
                                <th width="190">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo e($product['id']); ?></td>
                                    <td><?php echo e($product['name']); ?></td>
                                    <td><?php echo e($product['category_name']); ?></td>
                                    <td><?php echo e($product['slug']); ?></td>
                                    <td><?php echo e($product['price_text'] ?: '-'); ?></td>
                                    <td><?php echo e($product['available_colors'] ? 'Yes' : 'No'); ?></td>
                                    <td><?php echo (int)$product['is_customizable'] === 1 ? 'Yes' : 'No'; ?></td>
                                    <td><?php echo (int)$product['is_featured'] === 1 ? 'Yes' : 'No'; ?></td>
                                    <td>
                                        <?php if ((int)$product['status'] === 1): ?>
                                            <span class="status-badge active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($product['property_count']); ?></td>
                                    <td><?php echo e($product['extra_property_count']); ?></td>
                                    <td><?php echo e(format_datetime($product['created_at'])); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="<?php echo ADMIN_URL; ?>products/edit.php?id=<?php echo (int)$product['id']; ?>" class="btn btn-dark btn-sm">Edit</a>
                                            <a href="<?php echo ADMIN_URL; ?>products/delete.php?id=<?php echo (int)$product['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No products found yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>