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

<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <h1>About Us</h1>
            <p>
                We are a hands-on welding and metal fabrication business focused on practical work, custom builds, and reliable service.
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container about-grid">
        <div class="about-main">
            <div class="section-heading">
                <h2>Built on Real Work</h2>
                <p>
                    Our work is centered around welding, fabrication, and custom metal projects for homes, businesses, and site-based requirements.
                </p>
            </div>

            <div class="about-content-card">
                <p>
                    We handle a wide range of metal work, from custom product designs like tables, chairs, and racks to larger fabrication jobs such as staircases, railings, gates, structural frames, and other project-based requirements.
                </p>

                <p>
                    The products shown on this website are reference designs that customers can order and customize based on their own needs. At the same time, the website also represents the broader fabrication work we do for custom projects.
                </p>

                <p>
                    Whether you need a simple metal product, a custom-built item, or a fabrication job based on a specific requirement, our goal is to provide practical solutions with careful workmanship and clear communication.
                </p>
            </div>
        </div>

        <aside class="about-side">
            <div class="about-side-card">
                <h3>What We Do</h3>
                <ul>
                    <li>Custom metal tables and chairs</li>
                    <li>Staircases and railings</li>
                    <li>Gates and grills</li>
                    <li>Racks and shelves</li>
                    <li>Structural metal fabrication</li>
                    <li>General custom welding work</li>
                </ul>
            </div>

            <div class="about-side-card">
                <h3>How We Work</h3>
                <ul>
                    <li>Show sample designs</li>
                    <li>Discuss customer requirements</li>
                    <li>Adjust size, finish, and details</li>
                    <li>Handle custom and bulk orders</li>
                </ul>
            </div>
        </aside>
    </div>
</section>

<section class="section values-section">
    <div class="container">
        <div class="section-heading center-heading">
            <h2>Why Customers Contact Us</h2>
            <p>
                We focus on practical fabrication work, customization, and helping customers get something built to fit the real requirement.
            </p>
        </div>

        <div class="values-grid">
            <div class="value-card">
                <h3>Custom Work</h3>
                <p>
                    We do not only offer fixed products. We also take on custom fabrication work based on customer needs.
                </p>
            </div>

            <div class="value-card">
                <h3>Flexible Design</h3>
                <p>
                    Product dimensions, colors, finishes, and specifications can be adjusted to better match each request.
                </p>
            </div>

            <div class="value-card">
                <h3>Practical Experience</h3>
                <p>
                    Our work is based on actual fabrication jobs and completed projects, not only catalog-style display pieces.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container cta-box">
        <div>
            <h2>Need a Custom Metal Build?</h2>
            <p>
                Contact us to discuss your product idea, fabrication requirement, or bulk order.
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