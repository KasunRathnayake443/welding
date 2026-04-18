<?php
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Site Settings - ' . APP_NAME;
$pageHeading = 'Site Settings';
$pageSubheading = 'Manage core business details and homepage hero content.';
$pageCssFiles = ['settings.css'];

$successMessage = get_flash('success');
$errorMessage = get_flash('error');

$errors = [];

// Load existing single settings row
$stmt = $pdo->query("SELECT * FROM site_settings ORDER BY id ASC LIMIT 1");
$settings = $stmt->fetch();

$settingsId = $settings['id'] ?? 0;
$siteName = $settings['site_name'] ?? '';
$phone = $settings['phone'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$email = $settings['email'] ?? '';
$address = $settings['address'] ?? '';
$heroTitle = $settings['hero_title'] ?? '';
$heroSubtitle = $settings['hero_subtitle'] ?? '';

if (is_post_request()) {
    $siteName = trim($_POST['site_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $heroTitle = trim($_POST['hero_title'] ?? '');
    $heroSubtitle = trim($_POST['hero_subtitle'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($siteName === '') {
        $errors[] = 'Site name is required.';
    } elseif (mb_strlen($siteName) > 200) {
        $errors[] = 'Site name must not exceed 200 characters.';
    }

    if ($phone !== '' && !is_valid_phone_text($phone)) {
        $errors[] = 'Phone must not exceed 50 characters.';
    }

    if ($whatsapp !== '' && !is_valid_whatsapp_text($whatsapp)) {
        $errors[] = 'WhatsApp must not exceed 50 characters.';
    }

    if ($email !== '') {
        if (!is_valid_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (mb_strlen($email) > 150) {
            $errors[] = 'Email must not exceed 150 characters.';
        }
    }

    if ($heroTitle !== '' && mb_strlen($heroTitle) > 255) {
        $errors[] = 'Hero title must not exceed 255 characters.';
    }

    if (empty($errors)) {
        if ($settingsId > 0) {
            $stmt = $pdo->prepare("
                UPDATE site_settings
                SET site_name = ?, phone = ?, whatsapp = ?, email = ?, address = ?, hero_title = ?, hero_subtitle = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $siteName,
                $phone !== '' ? $phone : null,
                $whatsapp !== '' ? $whatsapp : null,
                $email !== '' ? $email : null,
                $address !== '' ? $address : null,
                $heroTitle !== '' ? $heroTitle : null,
                $heroSubtitle !== '' ? $heroSubtitle : null,
                $settingsId
            ]);

            set_flash('success', 'Site settings updated successfully.');
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO site_settings
                (site_name, phone, whatsapp, email, address, hero_title, hero_subtitle)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $siteName,
                $phone !== '' ? $phone : null,
                $whatsapp !== '' ? $whatsapp : null,
                $email !== '' ? $email : null,
                $address !== '' ? $address : null,
                $heroTitle !== '' ? $heroTitle : null,
                $heroSubtitle !== '' ? $heroSubtitle : null
            ]);

            set_flash('success', 'Site settings created successfully.');
        }

        redirect(ADMIN_URL . 'settings/index.php');
    }
}

$csrfToken = generate_csrf_token();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <?php require_once __DIR__ . '/../includes/topbar.php'; ?>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo e($successMessage); ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-error"><?php echo e($errorMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul class="alert-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="content-card settings-card">
            <div class="content-card-header">
                <h3>Business Information</h3>
            </div>

            <form method="POST" action="" class="admin-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="site_name">Site Name <span>*</span></label>
                        <input
                            type="text"
                            id="site_name"
                            name="site_name"
                            maxlength="200"
                            value="<?php echo e($siteName); ?>"
                            placeholder="Enter site name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            maxlength="50"
                            value="<?php echo e($phone); ?>"
                            placeholder="Enter phone number"
                        >
                    </div>

                    <div class="form-group">
                        <label for="whatsapp">WhatsApp</label>
                        <input
                            type="text"
                            id="whatsapp"
                            name="whatsapp"
                            maxlength="50"
                            value="<?php echo e($whatsapp); ?>"
                            placeholder="Enter WhatsApp number"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="150"
                            value="<?php echo e($email); ?>"
                            placeholder="Enter email address"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea
                        id="address"
                        name="address"
                        placeholder="Enter business address"><?php echo e($address); ?></textarea>
                </div>

                <div class="section-block">
                    <div class="section-block-header">
                        <h4>Homepage Hero Content</h4>
                    </div>

                    <div class="form-group">
                        <label for="hero_title">Hero Title</label>
                        <input
                            type="text"
                            id="hero_title"
                            name="hero_title"
                            maxlength="255"
                            value="<?php echo e($heroTitle); ?>"
                            placeholder="Enter hero title"
                        >
                    </div>

                    <div class="form-group">
                        <label for="hero_subtitle">Hero Subtitle</label>
                        <textarea
                            id="hero_subtitle"
                            name="hero_subtitle"
                            placeholder="Enter hero subtitle"><?php echo e($heroSubtitle); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $settingsId > 0 ? 'Update Settings' : 'Save Settings'; ?>
                </button>
            </form>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>