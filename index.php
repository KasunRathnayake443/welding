<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);

$featuredProducts = get_featured_products($pdo, 6);
if (empty($featuredProducts)) {
    $featuredProducts = get_latest_products($pdo, 6);
}

$featuredPortfolioItems = get_featured_portfolio_items($pdo, 6);
if (empty($featuredPortfolioItems)) {
    $featuredPortfolioItems = get_latest_portfolio_items($pdo, 6);
}

$pageTitle = ($siteSettings['site_name'] ?: 'Welding Company') . ' - Home';
$frontendCssFiles = ['home.css'];

$whatsAppMessage = 'Hi, I would like to ask about your metal fabrication work.';
$whatsAppLink = get_whatsapp_link($siteSettings['whatsapp'] ?? '', $whatsAppMessage);

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1>
                <?php echo e($siteSettings['hero_title'] ?: 'Custom Welding & Metal Fabrication'); ?>
            </h1>

            <p>
                <?php echo e($siteSettings['hero_subtitle'] ?: 'We build custom metal products and fabrication work for homes, businesses, and construction-related needs.'); ?>
            </p>

            <div class="hero-actions">
                <a href="<?php echo BASE_URL; ?>products.php" class="btn btn-primary">View Products</a>
                <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn btn-outline">See Previous Work</a>

                <?php if ($whatsAppLink !== '#'): ?>
                    <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-dark">WhatsApp Us</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-dark">Contact Us</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero-highlights">
            <div class="highlight-card">
                <h3>Custom Products</h3>
                <p>We create metal tables, chairs, racks, and other made-to-order items.</p>
            </div>

            <div class="highlight-card">
                <h3>Fabrication Work</h3>
                <p>We handle staircases, gates, railings, structural metal work, and more.</p>
            </div>

            <div class="highlight-card">
                <h3>Bulk Orders</h3>
                <p>We can handle larger custom orders based on customer requirements.</p>
            </div>

            <div class="highlight-card">
                <h3>Built to Requirement</h3>
                <p>Dimensions, colors, finishes, and design details can be adjusted to suit the project.</p>
            </div>
        </div>
    </div>
</section>

<section class="services-strip">
    <div class="container services-strip-grid">
        <div class="service-box">
            <h3>Tables & Chairs</h3>
            <p>Reference designs with customization options.</p>
        </div>

        <div class="service-box">
            <h3>Staircases & Railings</h3>
            <p>Custom work for homes, buildings, and commercial spaces.</p>
        </div>

        <div class="service-box">
            <h3>Racks & Structures</h3>
            <p>Storage racks, shelves, frames, and site metal works.</p>
        </div>

        <div class="service-box">
            <h3>General Metal Fabrication</h3>
            <p>Custom fabrication based on customer drawings or requirements.</p>
        </div>
    </div>
</section>

<section class="section featured-section">
    <div class="container">
        <div class="section-heading">
            <div class="section-heading-left">
                <h2>Featured Products</h2>
                <p>
                    These are sample designs customers can use as a starting point. Sizes, finishes, and other details can be customized as needed.
                </p>
            </div>

            <a href="<?php echo BASE_URL; ?>products.php" class="btn btn-outline">View All Products</a>
        </div>

        <?php if (!empty($featuredProducts)): ?>
            <div class="card-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <article class="common-card">
                        <div class="common-card-image">
                            <?php if (!empty($product['primary_image'])): ?>
                                <img src="<?php echo UPLOADS_URL . 'products/' . e($product['primary_image']); ?>" alt="<?php echo e($product['name']); ?>">
                            <?php else: ?>
                                <img src="<?php echo ASSETS_URL; ?>images/no-image.png" alt="<?php echo e($product['name']); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="common-card-body">
                            <div class="common-meta">
                                <span><?php echo e($product['category_name']); ?></span>
                                <?php if (!empty($product['price_text'])): ?>
                                    <span><?php echo e($product['price_text']); ?></span>
                                <?php endif; ?>
                            </div>

                            <h3><?php echo e($product['name']); ?></h3>

                            <p>
                                <?php echo e($product['short_description'] ?: 'Customizable metal product available on request.'); ?>
                            </p>

                            <a href="<?php echo BASE_URL; ?>product.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn btn-outline">View Details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-public-state">
                <p>No products have been added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section portfolio-section">
    <div class="container">
        <div class="section-heading">
            <div class="section-heading-left">
                <h2>Previous Work</h2>
                <p>
                    Browse some of the completed projects and custom metal work already handled by the company.
                </p>
            </div>

            <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn btn-outline">View Portfolio</a>
        </div>

        <?php if (!empty($featuredPortfolioItems)): ?>
            <div class="card-grid">
                <?php foreach ($featuredPortfolioItems as $item): ?>
                    <article class="common-card">
                        <div class="common-card-image">
                            <?php if (!empty($item['primary_image'])): ?>
                                <img src="<?php echo UPLOADS_URL . 'portfolio/' . e($item['primary_image']); ?>" alt="<?php echo e($item['title']); ?>">
                            <?php else: ?>
                                <img src="<?php echo ASSETS_URL; ?>images/no-image.png" alt="<?php echo e($item['title']); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="common-card-body">
                            <?php if ((int)$item['is_featured'] === 1): ?>
                                <div class="common-meta">
                                    <span>Featured Work</span>
                                </div>
                            <?php endif; ?>

                            <h3><?php echo e($item['title']); ?></h3>

                            <p>
                                <?php echo e($item['short_description'] ?: 'Completed custom metal work project.'); ?>
                            </p>

                            <a href="<?php echo BASE_URL; ?>portfolio-item.php?slug=<?php echo urlencode($item['slug']); ?>" class="btn btn-outline">View Project</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-public-state">
                <p>No portfolio items have been added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section custom-order-section">
    <div class="container custom-order-wrap">
        <div>
            <h2>Need Something Custom?</h2>
            <p>
                Whether you need a custom table, metal staircase, rack, railing, or a completely different fabrication job, you can contact us with your requirements and get a quote.
            </p>
        </div>

        <div class="hero-actions">
            <?php if ($whatsAppLink !== '#'): ?>
                <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-primary">Request via WhatsApp</a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline">Contact Us</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <h2>Contact Information</h2>
            <p>
                Reach out to discuss custom jobs, product inquiries, and bulk fabrication work.
            </p>
        </div>

        <div class="contact-summary-grid">
            <div class="contact-summary-card">
                <h3>Phone</h3>
                <p><?php echo e($siteSettings['phone'] ?: 'Not added yet'); ?></p>
            </div>

            <div class="contact-summary-card">
                <h3>WhatsApp</h3>
                <?php if (!empty($siteSettings['whatsapp'])): ?>
                    <a href="<?php echo e(get_whatsapp_link($siteSettings['whatsapp'], $whatsAppMessage)); ?>" target="_blank" rel="noopener">
                        <?php echo e($siteSettings['whatsapp']); ?>
                    </a>
                <?php else: ?>
                    <p>Not added yet</p>
                <?php endif; ?>
            </div>

            <div class="contact-summary-card">
                <h3>Email</h3>
                <p><?php echo e($siteSettings['email'] ?: 'Not added yet'); ?></p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>