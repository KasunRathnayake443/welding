<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    set_flash('error', 'Portfolio item not found.');
    redirect(ADMIN_URL . 'portfolio/index.php');
}

$pageTitle = 'Edit Portfolio Item - ' . APP_NAME;
$pageHeading = 'Edit Portfolio Item';
$pageSubheading = 'Update the selected portfolio item.';
$pageCssFiles = ['portfolio.css'];

$errors = [];

$title = $item['title'];
$slug = $item['slug'];
$shortDescription = $item['short_description'] ?? '';
$fullDescription = $item['full_description'] ?? '';
$isFeatured = (int)$item['is_featured'];
$status = (int)$item['status'];

if (is_post_request()) {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $fullDescription = trim($_POST['full_description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $status = isset($_POST['status']) ? 1 : 0;
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (mb_strlen($title) > 200) {
        $errors[] = 'Title must not exceed 200 characters.';
    }

    if ($slug === '') {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    if ($slug === '') {
        $errors[] = 'A valid slug could not be generated.';
    } elseif (mb_strlen($slug) > 220) {
        $errors[] = 'Slug must not exceed 220 characters.';
    } elseif (portfolio_slug_exists($pdo, $slug, $id)) {
        $errors[] = 'This slug already exists. Please use a different one.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE portfolio_items
            SET title = ?, slug = ?, short_description = ?, full_description = ?, is_featured = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $title,
            $slug,
            $shortDescription !== '' ? $shortDescription : null,
            $fullDescription !== '' ? $fullDescription : null,
            $isFeatured,
            $status,
            $id
        ]);

        set_flash('success', 'Portfolio item updated successfully.');
        redirect(ADMIN_URL . 'portfolio/index.php');
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
            <a href="<?php echo ADMIN_URL; ?>portfolio/index.php" class="btn btn-dark">← Back to Portfolio</a>
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
                <h3>Edit Portfolio Item</h3>
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label for="title">Title <span>*</span></label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        maxlength="200"
                        value="<?php echo e($title); ?>"
                        placeholder="Enter project title"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        maxlength="220"
                        value="<?php echo e($slug); ?>"
                        placeholder="Leave blank to auto-generate"
                    >
                </div>

                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <textarea id="short_description" name="short_description" placeholder="Short description"><?php echo e($shortDescription); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="full_description">Full Description</label>
                    <textarea id="full_description" name="full_description" placeholder="Full project description"><?php echo e($fullDescription); ?></textarea>
                </div>

                <div class="checkbox-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" <?php echo $isFeatured ? 'checked' : ''; ?>>
                        <span>Featured</span>
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="1" <?php echo $status ? 'checked' : ''; ?>>
                        <span>Active</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Update Portfolio Item</button>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>