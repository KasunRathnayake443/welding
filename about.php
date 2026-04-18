<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);

$pageTitle = 'About - ' . ($siteSettings['site_name'] ?: 'Welding Company');
$frontendCssFiles = ['about.css'];

$whatsAppMessage = 'Hi, I would like to know more about your metal fabrication services.';
$whatsAppLink = get_whatsapp_link($siteSettings['whatsapp'] ?? '', $whatsAppMessage);

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="about-hero-section">
    <div class="about-hero-overlay"></div>
    <div class="container about-hero-grid">
        <div class="about-hero-content">
            <span class="hero-eyebrow">About Our Work</span>
            <h1>Built on Real Welding and Fabrication Work</h1>
            <p>
                We are a hands-on metal fabrication business focused on custom builds, practical project work, and product designs that can be adjusted to match customer requirements.
            </p>

            <div class="about-hero-actions">
                <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn btn-primary">See Previous Work</a>

                <?php if ($whatsAppLink !== '#'): ?>
                    <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-outline light-outline">
                        WhatsApp Us
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline light-outline">
                        Contact Us
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="about-hero-visual">
            <div class="about-hero-image-card">
                <img src="<?php echo ASSETS_URL; ?>img/about/about-hero.jpg" alt="Metal fabrication and welding work">
            </div>
        </div>
    </div>
</section>

<section class="section story-section">
    <div class="container story-grid">
        <div class="story-image-wrap">
            <img src="<?php echo ASSETS_URL; ?>img/about/about1.jpg" alt="Workshop and fabrication process">
        </div>

        <div class="story-content">
            <div class="section-heading">
                <h2>More Than a Product Catalog</h2>
                <p>
                    This website represents both the products available for order and the wider custom metal work the business can take on.
                </p>
            </div>

            <div class="story-card">
                <p>
                    Some customers come looking for a table, chair, rack, or another sample design they saw on the site. Others need something completely different — a staircase, railing, structural piece, custom frame, or another made-to-requirement fabrication job.
                </p>

                <p>
                    That is why the website is structured as both a product showcase and a portfolio of completed work. It helps customers understand what can be ordered directly, what can be customized, and what kind of custom fabrication work can also be handled.
                </p>
            </div>

            <div class="story-points">
                <div class="story-point">
                    <h3>Sample Designs</h3>
                    <p>Products shown on the site can be used as starting points for new orders.</p>
                </div>

                <div class="story-point">
                    <h3>Custom Fabrication</h3>
                    <p>We also take on work that goes beyond the listed products.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section capabilities-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>What We Can Handle</h2>
            <p>
                Our work covers a wide range of metal-related fabrication needs, from smaller customer products to larger practical project work.
            </p>
        </div>

        <div class="capabilities-grid">
            <div class="capability-card">
                <h3>Metal Furniture</h3>
                <p>Tables, chairs, shelves, racks, and other fabricated product designs.</p>
            </div>

            <div class="capability-card">
                <h3>Home & Building Work</h3>
                <p>Staircases, railings, gates, grills, handrails, and support structures.</p>
            </div>

            <div class="capability-card">
                <h3>Custom Fabrication</h3>
                <p>Project-based work built according to drawings, measurements, or practical requirements.</p>
            </div>

            <div class="capability-card">
                <h3>Bulk & Repeat Orders</h3>
                <p>Suitable for customers who need repeated builds or multiple units of the same design.</p>
            </div>
        </div>
    </div>
</section>

<section class="section working-style-section">
    <div class="container">
        <div class="working-style-box">
            <div class="working-style-content">
                <h2>How Orders Usually Work</h2>
                <p>
                    Customers can choose an existing design, request changes, or contact us with a completely different fabrication need.
                </p>
            </div>

            <div class="working-style-steps">
                <div class="work-step">
                    <span>01</span>
                    <h3>Choose or Describe</h3>
                    <p>Select a product design or explain the custom work needed.</p>
                </div>

                <div class="work-step">
                    <span>02</span>
                    <h3>Discuss Requirements</h3>
                    <p>Confirm dimensions, finish, color, materials, and practical details.</p>
                </div>

                <div class="work-step">
                    <span>03</span>
                    <h3>Build to Need</h3>
                    <p>Proceed with the fabrication work based on the agreed requirement.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section values-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>Why Customers Reach Out</h2>
            <p>
                The business is built around practical fabrication work, flexibility, and custom solutions rather than fixed off-the-shelf limitations.
            </p>
        </div>

        <div class="values-grid">
            <div class="value-card">
                <h3>Real Fabrication Experience</h3>
                <p>
                    The work shown here is based on actual completed jobs and practical fabrication experience.
                </p>
            </div>

            <div class="value-card">
                <h3>Flexible Customization</h3>
                <p>
                    Many designs can be adjusted in size, finish, color, and other details to match the order.
                </p>
            </div>

            <div class="value-card">
                <h3>Wider Capability</h3>
                <p>
                    The business is not limited to one product type — it can handle a broad range of metal-related work.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container cta-box">
        <div>
            <h2>Need a Product or a Custom Build?</h2>
            <p>
                Whether you want one of the shown designs or a completely different fabrication job, get in touch and discuss your requirement.
            </p>
        </div>

        <div class="cta-actions">
            <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-primary">Contact Us</a>

            <?php if ($whatsAppLink !== '#'): ?>
                <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-dark">WhatsApp Us</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>