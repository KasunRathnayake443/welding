<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Add Category Property - ' . APP_NAME;
$pageHeading = 'Add Category Property';
$pageSubheading = 'Create a reusable property for a selected category.';
$pageCssFiles = ['category-properties.css'];

$errors = [];

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$propertyName = '';
$fieldType = 'text';
$placeholder = '';
$sortOrder = 0;
$status = 1;

$fieldTypes = allowed_property_field_types();

$categoriesStmt = $pdo->query("SELECT id, name, status FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll();

if (is_post_request()) {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $propertyName = trim($_POST['property_name'] ?? '');
    $fieldType = trim($_POST['field_type'] ?? 'text');
    $placeholder = trim($_POST['placeholder'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($categoryId <= 0) {
        $errors[] = 'Please select a category.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ? LIMIT 1");
        $stmt->execute([$categoryId]);
        if (!$stmt->fetch()) {
            $errors[] = 'Selected category does not exist.';
        }
    }

    if ($propertyName === '') {
        $errors[] = 'Property name is required.';
    } elseif (mb_strlen($propertyName) > 150) {
        $errors[] = 'Property name must not exceed 150 characters.';
    }

    if (!in_array($fieldType, $fieldTypes, true)) {
        $errors[] = 'Invalid field type selected.';
    }

    if ($placeholder !== '' && mb_strlen($placeholder) > 255) {
        $errors[] = 'Placeholder must not exceed 255 characters.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO category_properties
            (category_id, property_name, field_type, placeholder, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $categoryId,
            $propertyName,
            $fieldType,
            $placeholder !== '' ? $placeholder : null,
            $sortOrder,
            $status
        ]);

        set_flash('success', 'Category property created successfully.');
        redirect(ADMIN_URL . 'category-properties/index.php?category_id=' . $categoryId);
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
            <a href="<?php echo ADMIN_URL; ?>category-properties/index.php" class="btn btn-dark">← Back to Category Properties</a>
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
                <h3>Property Details</h3>
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-group">
                    <label for="category_id">Category <span>*</span></label>
                    <select name="category_id" id="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int)$category['id']; ?>" <?php echo $categoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                                <?php echo e($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="property_name">Property Name <span>*</span></label>
                    <input
                        type="text"
                        id="property_name"
                        name="property_name"
                        maxlength="150"
                        value="<?php echo e($propertyName); ?>"
                        placeholder="Enter property name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="field_type">Field Type <span>*</span></label>
                    <select name="field_type" id="field_type" required>
                        <?php foreach ($fieldTypes as $type): ?>
                            <option value="<?php echo e($type); ?>" <?php echo $fieldType === $type ? 'selected' : ''; ?>>
                                <?php echo e(ucfirst($type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="placeholder">Placeholder</label>
                    <input
                        type="text"
                        id="placeholder"
                        name="placeholder"
                        maxlength="255"
                        value="<?php echo e($placeholder); ?>"
                        placeholder="Optional placeholder text"
                    >
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="<?php echo e((string)$sortOrder); ?>"
                        placeholder="0"
                    >
                    <div class="form-help">Lower numbers appear first.</div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="1" <?php echo $status ? 'checked' : ''; ?>>
                        <span>Active</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Create Property</button>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>