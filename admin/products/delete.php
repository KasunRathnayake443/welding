<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if ($id <= 0) {
    set_flash('error', 'Invalid product ID.');
    redirect(ADMIN_URL . 'products/index.php');
}

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Invalid request token.');
    redirect(ADMIN_URL . 'products/index.php');
}

$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Product not found.');
    redirect(ADMIN_URL . 'products/index.php');
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

set_flash('success', 'Product deleted successfully.');
redirect(ADMIN_URL . 'products/index.php');