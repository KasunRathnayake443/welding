<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);

$slug = trim($_GET['slug'] ?? '');
$item = $slug !== '' ? get_public_portfolio_item_by_slug($pdo, $slug) : null;

if (!$item) {
    http_response_code(404);
    $pageTitle = 'Project Not Found - ' . ($siteSettings['site_name'] ?: 'Welding Company');
    $frontendCssFiles = ['portfolio-item.css'];
    require_once __DIR__ . '/includes/frontend-header.php';
    ?>
    <section class="section">
        <div class="container">
            <div class="empty-public-state">
                <p>Portfolio item not found.</p>
                <br>
                <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn btn-primary">Back to Portfolio</a>
            </div>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/frontend-footer.php';
    exit;
}

$portfolioImages = get_portfolio_images($pdo, (int)$item['id']);
$relatedItems = get_related_portfolio_items($pdo, (int)$item['id'], 4);

$pageTitle = $item['title'] . ' - ' . ($siteSettings['site_name'] ?: 'Welding Company');
$frontendCssFiles = ['portfolio-item.css'];

$whatsAppMessage = "Hi, I saw your project: " . $item['title'] . ". I would like to discuss a similar custom job.";
$whatsAppLink = get_whatsapp_link($siteSettings['whatsapp'] ?? '', $whatsAppMessage);

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="page-hero small-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>">Home</a>
            <span>/</span>
            <a href="<?php echo BASE_URL; ?>portfolio.php">Portfolio</a>
            <span>/</span>
            <span><?php echo e($item['title']); ?></span>
        </div>
    </div>
</section>

<section class="section portfolio-detail-section">
    <div class="container">
        <div class="portfolio-detail-grid">
            <div class="portfolio-gallery">
                <?php if (!empty($portfolioImages)): ?>
                    <div class="main-portfolio-image">
                        <img
                            id="mainPortfolioImage"
                            src="<?php echo UPLOADS_URL . 'portfolio/' . e($portfolioImages[0]['image_path']); ?>"
                            alt="<?php echo e($item['title']); ?>"
                        >
                    </div>

                    <?php if (count($portfolioImages) > 1): ?>
                        <div class="thumbnail-row">
                            <?php foreach ($portfolioImages as $index => $image): ?>
                                <button
                                    type="button"
                                    class="thumbnail-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                                    data-image="<?php echo UPLOADS_URL . 'portfolio/' . e($image['image_path']); ?>"
                                >
                                    <img src="<?php echo UPLOADS_URL . 'portfolio/' . e($image['image_path']); ?>" alt="<?php echo e($item['title']); ?>">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="main-portfolio-image no-image-box">
                        <span>No Image Available</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="portfolio-info">
                <div class="common-meta detail-meta">
                    <?php if ((int)$item['is_featured'] === 1): ?>
                        <span>Featured Work</span>
                    <?php endif; ?>
                    <span>Completed Project</span>
                </div>

                <h1><?php echo e($item['title']); ?></h1>

                <?php if (!empty($item['short_description'])): ?>
                    <p class="portfolio-short-description"><?php echo e($item['short_description']); ?></p>
                <?php endif; ?>

                <?php if (!empty($item['full_description'])): ?>
                    <div class="portfolio-description">
                        <h3>Project Details</h3>
                        <p><?php echo nl2br(e($item['full_description'])); ?></p>
                    </div>
                <?php endif; ?>

                <div class="portfolio-cta-box">
                    <h3>Need Similar Work?</h3>
                    <p>
                        If you need a similar custom metal fabrication job, contact us with your requirement and we’ll discuss the details.
                    </p>

                    <div class="portfolio-actions">
                        <?php if ($whatsAppLink !== '#'): ?>
                            <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-primary">
                                Ask on WhatsApp
                            </a>
                        <?php endif; ?>

                        <a href="<?php echo BASE_URL; ?>contact.php?project=<?php echo urlencode($item['title']); ?>" class="btn btn-outline">
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="customization-note">
            <h3>Custom Metal Work Available</h3>
            <p>
                This portfolio item represents work we have completed before. Similar projects can be discussed and adapted based on your location, size, material, finish, and installation needs.
            </p>
        </div>

        <?php if (!empty($relatedItems)): ?>
            <div class="related-portfolio-section">
                <div class="section-heading">
                    <h2>More Projects</h2>
                    <p>Browse more of our completed work.</p>
                </div>

                <div class="card-grid">
                    <?php foreach ($relatedItems as $relatedItem): ?>
                        <article class="common-card">
                            <div class="common-card-image">
                                <?php if (!empty($relatedItem['primary_image'])): ?>
                                    <img src="<?php echo UPLOADS_URL . 'portfolio/' . e($relatedItem['primary_image']); ?>" alt="<?php echo e($relatedItem['title']); ?>">
                                <?php else: ?>
                                    <div class="image-fallback">No Image</div>
                                <?php endif; ?>
                            </div>

                            <div class="common-card-body">
                                <div class="common-meta">
                                    <?php if ((int)$relatedItem['is_featured'] === 1): ?>
                                        <span>Featured Work</span>
                                    <?php endif; ?>
                                </div>

                                <h3><?php echo e($relatedItem['title']); ?></h3>

                                <p>
                                    <?php echo e($relatedItem['short_description'] ?: 'Completed metal fabrication project.'); ?>
                                </p>

                                <a href="<?php echo BASE_URL; ?>portfolio-item.php?slug=<?php echo urlencode($relatedItem['slug']); ?>" class="btn btn-outline">
                                    View Project
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
    const mainImage = document.getElementById('mainPortfolioImage');
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