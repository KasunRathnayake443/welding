<?php

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

function is_post_request(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return isset($_SESSION['csrf_token']) &&
        is_string($token) &&
        hash_equals($_SESSION['csrf_token'], $token);
}

function is_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function admin_is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function refresh_admin_activity(): void
{
    $_SESSION['last_activity'] = time();
}

function is_admin_session_expired(): bool
{
    if (empty($_SESSION['last_activity'])) {
        return false;
    }

    return (time() - (int)$_SESSION['last_activity']) > ADMIN_SESSION_TIMEOUT;
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function format_datetime(?string $datetime): string
{
    if (!$datetime) {
        return '-';
    }

    return date('M d, Y h:i A', strtotime($datetime));
}

function slugify(string $text): string
{
    $text = trim($text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function category_slug_exists(PDO $pdo, string $slug, int $ignoreId = 0): bool
{
    if ($ignoreId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$slug, $ignoreId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
    }

    return (bool) $stmt->fetch();
}

function allowed_property_field_types(): array
{
    return ['text', 'textarea', 'number', 'select'];
}

function is_valid_status_value($value): bool
{
    return in_array((int)$value, [0, 1], true);
}

function get_active_categories(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT id, name, status FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

function get_category_properties(PDO $pdo, int $categoryId, bool $onlyActive = false): array
{
    $sql = "SELECT * FROM category_properties WHERE category_id = ?";
    if ($onlyActive) {
        $sql .= " AND status = 1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll();
}

function product_slug_exists(PDO $pdo, string $slug, int $ignoreId = 0): bool
{
    if ($ignoreId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$slug, $ignoreId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
    }

    return (bool)$stmt->fetch();
}

function ensure_directory_exists(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

function allowed_image_extensions(): array
{
    return ['jpg', 'jpeg', 'png', 'webp'];
}

function allowed_image_mime_types(): array
{
    return ['image/jpeg', 'image/png', 'image/webp'];
}

function is_valid_uploaded_image(array $file): array
{
    $errors = [];

    if (!isset($file['error']) || is_array($file['error'])) {
        $errors[] = 'Invalid file upload.';
        return $errors;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed.';
        return $errors;
    }

    if (($file['size'] ?? 0) <= 0) {
        $errors[] = 'Uploaded file is empty.';
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        $errors[] = 'Image must not exceed 5MB.';
    }

    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($extension, allowed_image_extensions(), true)) {
        $errors[] = 'Only JPG, JPEG, PNG, and WEBP images are allowed.';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, allowed_image_mime_types(), true)) {
        $errors[] = 'Invalid image type uploaded.';
    }

    return $errors;
}

function generate_upload_file_name(string $originalName): string
{
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return uniqid('product_', true) . '.' . $extension;
}

function delete_file_if_exists(string $absolutePath): void
{
    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }
}

function portfolio_slug_exists(PDO $pdo, string $slug, int $ignoreId = 0): bool
{
    if ($ignoreId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM portfolio_items WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$slug, $ignoreId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM portfolio_items WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
    }

    return (bool)$stmt->fetch();
}