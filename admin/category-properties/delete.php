<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if ($id <= 0) {
    set_flash('error', 'Invalid property ID.');
    redirect(ADMIN_URL . 'category-properties/index.php');
}

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Invalid request token.');
    redirect(ADMIN_URL . 'category-properties/index.php');
}

$stmt = $pdo->prepare("SELECT * FROM category_properties WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$property = $stmt->fetch();

if (!$property) {
    set_flash('error', 'Category property not found.');
    redirect(ADMIN_URL . 'category-properties/index.php');
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM product_property_values WHERE category_property_id = ?");
$stmt->execute([$id]);
$usageCount = (int)$stmt->fetchColumn();

if ($usageCount > 0) {
    set_flash('error', 'This property cannot be deleted because it is already used in products.');
    redirect(ADMIN_URL . 'category-properties/index.php?category_id=' . (int)$property['category_id']);
}

$stmt = $pdo->prepare("DELETE FROM category_properties WHERE id = ?");
$stmt->execute([$id]);

set_flash('success', 'Category property deleted successfully.');
redirect(ADMIN_URL . 'category-properties/index.php?category_id=' . (int)$property['category_id']);