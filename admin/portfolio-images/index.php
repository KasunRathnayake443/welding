<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Portfolio Images - ' . APP_NAME;
$pageHeading = 'Portfolio Images';
$pageSubheading = 'Manage images for portfolio items and control display order.';
$pageCssFiles = ['portfolio-images.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$portfolioItemId = isset($_GET['portfolio_item_id']) ? (int)$_GET['portfolio_item_id'] : 0;

$itemsStmt = $pdo->query("SELECT id, title FROM portfolio_items ORDER BY title ASC");
$items = $itemsStmt->fetchAll();

$portfolioItem = null;
$images = [];

if ($portfolioItemId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ? LIMIT 1");
    $stmt->execute([$portfolioItemId]);
    $portfolioItem = $stmt->fetch();

    if ($portfolioItem) {
        $stmt = $pdo->prepare("
            SELECT *
            FROM portfolio_images
            WHERE portfolio_item_id = ?
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([$portfolioItemId]);
        $images = $stmt->fetchAll();
    } else {
        set_flash('error', 'Selected portfolio item not found.');
        redirect(ADMIN_URL . 'portfolio-images/index.php');
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
                    <label for="portfolio_item_id">Select Portfolio Item</label>
                    <select name="portfolio_item_id" id="portfolio_item_id" onchange="this.form.submit()">
                        <option value="">Choose Portfolio Item</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?php echo (int)$item['id']; ?>" <?php echo $portfolioItemId === (int)$item['id'] ? 'selected' : ''; ?>>
                                <?php echo e($item['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <?php if ($portfolioItem): ?>
                <a href="<?php echo ADMIN_URL; ?>portfolio-images/upload.php?portfolio_item_id=<?php echo (int)$portfolioItem['id']; ?>" class="btn btn-primary">Upload Image</a>
            <?php endif; ?>
        </div>

        <section class="content-card">
            <div class="content-card-header">
                <h3><?php echo $portfolioItem ? 'Images for: ' . e($portfolioItem['title']) : 'Portfolio Images'; ?></h3>
            </div>

            <?php if (!$portfolioItem): ?>
                <div class="empty-state">
                    <p>Select a portfolio item to manage its images.</p>
                </div>
            <?php elseif (empty($images)): ?>
                <div class="empty-state">
                    <p>No images uploaded for this portfolio item yet.</p>
                </div>
            <?php else: ?>
                <div class="image-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-card">
                            <div class="image-preview">
                                <img src="<?php echo UPLOADS_URL . 'portfolio/' . e($image['image_path']); ?>" alt="Portfolio Image">
                            </div>

                            <div class="image-card-body">
                                <div class="image-meta">
                                    <span><strong>ID:</strong> <?php echo e($image['id']); ?></span>
                                    <span><strong>Sort Order:</strong> <?php echo e($image['sort_order']); ?></span>
                                </div>

                                <div class="action-group">
                                    <a href="<?php echo ADMIN_URL; ?>portfolio-images/edit.php?id=<?php echo (int)$image['id']; ?>" class="btn btn-dark btn-sm">Edit</a>
                                    <a href="<?php echo ADMIN_URL; ?>portfolio-images/delete.php?id=<?php echo (int)$image['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
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