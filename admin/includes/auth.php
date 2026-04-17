<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!admin_is_logged_in()) {
    set_flash('error', 'Please log in to continue.');
    redirect(ADMIN_URL . 'login.php');
}

if (is_admin_session_expired()) {
    session_unset();
    session_destroy();
    session_start();

    set_flash('error', 'Your session has expired. Please log in again.');
    redirect(ADMIN_URL . 'login.php');
}

$stmt = $pdo->prepare("SELECT id, full_name, email, created_at FROM admins WHERE id = ? LIMIT 1");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

if (!$admin) {
    session_unset();
    session_destroy();
    session_start();

    set_flash('error', 'Admin account not found. Please log in again.');
    redirect(ADMIN_URL . 'login.php');
}

refresh_admin_activity();