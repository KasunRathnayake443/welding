<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT i.*, p.name AS product_name
    FROM inquiries i
    LEFT JOIN products p ON p.id = i.product_id
    WHERE i.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$inquiry = $stmt->fetch();

if (!$inquiry) {
    set_flash('error', 'Inquiry not found.');
    redirect(ADMIN_URL . 'inquiries/index.php');
}

$pageTitle = 'View Inquiry - ' . APP_NAME;
$pageHeading = 'View Inquiry';
$pageSubheading = 'Review inquiry details and update the current status.';
$pageCssFiles = ['inquiries.css'];

$errors = [];

if (is_post_request()) {
    $status = trim($_POST['status'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if (!in_array($status, allowed_inquiry_statuses(), true)) {
        $errors[] = 'Invalid status selected.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        set_flash('success', 'Inquiry status updated successfully.');
        redirect(ADMIN_URL . 'inquiries/view.php?id=' . $id);
    }
}

$csrfToken = generate_csrf_token();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

        <div class="page-actions page-actions-left">
            <a href="<?php echo ADMIN_URL; ?>inquiries/index.php" class="btn btn-dark">← Back to Inquiries</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul class="alert-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="content-card details-card">
            <div class="content-card-header">
                <h3>Inquiry Details</h3>
            </div>

            <div class="details-grid">
                <div class="detail-row">
                    <span>ID</span>
                    <strong><?php echo e($inquiry['id']); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Name</span>
                    <strong><?php echo e($inquiry['name']); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Phone</span>
                    <strong><?php echo e($inquiry['phone']); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Email</span>
                    <strong><?php echo e($inquiry['email'] ?: '-'); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Product</span>
                    <strong><?php echo e($inquiry['product_name'] ?: '-'); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Subject</span>
                    <strong><?php echo e($inquiry['subject'] ?: '-'); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Created</span>
                    <strong><?php echo e(format_datetime($inquiry['created_at'])); ?></strong>
                </div>

                <div class="detail-row">
                    <span>Current Status</span>
                    <strong>
                        <span class="status-badge status-<?php echo e($inquiry['status']); ?>">
                            <?php echo e(ucfirst($inquiry['status'])); ?>
                        </span>
                    </strong>
                </div>
            </div>

            <div class="message-block">
                <h4>Message</h4>
                <div class="message-box">
                    <?php echo nl2br(e($inquiry['message'])); ?>
                </div>
            </div>

            <div class="status-update-block">
                <h4>Update Status</h4>

                <form method="POST" action="" class="admin-form inline-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" required>
                            <option value="new" <?php echo $inquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                            <option value="closed" <?php echo $inquiry['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>