<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    set_flash('error', 'Category not found.');
    redirect(ADMIN_URL . 'categories/index.php');
}

$pageTitle = 'Edit Category - ' . APP_NAME;
$pageHeading = 'Edit Category';
$pageSubheading = 'Update the selected category.';
$pageCssFiles = ['categories.css'];

$errors = [];
$name = $category['name'];
$slug = $category['slug'];
$description = $category['description'] ?? '';
$status = (int) $category['status'];

if (is_post_request()) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($name === '') {
        $errors[] = 'Category name is required.';
    } elseif (mb_strlen($name) > 150) {
        $errors[] = 'Category name must not exceed 150 characters.';
    }

    if ($slug === '') {
        $slug = slugify($name);
    } else {
        $slug = slugify($slug);
    }

    if ($slug === '') {
        $errors[] = 'A valid slug could not be generated.';
    } elseif (mb_strlen($slug) > 180) {
        $errors[] = 'Slug must not exceed 180 characters.';
    }

    if ($description !== '' && mb_strlen($description) > 65535) {
        $errors[] = 'Description is too long.';
    }

    if (empty($errors) && category_slug_exists($pdo, $slug, $id)) {
        $errors[] = 'This slug already exists. Please use a different one.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE categories
            SET name = ?, slug = ?, description = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $name,
            $slug,
            $description !== '' ? $description : null,
            $status,
            $id
        ]);

        set_flash('success', 'Category updated successfully.');
        redirect(ADMIN_URL . 'categories/index.php');
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
            <a href="<?php echo ADMIN_URL; ?>categories/index.php" class="btn btn-dark">← Back to Categories</a>
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
                <h3>Edit Category</h3>
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label for="name">Category Name <span>*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        maxlength="150"
                        value="<?php echo e($name); ?>"
                        placeholder="Enter category name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        maxlength="180"
                        value="<?php echo e($slug); ?>"
                        placeholder="Leave blank to auto-generate"
                    >
                    <div class="form-help">Used in URLs. Only letters, numbers, and hyphens will be kept.</div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        placeholder="Enter a short category description"><?php echo e($description); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="1" <?php echo $status ? 'checked' : ''; ?>>
                        <span>Active</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>