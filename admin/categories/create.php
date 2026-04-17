<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Add Category - ' . APP_NAME;
$pageHeading = 'Add Category';
$pageSubheading = 'Create a new product category and define how it appears in the admin panel and website.';

$errors = [];
$name = '';
$slug = '';
$description = '';
$status = 1;

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
        $errors[] = 'A valid slug could not be generated. Please enter a different category name.';
    }

    if ($description !== '' && mb_strlen($description) > 5000) {
        $errors[] = 'Description is too long.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $existing = $stmt->fetch();

        if ($existing) {
            $errors[] = 'This slug already exists. Please use a different name or slug.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $description ?: null, $status]);

        set_flash('success', 'Category created successfully.');
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

        <a href="<?php echo ADMIN_URL; ?>categories/index.php" class="back-link">← Back to Categories</a>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul class="alert-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="admin-card form-card">
            <div class="admin-card-header">
                <h3>Category Information</h3>
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label for="name">Category Name <span>*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo e($name); ?>"
                        maxlength="150"
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
                        value="<?php echo e($slug); ?>"
                        maxlength="180"
                        placeholder="Leave blank to auto-generate from name"
                    >
                    <div class="form-help">Used in URLs and internal references. Only letters, numbers, and hyphens will be kept.</div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        maxlength="5000"
                        placeholder="Enter a short description for this category"><?php echo e($description); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div class="status-row">
                        <input type="checkbox" id="status" name="status" value="1" <?php echo $status ? 'checked' : ''; ?>>
                        <label for="status" style="margin-bottom:0;">Active</label>
                    </div>
                    <div class="form-help">Inactive categories will remain in the admin panel but can be hidden from the website later.</div>
                </div>

                <button type="submit" class="btn btn-primary">Create Category</button>
            </form>
        </section>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>