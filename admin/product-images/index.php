<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Product Images - ' . APP_NAME;
$pageHeading = 'Product Images';
$pageSubheading = 'Manage product images and display order.';
$pageCssFiles = ['product-images.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

$productsStmt = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
$products = $productsStmt->fetchAll();

$product = null;
$images = [];

if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $stmt = $pdo->prepare("
            SELECT *
            FROM product_images
            WHERE product_id = ?
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([$productId]);
        $images = $stmt->fetchAll();
    } else {
        set_flash('error', 'Selected product not found.');
        redirect(ADMIN_URL . 'product-images/index.php');
    }
}

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
                    <label for="product_id">Select Product</label>
                    <select name="product_id" id="product_id" onchange="this.form.submit()">
                        <option value="">Choose Product</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?php echo (int)$p['id']; ?>" <?php echo $productId === (int)$p['id'] ? 'selected' : ''; ?>>
                                <?php echo e($p['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <?php if ($product): ?>
                <a href="<?php echo ADMIN_URL; ?>product-images/upload.php?product_id=<?php echo (int)$product['id']; ?>" class="btn btn-primary">Upload Image</a>
            <?php endif; ?>
        </div>

        <section class="content-card">
            <div class="content-card-header">
                <h3><?php echo $product ? 'Images for: ' . e($product['name']) : 'Product Images'; ?></h3>
            </div>

            <?php if (!$product): ?>
                <div class="empty-state">
                    <p>Select a product to manage its images.</p>
                </div>
            <?php elseif (empty($images)): ?>
                <div class="empty-state">
                    <p>No images uploaded for this product yet.</p>
                </div>
            <?php else: ?>
                <div class="image-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-card">
                            <div class="image-preview">
                                <img src="<?php echo UPLOADS_URL . 'products/' . e($image['image_path']); ?>" alt="Product Image">
                            </div>

                            <div class="image-card-body">
                                <div class="image-meta">
                                    <span><strong>ID:</strong> <?php echo e($image['id']); ?></span>
                                    <span><strong>Sort Order:</strong> <?php echo e($image['sort_order']); ?></span>
                                </div>

                                <div class="action-group">
                                    <a href="<?php echo ADMIN_URL; ?>product-images/edit.php?id=<?php echo (int)$image['id']; ?>" class="btn btn-dark btn-sm">Edit</a>
                                    <a href="<?php echo ADMIN_URL; ?>product-images/delete.php?id=<?php echo (int)$image['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>