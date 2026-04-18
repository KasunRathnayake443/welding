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
    <div class="hero-overlay"></div>
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="hero-eyebrow">Custom Welding & Metal Fabrication</span>

            <h1>
                <?php echo e($siteSettings['hero_title'] ?: 'Built in Metal. Made for Real Requirements.'); ?>
            </h1>

            <p>
                <?php echo e($siteSettings['hero_subtitle'] ?: 'We build custom metal products and fabrication work for homes, businesses, and project-based needs — from furniture and racks to staircases, railings, and custom structures.'); ?>
            </p>

            <div class="hero-actions">
                <a href="<?php echo BASE_URL; ?>products.php" class="btn btn-primary">Explore Products</a>
                <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn btn-outline light-outline">See Previous Work</a>

                <?php if ($whatsAppLink !== '#'): ?>
                    <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-dark hero-dark-btn">WhatsApp Us</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-dark hero-dark-btn">Contact Us</a>
                <?php endif; ?>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <strong>Custom Products</strong>
                    <span>Tables, chairs, racks and more</span>
                </div>

                <div class="hero-stat">
                    <strong>Project Work</strong>
                    <span>Staircases, railings, gates, structures</span>
                </div>

                <div class="hero-stat">
                    <strong>Made to Requirement</strong>
                    <span>Size, finish and design can be adjusted</span>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-visual-card">
                <img src="<?php echo ASSETS_URL; ?>images/home-hero.jpg" alt="Metal fabrication work">
            </div>
        </div>
    </div>
</section>

<section class="services-strip">
    <div class="container services-strip-grid">
        <div class="service-box">
            <h3>Product Designs</h3>
            <p>Reference designs customers can order and customize.</p>
        </div>

        <div class="service-box">
            <h3>Custom Fabrication</h3>
            <p>Metal work built for real-world requirements.</p>
        </div>

        <div class="service-box">
            <h3>Bulk Orders</h3>
            <p>Ideal for repeated builds and larger order quantities.</p>
        </div>

        <div class="service-box">
            <h3>Site Work</h3>
            <p>From decorative items to practical structural jobs.</p>
        </div>
    </div>
</section>

<section class="section intro-section">
    <div class="container intro-grid">
        <div class="intro-image-wrap">
            <img src="<?php echo ASSETS_URL; ?>images/home-about.jpg" alt="Custom welding and fabrication">
        </div>

        <div class="intro-content">
            <div class="section-heading">
                <h2>More Than Just Tables and Chairs</h2>
                <p>
                    We handle a wide range of welding and fabrication work, while also offering product designs customers can use as a starting point for custom orders.
                </p>
            </div>

            <div class="intro-text-card">
                <p>
                    This website is built to show both sides of the business: the custom metal products available for order, and the broader fabrication work already completed for customers and projects.
                </p>

                <p>
                    If you see a product design you like, it can usually be customized to match your preferred size, color, finish, or other details. If you need something completely different, you can contact us for a custom fabrication job.
                </p>
            </div>

            <div class="intro-points">
                <div class="intro-point">
                    <h3>Reference Designs</h3>
                    <p>Start from an existing design and modify it to fit your need.</p>
                </div>

                <div class="intro-point">
                    <h3>Custom Builds</h3>
                    <p>We also fabricate one-off and project-based metal work.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section category-highlight-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>What We Build</h2>
            <p>
                We take on a wide variety of metal-related work, from everyday product builds to larger practical fabrication jobs.
            </p>
        </div>

        <div class="category-highlight-grid">
            <div class="category-highlight-card">
                <h3>Furniture & Interior Items</h3>
                <p>Metal tables, chairs, shelves, racks, stands, and decorative pieces.</p>
            </div>

            <div class="category-highlight-card">
                <h3>Home & Building Work</h3>
                <p>Staircases, railings, gates, grills, handrails, and structural details.</p>
            </div>

            <div class="category-highlight-card">
                <h3>Project-Based Fabrication</h3>
                <p>Custom frames, supports, bridge-like builds, and other made-to-requirement jobs.</p>
            </div>
        </div>
    </div>
</section>

<section class="section featured-section">
    <div class="container">
        <div class="section-heading split-heading">
            <div class="section-heading-left">
                <h2>Featured Products</h2>
                <p>
                    These are sample designs customers can use as a starting point. Sizes, finishes, and details can be changed when needed.
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
                                <div class="image-fallback">No Image</div>
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
        <div class="section-heading split-heading">
            <div class="section-heading-left">
                <h2>Previous Work</h2>
                <p>
                    A look at some of the practical metal work and fabrication projects already completed.
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
                                <div class="image-fallback">No Image</div>
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
            <h2>Need Something Built to Your Requirement?</h2>
            <p>
                You can contact us for custom fabrication jobs, changes to existing product designs, or larger order quantities for repeated builds.
            </p>
        </div>

        <div class="hero-actions">
            <?php if ($whatsAppLink !== '#'): ?>
                <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-primary">Request via WhatsApp</a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline light-outline">Contact Us</a>
        </div>
    </div>
</section>

<section class="section contact-summary-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>Get in Touch</h2>
            <p>
                Reach out to discuss product orders, fabrication work, bulk requests, or custom projects.
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