<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Add Product - ' . APP_NAME;
$pageHeading = 'Add Product';
$pageSubheading = 'Create a new product and assign category-based and extra custom properties.';
$pageCssFiles = ['products.css'];

$errors = [];

$categories = get_active_categories($pdo);

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$name = '';
$slug = '';
$shortDescription = '';
$fullDescription = '';
$priceText = '';
$availableColors = '';
$isCustomizable = 1;
$isFeatured = 0;
$status = 1;

$propertyValues = [];
$extraPropertyNames = [''];
$extraPropertyValues = [''];

if (is_post_request()) {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $fullDescription = trim($_POST['full_description'] ?? '');
    $priceText = trim($_POST['price_text'] ?? '');
    $availableColors = trim($_POST['available_colors'] ?? '');
    $isCustomizable = isset($_POST['is_customizable']) ? 1 : 0;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $status = isset($_POST['status']) ? 1 : 0;
    $csrfToken = $_POST['csrf_token'] ?? '';

    $propertyValues = $_POST['property_values'] ?? [];
    $extraPropertyNames = $_POST['extra_property_name'] ?? [''];
    $extraPropertyValues = $_POST['extra_property_value'] ?? [''];

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

    if ($name === '') {
        $errors[] = 'Product name is required.';
    } elseif (mb_strlen($name) > 200) {
        $errors[] = 'Product name must not exceed 200 characters.';
    }

    if ($slug === '') {
        $slug = slugify($name);
    } else {
        $slug = slugify($slug);
    }

    if ($slug === '') {
        $errors[] = 'A valid slug could not be generated.';
    } elseif (mb_strlen($slug) > 220) {
        $errors[] = 'Slug must not exceed 220 characters.';
    } elseif (product_slug_exists($pdo, $slug)) {
        $errors[] = 'This slug already exists. Please use a different one.';
    }

    if ($priceText !== '' && mb_strlen($priceText) > 150) {
        $errors[] = 'Price text must not exceed 150 characters.';
    }

    // Validate category properties
    $categoryProperties = $categoryId > 0 ? get_category_properties($pdo, $categoryId, false) : [];
    $validPropertyIds = array_map(fn($p) => (int)$p['id'], $categoryProperties);

    foreach ($propertyValues as $propertyId => $value) {
        $propertyId = (int)$propertyId;
        if (!in_array($propertyId, $validPropertyIds, true)) {
            $errors[] = 'Invalid category property submitted.';
            break;
        }
    }

    // Normalize extra properties
    $normalizedExtraRows = [];
    $extraCount = max(count($extraPropertyNames), count($extraPropertyValues));

    for ($i = 0; $i < $extraCount; $i++) {
        $propName = trim($extraPropertyNames[$i] ?? '');
        $propValue = trim($extraPropertyValues[$i] ?? '');

        if ($propName === '' && $propValue === '') {
            continue;
        }

        if ($propName === '') {
            $errors[] = 'Each extra property must have a property name.';
            break;
        }

        if (mb_strlen($propName) > 150) {
            $errors[] = 'Extra property name must not exceed 150 characters.';
            break;
        }

        $normalizedExtraRows[] = [
            'name' => $propName,
            'value' => $propValue,
        ];
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO products
                (category_id, name, slug, short_description, full_description, price_text, available_colors, is_customizable, is_featured, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $categoryId,
                $name,
                $slug,
                $shortDescription !== '' ? $shortDescription : null,
                $fullDescription !== '' ? $fullDescription : null,
                $priceText !== '' ? $priceText : null,
                $availableColors !== '' ? $availableColors : null,
                $isCustomizable,
                $isFeatured,
                $status
            ]);

            $productId = (int)$pdo->lastInsertId();

            // Insert category property values
            foreach ($categoryProperties as $property) {
                $propertyId = (int)$property['id'];
                $value = trim($propertyValues[$propertyId] ?? '');

                if ($value === '') {
                    continue;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO product_property_values (product_id, category_property_id, property_value)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$productId, $propertyId, $value]);
            }

            // Insert extra properties
            foreach ($normalizedExtraRows as $index => $row) {
                $stmt = $pdo->prepare("
                    INSERT INTO product_extra_properties (product_id, property_name, property_value, sort_order)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $productId,
                    $row['name'],
                    $row['value'] !== '' ? $row['value'] : null,
                    $index
                ]);
            }

            $pdo->commit();

            set_flash('success', 'Product created successfully.');
            redirect(ADMIN_URL . 'products/index.php');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Failed to create product. Please try again.';
        }
    }
}

$categoryProperties = $categoryId > 0 ? get_category_properties($pdo, $categoryId, false) : [];
$csrfToken = generate_csrf_token();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

        <div class="page-actions page-actions-left">
            <a href="<?php echo ADMIN_URL; ?>products/index.php" class="btn btn-dark">← Back to Products</a>
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
                <h3>Product Details</h3>
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="category_id">Category <span>*</span></label>
                        <select name="category_id" id="category_id" onchange="window.location='<?php echo ADMIN_URL; ?>products/create.php?category_id=' + this.value;" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int)$category['id']; ?>" <?php echo $categoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help">Select a category first to load its properties.</div>
                    </div>

                    <div class="form-group">
                        <label for="name">Product Name <span>*</span></label>
                        <input type="text" id="name" name="name" maxlength="200" value="<?php echo e($name); ?>" placeholder="Enter product name" required>
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" maxlength="220" value="<?php echo e($slug); ?>" placeholder="Leave blank to auto-generate">
                    </div>

                    <div class="form-group">
                        <label for="price_text">Price Text</label>
                        <input type="text" id="price_text" name="price_text" maxlength="150" value="<?php echo e($priceText); ?>" placeholder="e.g. Starting from Rs. 45,000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <textarea id="short_description" name="short_description" placeholder="Short description"><?php echo e($shortDescription); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="full_description">Full Description</label>
                    <textarea id="full_description" name="full_description" placeholder="Full product description"><?php echo e($fullDescription); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="available_colors">Available Colors</label>
                    <textarea id="available_colors" name="available_colors" placeholder="e.g. Black, White, Gold, Custom"><?php echo e($availableColors); ?></textarea>
                </div>

                <div class="checkbox-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_customizable" value="1" <?php echo $isCustomizable ? 'checked' : ''; ?>>
                        <span>Customizable</span>
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" <?php echo $isFeatured ? 'checked' : ''; ?>>
                        <span>Featured</span>
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="status" value="1" <?php echo $status ? 'checked' : ''; ?>>
                        <span>Active</span>
                    </label>
                </div>

                <div class="section-block">
                    <div class="section-block-header">
                        <h4>Category Properties</h4>
                    </div>

                    <?php if (!empty($categoryProperties)): ?>
                        <div class="property-grid">
                            <?php foreach ($categoryProperties as $property): ?>
                                <?php $propertyId = (int)$property['id']; ?>
                                <div class="form-group">
                                    <label for="property_<?php echo $propertyId; ?>">
                                        <?php echo e($property['property_name']); ?>
                                    </label>

                                    <?php if ($property['field_type'] === 'textarea'): ?>
                                        <textarea
                                            id="property_<?php echo $propertyId; ?>"
                                            name="property_values[<?php echo $propertyId; ?>]"
                                            placeholder="<?php echo e($property['placeholder'] ?: 'Enter value'); ?>"
                                        ><?php echo e($propertyValues[$propertyId] ?? ''); ?></textarea>
                                    <?php else: ?>
                                        <input
                                            type="<?php echo $property['field_type'] === 'number' ? 'number' : 'text'; ?>"
                                            id="property_<?php echo $propertyId; ?>"
                                            name="property_values[<?php echo $propertyId; ?>]"
                                            value="<?php echo e($propertyValues[$propertyId] ?? ''); ?>"
                                            placeholder="<?php echo e($property['placeholder'] ?: 'Enter value'); ?>"
                                        >
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-mini-state">
                            <p>Select a category that has properties, or create properties first.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="section-block">
                    <div class="section-block-header">
                        <h4>Extra Custom Properties</h4>
                        <button type="button" class="btn btn-light btn-sm" id="addExtraPropertyBtn">+ Add Extra Property</button>
                    </div>

                    <div id="extraPropertiesWrapper">
                        <?php
                        $extraRowCount = max(count($extraPropertyNames), count($extraPropertyValues));
                        if ($extraRowCount === 0) {
                            $extraRowCount = 1;
                        }
                        ?>

                        <?php for ($i = 0; $i < $extraRowCount; $i++): ?>
                            <div class="extra-property-row">
                                <input
                                    type="text"
                                    name="extra_property_name[]"
                                    maxlength="150"
                                    value="<?php echo e($extraPropertyNames[$i] ?? ''); ?>"
                                    placeholder="Property name"
                                >
                                <input
                                    type="text"
                                    name="extra_property_value[]"
                                    value="<?php echo e($extraPropertyValues[$i] ?? ''); ?>"
                                    placeholder="Property value"
                                >
                                <button type="button" class="btn btn-danger btn-sm remove-extra-btn">Remove</button>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Product</button>
            </form>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('extraPropertiesWrapper');
    const addBtn = document.getElementById('addExtraPropertyBtn');

    if (wrapper && addBtn) {
        addBtn.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'extra-property-row';
            row.innerHTML = `
                <input type="text" name="extra_property_name[]" maxlength="150" placeholder="Property name">
                <input type="text" name="extra_property_value[]" placeholder="Property value">
                <button type="button" class="btn btn-danger btn-sm remove-extra-btn">Remove</button>
            `;
            wrapper.appendChild(row);
        });

        wrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-extra-btn')) {
                const rows = wrapper.querySelectorAll('.extra-property-row');
                if (rows.length > 1) {
                    e.target.closest('.extra-property-row').remove();
                } else {
                    const inputs = rows[0].querySelectorAll('input');
                    inputs.forEach(input => input.value = '');
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>