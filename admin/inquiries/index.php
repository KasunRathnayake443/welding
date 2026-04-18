<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Inquiries - ' . APP_NAME;
$pageHeading = 'Inquiries';
$pageSubheading = 'View and manage customer inquiries from the website.';
$pageCssFiles = ['inquiries.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$sql = "
    SELECT i.*, p.name AS product_name
    FROM inquiries i
    LEFT JOIN products p ON p.id = i.product_id
    WHERE 1 = 1
";

$params = [];

if ($search !== '') {
    $sql .= " AND (
        i.name LIKE ?
        OR i.phone LIKE ?
        OR i.email LIKE ?
        OR i.subject LIKE ?
        OR i.message LIKE ?
        OR p.name LIKE ?
    ) ";
    $searchLike = '%' . $search . '%';
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
}

if ($statusFilter !== '' && in_array($statusFilter, allowed_inquiry_statuses(), true)) {
    $sql .= " AND i.status = ? ";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY i.id DESC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

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
                        placeholder="Search inquiries"
                    >
                </div>

                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">All</option>
                        <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="contacted" <?php echo $statusFilter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                        <option value="closed" <?php echo $statusFilter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="<?php echo ADMIN_URL; ?>inquiries/index.php" class="btn btn-light">Reset</a>
            </form>
        </div>

        <section class="content-card">
            <div class="content-card-header">
                <h3>All Inquiries</h3>
            </div>

            <?php if (!empty($inquiries)): ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="70">ID</th>
                                <th width="160">Name</th>
                                <th width="140">Phone</th>
                                <th width="180">Email</th>
                                <th width="180">Product</th>
                                <th width="160">Subject</th>
                                <th>Message</th>
                                <th width="110">Status</th>
                                <th width="170">Created</th>
                                <th width="210">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo e($inquiry['id']); ?></td>
                                    <td><?php echo e($inquiry['name']); ?></td>
                                    <td><?php echo e($inquiry['phone']); ?></td>
                                    <td><?php echo e($inquiry['email'] ?: '-'); ?></td>
                                    <td><?php echo e($inquiry['product_name'] ?: '-'); ?></td>
                                    <td><?php echo e($inquiry['subject'] ?: '-'); ?></td>
                                    <td><?php echo e(mb_strimwidth($inquiry['message'], 0, 80, '...')); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo e($inquiry['status']); ?>">
                                            <?php echo e(ucfirst($inquiry['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo e(format_datetime($inquiry['created_at'])); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="<?php echo ADMIN_URL; ?>inquiries/view.php?id=<?php echo (int)$inquiry['id']; ?>" class="btn btn-dark btn-sm">View</a>
                                            <a href="<?php echo ADMIN_URL; ?>inquiries/delete.php?id=<?php echo (int)$inquiry['id']; ?>&csrf_token=<?php echo e(generate_csrf_token()); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this inquiry?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No inquiries found yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>