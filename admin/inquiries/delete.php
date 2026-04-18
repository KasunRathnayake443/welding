<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if ($id <= 0) {
    set_flash('error', 'Invalid inquiry ID.');
    redirect(ADMIN_URL . 'inquiries/index.php');
}

if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'Invalid request token.');
    redirect(ADMIN_URL . 'inquiries/index.php');
}

$stmt = $pdo->prepare("SELECT id FROM inquiries WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$inquiry = $stmt->fetch();

if (!$inquiry) {
    set_flash('error', 'Inquiry not found.');
    redirect(ADMIN_URL . 'inquiries/index.php');
}

$stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
$stmt->execute([$id]);

set_flash('success', 'Inquiry deleted successfully.');
redirect(ADMIN_URL . 'inquiries/index.php');