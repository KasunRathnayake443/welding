<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Upload Product Image - ' . APP_NAME;
$pageHeading = 'Upload Product Image';
$pageSubheading = 'Upload an image for a selected product.';
$pageCssFiles = ['product-images.css'];

$errors = [];

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : (int)($_POST['product_id'] ?? 0);
$sortOrder = 0;

$productsStmt = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
$products = $productsStmt->fetchAll();

if (is_post_request()) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($productId <= 0) {
        $errors[] = 'Please select a product.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            $errors[] = 'Selected product does not exist.';
        }
    }

    if (!isset($_FILES['image'])) {
        $errors[] = 'Please choose an image.';
    } else {
        $imageErrors = is_valid_uploaded_image($_FILES['image']);
        $errors = array_merge($errors, $imageErrors);
    }

    if (empty($errors)) {
        try {
            ensure_directory_exists(PRODUCT_UPLOADS_PATH);

            $newFileName = generate_upload_file_name($_FILES['image']['name']);
            $targetPath = PRODUCT_UPLOADS_PATH . $newFileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                throw new Exception('Failed to move uploaded file.');
            }

            $stmt = $pdo->prepare("
                INSERT INTO product_images (product_id, image_path, sort_order)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$productId, $newFileName, $sortOrder]);

            set_flash('success', 'Product image uploaded successfully.');
            redirect(ADMIN_URL . 'product-images/index.php?product_id=' . $productId);
        } catch (Throwable $e) {
            if (isset($targetPath)) {
                delete_file_if_exists($targetPath);
            }
            $errors[] = 'Failed to upload image. Please try again.';
        }
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
            <a href="<?php echo ADMIN_URL; ?>product-images/index.php<?php echo $productId > 0 ? '?product_id=' . $productId : ''; ?>" class="btn btn-dark">← Back to Product Images</a>
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
                <h3>Upload Image</h3>
            </div>

            <form method="POST" action="" enctype="multipart/form-data" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label for="product_id">Product <span>*</span></label>
                    <select name="product_id" id="product_id" required>
                        <option value="">Select Product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo (int)$product['id']; ?>" <?php echo $productId === (int)$product['id'] ? 'selected' : ''; ?>>
                                <?php echo e($product['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Image <span>*</span></label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                    <div class="form-help">Allowed: JPG, JPEG, PNG, WEBP. Max: 5MB.</div>
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo e((string)$sortOrder); ?>" placeholder="0">
                </div>

                <button type="submit" class="btn btn-primary">Upload Image</button>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>