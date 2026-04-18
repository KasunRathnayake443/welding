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

function generate_portfolio_upload_file_name(string $originalName): string
{
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return uniqid('portfolio_', true) . '.' . $extension;
}

function allowed_inquiry_statuses(): array
{
    return ['new', 'contacted', 'closed'];
}

function is_valid_phone_text(string $value): bool
{
    return mb_strlen($value) <= 50;
}

function is_valid_whatsapp_text(string $value): bool
{
    return mb_strlen($value) <= 50;
}


function get_site_settings(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM site_settings ORDER BY id ASC LIMIT 1");
    $settings = $stmt->fetch();

    return $settings ?: [
        'site_name' => 'Welding Company',
        'phone' => '',
        'whatsapp' => '',
        'email' => '',
        'address' => '',
        'hero_title' => 'Custom Welding & Metal Fabrication',
        'hero_subtitle' => 'We build metal products and custom fabrication work for homes, businesses, and projects.'
    ];
}

function get_featured_products(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name,
               (
                   SELECT pi.image_path
                   FROM product_images pi
                   WHERE pi.product_id = p.id
                   ORDER BY pi.sort_order ASC, pi.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.status = 1 AND p.is_featured = 1
        ORDER BY p.id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_latest_products(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name,
               (
                   SELECT pi.image_path
                   FROM product_images pi
                   WHERE pi.product_id = p.id
                   ORDER BY pi.sort_order ASC, pi.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.status = 1
        ORDER BY p.id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_featured_portfolio_items(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare("
        SELECT pi.*,
               (
                   SELECT pim.image_path
                   FROM portfolio_images pim
                   WHERE pim.portfolio_item_id = pi.id
                   ORDER BY pim.sort_order ASC, pim.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM portfolio_items pi
        WHERE pi.status = 1 AND pi.is_featured = 1
        ORDER BY pi.id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_latest_portfolio_items(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare("
        SELECT pi.*,
               (
                   SELECT pim.image_path
                   FROM portfolio_images pim
                   WHERE pim.portfolio_item_id = pi.id
                   ORDER BY pim.sort_order ASC, pim.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM portfolio_items pi
        WHERE pi.status = 1
        ORDER BY pi.id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_whatsapp_link(?string $number, string $message = ''): string
{
    $cleanNumber = preg_replace('/[^0-9]/', '', (string)$number);

    if ($cleanNumber === '') {
        return '#';
    }

    $url = 'https://wa.me/' . $cleanNumber;

    if ($message !== '') {
        $url .= '?text=' . urlencode($message);
    }

    return $url;
}

function get_public_categories(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT c.*,
               (
                   SELECT COUNT(*)
                   FROM products p
                   WHERE p.category_id = c.id AND p.status = 1
               ) AS product_count
        FROM categories c
        WHERE c.status = 1
        ORDER BY c.name ASC
    ");
    return $stmt->fetchAll();
}

function get_public_products(PDO $pdo, string $search = '', int $categoryId = 0): array
{
    $sql = "
        SELECT p.*, c.name AS category_name,
               (
                   SELECT pi.image_path
                   FROM product_images pi
                   WHERE pi.product_id = p.id
                   ORDER BY pi.sort_order ASC, pi.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.status = 1
          AND c.status = 1
    ";

    $params = [];

    if ($categoryId > 0) {
        $sql .= " AND p.category_id = ? ";
        $params[] = $categoryId;
    }

    if ($search !== '') {
        $sql .= " AND (
            p.name LIKE ?
            OR p.short_description LIKE ?
            OR p.full_description LIKE ?
            OR c.name LIKE ?
        ) ";
        $searchLike = '%' . $search . '%';
        $params[] = $searchLike;
        $params[] = $searchLike;
        $params[] = $searchLike;
        $params[] = $searchLike;
    }

    $sql .= " ORDER BY p.is_featured DESC, p.id DESC ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function get_public_product_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.slug = ?
          AND p.status = 1
          AND c.status = 1
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();

    return $product ?: null;
}

function get_product_images(PDO $pdo, int $productId): array
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM product_images
        WHERE product_id = ?
        ORDER BY sort_order ASC, id ASC
    ");
    $stmt->execute([$productId]);

    return $stmt->fetchAll();
}

function get_product_category_properties_with_values(PDO $pdo, int $productId): array
{
    $stmt = $pdo->prepare("
        SELECT cp.property_name, cp.field_type, cp.sort_order, ppv.property_value
        FROM product_property_values ppv
        INNER JOIN category_properties cp ON cp.id = ppv.category_property_id
        WHERE ppv.product_id = ?
          AND cp.status = 1
          AND ppv.property_value IS NOT NULL
          AND ppv.property_value != ''
        ORDER BY cp.sort_order ASC, cp.id ASC
    ");
    $stmt->execute([$productId]);

    return $stmt->fetchAll();
}

function get_product_extra_properties(PDO $pdo, int $productId): array
{
    $stmt = $pdo->prepare("
        SELECT property_name, property_value, sort_order
        FROM product_extra_properties
        WHERE product_id = ?
          AND property_name IS NOT NULL
          AND property_name != ''
        ORDER BY sort_order ASC, id ASC
    ");
    $stmt->execute([$productId]);

    return $stmt->fetchAll();
}

function get_related_products(PDO $pdo, int $categoryId, int $excludeProductId, int $limit = 4): array
{
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name,
               (
                   SELECT pi.image_path
                   FROM product_images pi
                   WHERE pi.product_id = p.id
                   ORDER BY pi.sort_order ASC, pi.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        WHERE p.status = 1
          AND c.status = 1
          AND p.category_id = ?
          AND p.id != ?
        ORDER BY p.is_featured DESC, p.id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(2, $excludeProductId, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_public_portfolio_items(PDO $pdo, string $search = ''): array
{
    $sql = "
        SELECT pi.*,
               (
                   SELECT pim.image_path
                   FROM portfolio_images pim
                   WHERE pim.portfolio_item_id = pi.id
                   ORDER BY pim.sort_order ASC, pim.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM portfolio_items pi
        WHERE pi.status = 1
    ";

    $params = [];

    if ($search !== '') {
        $sql .= " AND (
            pi.title LIKE ?
            OR pi.short_description LIKE ?
            OR pi.full_description LIKE ?
        ) ";
        $searchLike = '%' . $search . '%';
        $params[] = $searchLike;
        $params[] = $searchLike;
        $params[] = $searchLike;
    }

    $sql .= " ORDER BY pi.is_featured DESC, pi.id DESC ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function get_public_portfolio_item_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM portfolio_items
        WHERE slug = ?
          AND status = 1
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $item = $stmt->fetch();

    return $item ?: null;
}

function get_portfolio_images(PDO $pdo, int $portfolioItemId): array
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM portfolio_images
        WHERE portfolio_item_id = ?
        ORDER BY sort_order ASC, id ASC
    ");
    $stmt->execute([$portfolioItemId]);

    return $stmt->fetchAll();
}

function get_related_portfolio_items(PDO $pdo, int $excludeId, int $limit = 4): array
{
    $stmt = $pdo->prepare("
        SELECT pi.*,
               (
                   SELECT pim.image_path
                   FROM portfolio_images pim
                   WHERE pim.portfolio_item_id = pi.id
                   ORDER BY pim.sort_order ASC, pim.id ASC
                   LIMIT 1
               ) AS primary_image
        FROM portfolio_items pi
        WHERE pi.status = 1
          AND pi.id != ?
        ORDER BY pi.is_featured DESC, pi.id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $excludeId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}