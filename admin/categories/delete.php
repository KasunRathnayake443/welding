<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if ($id <= 0) {
    set_flash('error', 'Invalid category ID.');
    redirect(ADMIN_URL . 'categories/index.php');
}

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Invalid request token.');
    redirect(ADMIN_URL . 'categories/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    set_flash('error', 'Category not found.');
    redirect(ADMIN_URL . 'categories/index.php');
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmt->execute([$id]);
$productCount = (int) $stmt->fetchColumn();

if ($productCount > 0) {
    set_flash('error', 'This category cannot be deleted because products are assigned to it.');
    redirect(ADMIN_URL . 'categories/index.php');
}

$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);

set_flash('success', 'Category deleted successfully.');
redirect(ADMIN_URL . 'categories/index.php');