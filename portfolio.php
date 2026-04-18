<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);

$search = trim($_GET['search'] ?? '');
$portfolioItems = get_public_portfolio_items($pdo, $search);

$pageTitle = 'Portfolio - ' . ($siteSettings['site_name'] ?: 'Welding Company');
$frontendCssFiles = ['portfolio.css'];

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <h1>Our Work</h1>
            <p>
                Browse some of the completed welding and metal fabrication work already handled by our team.
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="portfolio-layout">
            <aside class="portfolio-sidebar">
                <div class="sidebar-card">
                    <h3>Search Portfolio</h3>

                    <form method="GET" action="" class="public-filter-form">
                        <div class="form-group">
                            <label for="search">Keyword</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="<?php echo e($search); ?>"
                                placeholder="Search projects"
                            >
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?php echo BASE_URL; ?>portfolio.php" class="btn btn-outline">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="sidebar-card">
                    <h3>Custom Work</h3>
                    <p>
                        We handle a wide range of custom metal fabrication work, from decorative items and furniture to structural and project-based jobs.
                    </p>
                </div>
            </aside>

            <div class="portfolio-content">
                <div class="portfolio-toolbar">
                    <div>
                        <h2>Completed Projects</h2>
                        <p><?php echo count($portfolioItems); ?> item(s) found</p>
                    </div>
                </div>

                <?php if (!empty($portfolioItems)): ?>
                    <div class="card-grid">
                        <?php foreach ($portfolioItems as $item): ?>
                            <article class="common-card">
                                <div class="common-card-image">
                                    <?php if (!empty($item['primary_image'])): ?>
                                        <img src="<?php echo UPLOADS_URL . 'portfolio/' . e($item['primary_image']); ?>" alt="<?php echo e($item['title']); ?>">
                                    <?php else: ?>
                                        <div class="image-fallback">No Image</div>
                                    <?php endif; ?>
                                </div>

                                <div class="common-card-body">
                                    <div class="common-meta">
                                        <?php if ((int)$item['is_featured'] === 1): ?>
                                            <span>Featured Work</span>
                                        <?php endif; ?>
                                    </div>

                                    <h3><?php echo e($item['title']); ?></h3>

                                    <p>
                                        <?php echo e($item['short_description'] ?: 'Completed metal fabrication project.'); ?>
                                    </p>

                                    <a href="<?php echo BASE_URL; ?>portfolio-item.php?slug=<?php echo urlencode($item['slug']); ?>" class="btn btn-outline">
                                        View Project
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-public-state">
                        <p>No portfolio items found for the selected search.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>