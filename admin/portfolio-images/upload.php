<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Upload Portfolio Image - ' . APP_NAME;
$pageHeading = 'Upload Portfolio Image';
$pageSubheading = 'Upload an image for a selected portfolio item.';
$pageCssFiles = ['portfolio-images.css'];

$errors = [];

$portfolioItemId = isset($_GET['portfolio_item_id']) ? (int)$_GET['portfolio_item_id'] : (int)($_POST['portfolio_item_id'] ?? 0);
$sortOrder = 0;

$itemsStmt = $pdo->query("SELECT id, title FROM portfolio_items ORDER BY title ASC");
$items = $itemsStmt->fetchAll();

if (is_post_request()) {
    $portfolioItemId = (int)($_POST['portfolio_item_id'] ?? 0);
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($portfolioItemId <= 0) {
        $errors[] = 'Please select a portfolio item.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM portfolio_items WHERE id = ? LIMIT 1");
        $stmt->execute([$portfolioItemId]);
        if (!$stmt->fetch()) {
            $errors[] = 'Selected portfolio item does not exist.';
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
            ensure_directory_exists(PORTFOLIO_UPLOADS_PATH);

            $newFileName = generate_portfolio_upload_file_name($_FILES['image']['name']);
            $targetPath = PORTFOLIO_UPLOADS_PATH . $newFileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                throw new Exception('Failed to move uploaded file.');
            }

            $stmt = $pdo->prepare("
                INSERT INTO portfolio_images (portfolio_item_id, image_path, sort_order)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$portfolioItemId, $newFileName, $sortOrder]);

            set_flash('success', 'Portfolio image uploaded successfully.');
            redirect(ADMIN_URL . 'portfolio-images/index.php?portfolio_item_id=' . $portfolioItemId);
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
            <a href="<?php echo ADMIN_URL; ?>portfolio-images/index.php<?php echo $portfolioItemId > 0 ? '?portfolio_item_id=' . $portfolioItemId : ''; ?>" class="btn btn-dark">← Back to Portfolio Images</a>
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
                    <label for="portfolio_item_id">Portfolio Item <span>*</span></label>
                    <select name="portfolio_item_id" id="portfolio_item_id" required>
                        <option value="">Select Portfolio Item</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?php echo (int)$item['id']; ?>" <?php echo $portfolioItemId === (int)$item['id'] ? 'selected' : ''; ?>>
                                <?php echo e($item['title']); ?>
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