<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT pi.*, p.name AS product_name
    FROM product_images pi
    INNER JOIN products p ON p.id = pi.product_id
    WHERE pi.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$image = $stmt->fetch();

if (!$image) {
    set_flash('error', 'Product image not found.');
    redirect(ADMIN_URL . 'product-images/index.php');
}

$pageTitle = 'Edit Product Image - ' . APP_NAME;
$pageHeading = 'Edit Product Image';
$pageSubheading = 'Update image sort order.';
$pageCssFiles = ['product-images.css'];

$errors = [];
$sortOrder = (int)$image['sort_order'];

if (is_post_request()) {
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE product_images SET sort_order = ? WHERE id = ?");
        $stmt->execute([$sortOrder, $id]);

        set_flash('success', 'Product image updated successfully.');
        redirect(ADMIN_URL . 'product-images/index.php?product_id=' . (int)$image['product_id']);
    }
}

$csrfToken = generate_csrf_token();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

        <div class="page-actions page-actions-left">
            <a href="<?php echo ADMIN_URL; ?>product-images/index.php?product_id=<?php echo (int)$image['product_id']; ?>" class="btn btn-dark">← Back to Product Images</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul class="alert-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="content-card form-card">
            <div class="content-card-header">
                <h3>Edit Image</h3>
            </div>

            <div class="single-image-preview">
                <img src="<?php echo UPLOADS_URL . 'products/' . e($image['image_path']); ?>" alt="Product Image">
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label>Product</label>
                    <input type="text" value="<?php echo e($image['product_name']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo e((string)$sortOrder); ?>" placeholder="0">
                </div>

                <button type="submit" class="btn btn-primary">Update Image</button>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>