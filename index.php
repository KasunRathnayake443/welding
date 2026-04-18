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

<!-- ═══════════════════════════════════════════════════════
     HERO — full-width carousel, content overlaid
════════════════════════════════════════════════════════ -->
<section class="hero-section">

    <!-- Carousel fills the section as background -->
    <div class="hero-carousel-panel" id="heroCarousel" aria-label="Project gallery" style="--slide-count: 4">

        <div class="carousel-track" id="carouselTrack">

            <div class="carousel-slide">
                <img src="<?php echo ASSETS_URL; ?>img/home/metal-stairs.webp" alt="Custom metal staircase">
                <div class="slide-caption">
                    <p class="slide-caption-label">Structural Work</p>
                    <p class="slide-caption-title">Custom Metal Staircase</p>
                </div>
            </div>

            <div class="carousel-slide">
                <img src="<?php echo ASSETS_URL; ?>img/home/table.webp" alt="Metal furniture fabrication">
                <div class="slide-caption">
                    <p class="slide-caption-label">Furniture</p>
                    <p class="slide-caption-title">Industrial Table &amp; Frame</p>
                </div>
            </div>

            <div class="carousel-slide">
                <img src="<?php echo ASSETS_URL; ?>img/home/gates.webp" alt="Gate and railing work">
                <div class="slide-caption">
                    <p class="slide-caption-label">Gates &amp; Railings</p>
                    <p class="slide-caption-title">Decorative Entry Gate</p>
                </div>
            </div>

            <div class="carousel-slide">
                <img src="<?php echo ASSETS_URL; ?>img/home/framestructure.jpg" alt="Project-based metal fabrication">
                <div class="slide-caption">
                    <p class="slide-caption-label">Project Fabrication</p>
                    <p class="slide-caption-title">Custom Steel Frame Structure</p>
                </div>
            </div>

        </div><!-- /.carousel-track -->

        <!-- Slide counter -->
        <div class="carousel-counter" aria-hidden="true">
            <span id="carouselCurrent">1</span> / <span id="carouselTotal">4</span>
        </div>

        <!-- Controls -->
        <div class="carousel-controls" aria-label="Carousel controls">
            <div class="carousel-dots" id="carouselDots" role="tablist" aria-label="Slides"></div>
            <button class="carousel-btn" id="carouselPrev" aria-label="Previous slide">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="carousel-btn" id="carouselNext" aria-label="Next slide">
                <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

    </div><!-- /.hero-carousel-panel -->

    <!-- Content overlay (above carousel) -->
    <div class="hero-content-panel">

        <span class="hero-eyebrow">Custom Welding &amp; Metal Fabrication</span>

        <h1>
            <?php echo $siteSettings['hero_title'] ?: 'Built in <em>Metal</em>.<br>Made for Real Requirements.'; ?>
        </h1>

        <p>
            <?php echo e($siteSettings['hero_subtitle'] ?: 'We build custom metal products and fabrication work for homes, businesses, and project-based needs — from furniture and racks to staircases, railings, and custom structures.'); ?>
        </p>

        <div class="hero-actions">
            <a href="<?php echo BASE_URL; ?>products.php" class="btn-primary-gold">
                Explore Products
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>

            <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn-ghost-light">
                See Previous Work
            </a>

            <?php if ($whatsAppLink !== '#'): ?>
                <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn-ghost-light">
                    WhatsApp Us
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn-ghost-light">Contact Us</a>
            <?php endif; ?>
        </div>

    </div><!-- /.hero-content-panel -->

</section>


<!-- ═══════════════════════════════════════════════════════
     SERVICES STRIP
════════════════════════════════════════════════════════ -->
<section class="services-strip">
    <div class="services-strip-grid">

        <div class="service-box">
            <div class="service-box-image">
                <img src="<?php echo ASSETS_URL; ?>img/home/table.webp" alt="Metal product designs">
                <!-- fallback shown if image missing via onerror -->
            </div>
            <div class="service-box-text">
                <span class="service-tag">Catalogue</span>
                <h3>Product Designs</h3>
                <p>Reference designs customers can order and customize to their size and finish.</p>
            </div>
        </div>

        <div class="service-box">
            <div class="service-box-image">
                <img src="<?php echo ASSETS_URL; ?>img/home/custom-fabrication.jpg" alt="Custom metal fabrication">
            </div>
            <div class="service-box-text">
                <span class="service-tag">Custom</span>
                <h3>Custom Fabrication</h3>
                <p>Metal work built specifically for real-world requirements and project specs.</p>
            </div>
        </div>

        <div class="service-box">
            <div class="service-box-image">
                <img src="<?php echo ASSETS_URL; ?>img/home/bulk-orders.png" alt="Bulk metal orders">
            </div>
            <div class="service-box-text">
                <span class="service-tag">Bulk</span>
                <h3>Bulk Orders</h3>
                <p>Ideal for repeated builds, commercial setups, and larger order quantities.</p>
            </div>
        </div>

        <div class="service-box">
            <div class="service-box-image">
                <img src="<?php echo ASSETS_URL; ?>img/home/site-works.jpg" alt="On-site metal work">
            </div>
            <div class="service-box-text">
                <span class="service-tag">On-site</span>
                <h3>Site Work</h3>
                <p>From decorative details to practical structural welding jobs on location.</p>
            </div>
        </div>

    </div><!-- /.services-strip-grid -->
</section>

<!-- ═══════════════════════════════════════════════════════
     INTRO
════════════════════════════════════════════════════════ -->
<section class="section intro-section">
    <div class="container intro-grid">
        <div class="intro-image-wrap">
            <img src="<?php echo ASSETS_URL; ?>img/home/welding.webp" alt="Custom welding and fabrication">
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

<!-- ═══════════════════════════════════════════════════════
     CATEGORY HIGHLIGHTS
════════════════════════════════════════════════════════ -->
<section class="section category-highlight-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>What We Build</h2>
            <p>We take on a wide variety of metal-related work, from everyday product builds to larger practical fabrication jobs.</p>
        </div>

        <div class="category-highlight-grid">
            <div class="category-highlight-card">
                <h3>Furniture &amp; Interior Items</h3>
                <p>Metal tables, chairs, shelves, racks, stands, and decorative pieces.</p>
            </div>
            <div class="category-highlight-card">
                <h3>Home &amp; Building Work</h3>
                <p>Staircases, railings, gates, grills, handrails, and structural details.</p>
            </div>
            <div class="category-highlight-card">
                <h3>Project-Based Fabrication</h3>
                <p>Custom frames, supports, bridge-like builds, and other made-to-requirement jobs.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     FEATURED PRODUCTS
════════════════════════════════════════════════════════ -->
<section class="section featured-section">
    <div class="container">
        <div class="section-heading split-heading">
            <div class="section-heading-left">
                <h2>Featured Products</h2>
                <p>Sample designs customers can use as a starting point. Sizes, finishes, and details can be changed when needed.</p>
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
                            <p><?php echo e($product['short_description'] ?: 'Customizable metal product available on request.'); ?></p>
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

<!-- ═══════════════════════════════════════════════════════
     PORTFOLIO
════════════════════════════════════════════════════════ -->
<section class="section portfolio-section">
    <div class="container">
        <div class="section-heading split-heading">
            <div class="section-heading-left">
                <h2>Previous Work</h2>
                <p>A look at some of the practical metal work and fabrication projects already completed.</p>
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
                                <div class="common-meta"><span>Featured Work</span></div>
                            <?php endif; ?>
                            <h3><?php echo e($item['title']); ?></h3>
                            <p><?php echo e($item['short_description'] ?: 'Completed custom metal work project.'); ?></p>
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

<!-- ═══════════════════════════════════════════════════════
     CUSTOM ORDER CTA
════════════════════════════════════════════════════════ -->
<section class="section custom-order-section">
    <div class="container custom-order-wrap">
        <div>
            <h2>Need Something Built to Your Requirement?</h2>
            <p>Contact us for custom fabrication jobs, changes to existing product designs, or larger order quantities for repeated builds.</p>
        </div>
        <div class="hero-actions">
            <?php if ($whatsAppLink !== '#'): ?>
                <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-primary">Request via WhatsApp</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline light-outline">Contact Us</a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CONTACT SUMMARY
════════════════════════════════════════════════════════ -->
<section class="section contact-summary-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>Get in Touch</h2>
            <p>Reach out to discuss product orders, fabrication work, bulk requests, or custom projects.</p>
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

<!-- ═══════════════════════════════════════════════════════
     CAROUSEL JS (vanilla, no dependencies)
════════════════════════════════════════════════════════ -->
<script>
(function () {
    const track    = document.getElementById('carouselTrack');
    const dotsWrap = document.getElementById('carouselDots');
    const btnPrev  = document.getElementById('carouselPrev');
    const btnNext  = document.getElementById('carouselNext');
    const elCur    = document.getElementById('carouselCurrent');
    const elTotal  = document.getElementById('carouselTotal');
    const panel    = document.getElementById('heroCarousel');

    if (!track) return;

    const slides = track.querySelectorAll('.carousel-slide');
    const total  = slides.length;
    let current  = 0;
    let timer;

    /* Sync the CSS variable so the track/slide widths are correct */
    panel.style.setProperty('--slide-count', total);
    elTotal.textContent = total;

    /* build dots */
    slides.forEach((_, i) => {
        const d = document.createElement('button');
        d.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        d.setAttribute('role', 'tab');
        d.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        d.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(d);
    });

    function goTo(index) {
        current = (index + total) % total;

        /* Each slide = (100 / total)% of track width, so step = (100 / total)% */
        const pct = current * (100 / total);
        track.style.transform = 'translateX(-' + pct + '%)';

        elCur.textContent = current + 1;

        dotsWrap.querySelectorAll('.carousel-dot').forEach((d, i) => {
            d.classList.toggle('active', i === current);
        });

        resetTimer();
    }

    function resetTimer() {
        clearInterval(timer);
        timer = setInterval(() => goTo(current + 1), 5000);
    }

    btnPrev.addEventListener('click', () => goTo(current - 1));
    btnNext.addEventListener('click', () => goTo(current + 1));

    /* pause on hover */
    panel.addEventListener('mouseenter', () => clearInterval(timer));
    panel.addEventListener('mouseleave', resetTimer);

    /* touch/swipe */
    let startX = 0;
    panel.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    panel.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) goTo(dx < 0 ? current + 1 : current - 1);
    }, { passive: true });

    resetTimer();
})();
</script>