<?php

define('APP_NAME', 'Welding Site Admin');
define('BASE_URL', 'http://localhost/welding/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_URL', BASE_URL . 'uploads/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/');
define('PRODUCT_UPLOADS_PATH', UPLOADS_PATH . 'products/');
define('PORTFOLIO_UPLOADS_PATH', UPLOADS_PATH . 'portfolio/');

define('ADMIN_SESSION_TIMEOUT', 7200); // 2 hours

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}