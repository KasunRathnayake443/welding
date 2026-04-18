<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);

$slug = trim($_GET['slug'] ?? '');
$product = $slug !== '' ? get_public_product_by_slug($pdo, $slug) : null;

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product Not Found - ' . ($siteSettings['site_name'] ?: 'Welding Company');
    $frontendCssFiles = ['product-details.css'];
    require_once __DIR__ . '/includes/frontend-header.php';
    ?>
    <section class="section">
        <div class="container">
            <div class="empty-public-state">
                <p>Product not found.</p>
                <br>
                <a href="<?php echo BASE_URL; ?>products.php" class="btn btn-primary">Back to Products</a>
            </div>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/frontend-footer.php';
    exit;
}

$productImages = get_product_images($pdo, (int)$product['id']);
$categoryProperties = get_product_category_properties_with_values($pdo, (int)$product['id']);
$extraProperties = get_product_extra_properties($pdo, (int)$product['id']);
$relatedProducts = get_related_products($pdo, (int)$product['category_id'], (int)$product['id'], 4);

$pageTitle = $product['name'] . ' - ' . ($siteSettings['site_name'] ?: 'Welding Company');
$frontendCssFiles = ['product-details.css'];

$whatsAppMessage = "Hi, I'm interested in the product: " . $product['name'] . ". Can you share more details?";
$whatsAppLink = get_whatsapp_link($siteSettings['whatsapp'] ?? '', $whatsAppMessage);

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="page-hero small-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a>
            <span>/</span>
            <a href="<?php echo BASE_URL; ?>products.php">Products</a>
            <span>/</span>
            <span><?php echo e($product['name']); ?></span>
        </div>
    </div>
</section>

<section class="section product-detail-section">
    <div class="container">
        <div class="product-detail-grid">
            <div class="product-gallery">
                <?php if (!empty($productImages)): ?>
                    <div class="main-product-image">
                        <img
                            id="mainProductImage"
                            src="<?php echo UPLOADS_URL . 'products/' . e($productImages[0]['image_path']); ?>"
                            alt="<?php echo e($product['name']); ?>"
                        >
                    </div>

                    <?php if (count($productImages) > 1): ?>
                        <div class="thumbnail-row">
                            <?php foreach ($productImages as $index => $image): ?>
                                <button
                                    type="button"
                                    class="thumbnail-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                                    data-image="<?php echo UPLOADS_URL . 'products/' . e($image['image_path']); ?>"
                                >
                                    <img src="<?php echo UPLOADS_URL . 'products/' . e($image['image_path']); ?>" alt="<?php echo e($product['name']); ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="main-product-image no-image-box">
                        <span>No Image Available</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <div class="common-meta detail-meta">
                    <span><?php echo e($product['category_name']); ?></span>

                    <?php if (!empty($product['price_text'])): ?>
                        <span><?php echo e($product['price_text']); ?></span>
                    <?php endif; ?>

                    <?php if ((int)$product['is_customizable'] === 1): ?>
                        <span>Customizable</span>
                    <?php endif; ?>
                </div>

                <h1><?php echo e($product['name']); ?></h1>

                <?php if (!empty($product['short_description'])): ?>
                    <p class="product-short-description"><?php echo e($product['short_description']); ?></p>
                <?php endif; ?>

                <?php if (!empty($product['full_description'])): ?>
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(e($product['full_description'])); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['available_colors'])): ?>
                    <div class="product-description">
                        <h3>Available Colors</h3>
                        <p><?php echo nl2br(e($product['available_colors'])); ?></p>
                    </div>
                <?php endif; ?>

                <div class="product-cta-box">
                    <h3>Need This Design?</h3>
                    <p>
                        This product can be discussed and customized based on your requirement.
                    </p>

                    <div class="product-actions">
                        <?php if ($whatsAppLink !== '#'): ?>
                            <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-primary">
                                Ask on WhatsApp
                            </a>
                        <?php endif; ?>

                        <a href="<?php echo BASE_URL; ?>contact.php?product=<?php echo urlencode($product['name']); ?>" class="btn btn-outline">
                            Request Quote
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($categoryProperties) || !empty($extraProperties)): ?>
            <div class="specifications-section">
                <div class="section-heading">
                    <h2>Specifications</h2>
                    <p>Product details and available specifications.</p>
                </div>

                <div class="specification-list">
                    <?php foreach ($categoryProperties as $property): ?>
                        <div class="spec-row">
                            <span><?php echo e($property['property_name']); ?></span>
                            <strong><?php echo e($property['property_value']); ?></strong>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($extraProperties as $property): ?>
                        <div class="spec-row">
                            <span><?php echo e($property['property_name']); ?></span>
                            <strong><?php echo e($property['property_value'] ?: '-'); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="customization-note">
            <h3>Customization Available</h3>
            <p>
                This design can be modified based on customer requirements. Size, material, finish, color, and other details can be discussed before order confirmation.
            </p>
        </div>

        <?php if (!empty($relatedProducts)): ?>
            <div class="related-products-section">
                <div class="section-heading">
                    <h2>Related Products</h2>
                    <p>Other products from the same category.</p>
                </div>

                <div class="card-grid">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <article class="common-card">
                            <div class="common-card-image">
                                <?php if (!empty($relatedProduct['primary_image'])): ?>
                                    <img src="<?php echo UPLOADS_URL . 'products/' . e($relatedProduct['primary_image']); ?>" alt="<?php echo e($relatedProduct['name']); ?>">
                                <?php else: ?>
                                    <div class="image-fallback">No Image</div>
                                <?php endif; ?>
                            </div>

                            <div class="common-card-body">
                                <div class="common-meta">
                                    <span><?php echo e($relatedProduct['category_name']); ?></span>
                                </div>

                                <h3><?php echo e($relatedProduct['name']); ?></h3>

                                <p>
                                    <?php echo e($relatedProduct['short_description'] ?: 'Custom metal product available on request.'); ?>
                                </p>

                                <a href="<?php echo BASE_URL; ?>product.php?slug=<?php echo urlencode($relatedProduct['slug']); ?>" class="btn btn-outline">
                                    View Details
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('mainProductImage');
    const thumbs = document.querySelectorAll('.thumbnail-btn');

    if (mainImage && thumbs.length) {
        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                mainImage.src = this.getAttribute('data-image');

                thumbs.forEach(function (item) {
                    item.classList.remove('active');
                });

                this.classList.add('active');
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>