<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Portfolio - ' . APP_NAME;
$pageHeading = 'Portfolio';
$pageSubheading = 'Manage completed work entries and showcased fabrication projects.';
$pageCssFiles = ['portfolio.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$search = trim($_GET['search'] ?? '');
$featuredFilter = isset($_GET['featured']) ? $_GET['featured'] : '';

$sql = "
    SELECT pi.*,
           (
               SELECT COUNT(*)
               FROM portfolio_images pim
               WHERE pim.portfolio_item_id = pi.id
           ) AS image_count
    FROM portfolio_items pi
    WHERE 1 = 1
";

$params = [];

if ($search !== '') {
    $sql .= " AND (pi.title LIKE ? OR pi.slug LIKE ? OR pi.short_description LIKE ?) ";
    $searchLike = '%' . $search . '%';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
}

if ($featuredFilter === '1' || $featuredFilter === '0') {
    $sql .= " AND pi.is_featured = ? ";
    $params[] = (int)$featuredFilter;
}

$sql .= " ORDER BY pi.id DESC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo e($successMessage); ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-error"><?php echo e($errorMessage); ?></div>
        <?php endif; ?>

        <div class="page-toolbar">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="<?php echo e($search); ?>"
                        placeholder="Search portfolio items"
                    >
                </div>

                <div class="filter-group">
                    <label for="featured">Featured</label>
                    <select name="featured" id="featured">
                        <option value="">All</option>
                        <option value="1" <?php echo $featuredFilter === '1' ? 'selected' : ''; ?>>Featured Only</option>
                        <option value="0" <?php echo $featuredFilter === '0' ? 'selected' : ''; ?>>Non-featured</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="<?php echo ADMIN_URL; ?>portfolio/index.php" class="btn btn-light">Reset</a>
            </form>

            <a href="<?php echo ADMIN_URL; ?>portfolio/create.php" class="btn btn-primary">Add Portfolio Item</a>
        </div>

        <section class="content-card">
            <div class="content-card-header">
                <h3>All Portfolio Items</h3>
            </div>

            <?php if (!empty($items)): ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="70">ID</th>
                                <th width="200">Title</th>
                                <th width="180">Slug</th>
                                <th>Short Description</th>
                                <th width="90">Images</th>
                                <th width="90">Featured</th>
                                <th width="100">Status</th>
                                <th width="180">Created</th>
                                <th width="190">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo e($item['id']); ?></td>
                                    <td><?php echo e($item['title']); ?></td>
                                    <td><?php echo e($item['slug']); ?></td>
                                    <td><?php echo e($item['short_description'] ?: '-'); ?></td>
                                    <td><?php echo e($item['image_count']); ?></td>
                                    <td><?php echo (int)$item['is_featured'] === 1 ? 'Yes' : 'No'; ?></td>
                                    <td>
                                        <?php if ((int)$item['status'] === 1): ?>
                                            <span class="status-badge active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(format_datetime($item['created_at'])); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="<?php echo ADMIN_URL; ?>portfolio/edit.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-dark btn-sm">Edit</a>
                                            <a href="<?php echo ADMIN_URL; ?>portfolio/delete.php?id=<?php echo (int)$item['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this portfolio item?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No portfolio items found yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>