<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);
$categories = get_public_categories($pdo);

$search = trim($_GET['search'] ?? '');
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$products = get_public_products($pdo, $search, $categoryId);

$pageTitle = 'Products - ' . ($siteSettings['site_name'] ?: 'Welding Company');
$frontendCssFiles = ['products.css'];

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <h1>Products</h1>
            <p>
                Browse our sample product designs. These can be customized based on your required size, finish, color, and other details.
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="products-layout">
            <aside class="products-sidebar">
                <div class="sidebar-card">
                    <h3>Search Products</h3>

                    <form method="GET" action="" class="public-filter-form">
                        <div class="form-group">
                            <label for="search">Keyword</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="<?php echo e($search); ?>"
                                placeholder="Search products"
                            >
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category" id="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo (int)$category['id']; ?>" <?php echo $categoryId === (int)$category['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($category['name']); ?> (<?php echo e($category['product_count']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="<?php echo BASE_URL; ?>products.php" class="btn btn-outline">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="sidebar-card">
                    <h3>Customization</h3>
                    <p>
                        Most products shown here are reference designs. We can customize dimensions, materials, colors, and finishes based on your request.
                    </p>
                </div>
            </aside>

            <div class="products-content">
                <div class="products-toolbar">
                    <div>
                        <h2>Available Products</h2>
                        <p><?php echo count($products); ?> product(s) found</p>
                    </div>
                </div>

                <?php if (!empty($products)): ?>
                    <div class="card-grid">
                        <?php foreach ($products as $product): ?>
                            <article class="common-card">
                                <div class="common-card-image">
                                    <?php if (!empty($product['primary_image'])): ?>
                                        <img src="<?php echo UPLOADS_URL . 'products/' . e($product['primary_image']); ?>" alt="<?php echo e($product['name']); ?>">
                                    <?php else: ?>
                                        <div class="image-fallback">No Image</div>
                                    <?php endif; ?>
                                </div>

                                <div class="common-card-body">
                                    <div class="common-meta">
                                        <span><?php echo e($product['category_name']); ?></span>

                                        <?php if (!empty($product['price_text'])): ?>
                                            <span><?php echo e($product['price_text']); ?></span>
                                        <?php endif; ?>

                                        <?php if ((int)$product['is_customizable'] === 1): ?>
                                            <span>Customizable</span>
                                        <?php endif; ?>
                                    </div>

                                    <h3><?php echo e($product['name']); ?></h3>

                                    <p>
                                        <?php echo e($product['short_description'] ?: 'Custom metal product available on request.'); ?>
                                    </p>

                                    <a href="<?php echo BASE_URL; ?>product.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn btn-outline">
                                        View Details
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-public-state">
                        <p>No products found for the selected filters.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>