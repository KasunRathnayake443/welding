<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if ($id <= 0) {
    set_flash('error', 'Invalid image ID.');
    redirect(ADMIN_URL . 'product-images/index.php');
}

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Invalid request token.');
    redirect(ADMIN_URL . 'product-images/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM product_images WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$image = $stmt->fetch();

if (!$image) {
    set_flash('error', 'Product image not found.');
    redirect(ADMIN_URL . 'product-images/index.php');
}

$stmt = $pdo->prepare("DELETE FROM product_images WHERE id = ?");
$stmt->execute([$id]);

delete_file_if_exists(PRODUCT_UPLOADS_PATH . $image['image_path']);

set_flash('success', 'Product image deleted successfully.');
redirect(ADMIN_URL . 'product-images/index.php?product_id=' . (int)$image['product_id']);