<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT pim.*, pi.title AS portfolio_title
    FROM portfolio_images pim
    INNER JOIN portfolio_items pi ON pi.id = pim.portfolio_item_id
    WHERE pim.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$image = $stmt->fetch();

if (!$image) {
    set_flash('error', 'Portfolio image not found.');
    redirect(ADMIN_URL . 'portfolio-images/index.php');
}

$pageTitle = 'Edit Portfolio Image - ' . APP_NAME;
$pageHeading = 'Edit Portfolio Image';
$pageSubheading = 'Update image sort order.';
$pageCssFiles = ['portfolio-images.css'];

$errors = [];
$sortOrder = (int)$image['sort_order'];

if (is_post_request()) {
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE portfolio_images SET sort_order = ? WHERE id = ?");
        $stmt->execute([$sortOrder, $id]);

        set_flash('success', 'Portfolio image updated successfully.');
        redirect(ADMIN_URL . 'portfolio-images/index.php?portfolio_item_id=' . (int)$image['portfolio_item_id']);
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
            <a href="<?php echo ADMIN_URL; ?>portfolio-images/index.php?portfolio_item_id=<?php echo (int)$image['portfolio_item_id']; ?>" class="btn btn-dark">← Back to Portfolio Images</a>
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
                <img src="<?php echo UPLOADS_URL . 'portfolio/' . e($image['image_path']); ?>" alt="Portfolio Image">
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label>Portfolio Item</label>
                    <input type="text" value="<?php echo e($image['portfolio_title']); ?>" disabled>
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