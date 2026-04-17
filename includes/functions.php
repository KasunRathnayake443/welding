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