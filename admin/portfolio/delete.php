<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if ($id <= 0) {
    set_flash('error', 'Invalid portfolio item ID.');
    redirect(ADMIN_URL . 'portfolio/index.php');
}

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Invalid request token.');
    redirect(ADMIN_URL . 'portfolio/index.php');
}

$stmt = $pdo->prepare("SELECT id FROM portfolio_items WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    set_flash('error', 'Portfolio item not found.');
    redirect(ADMIN_URL . 'portfolio/index.php');
}

$stmt = $pdo->prepare("DELETE FROM portfolio_items WHERE id = ?");
$stmt->execute([$id]);

set_flash('success', 'Portfolio item deleted successfully.');
redirect(ADMIN_URL . 'portfolio/index.php');