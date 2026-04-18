<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$siteSettings = get_site_settings($pdo);
$products = get_contactable_products($pdo);

$pageTitle = 'Contact - ' . ($siteSettings['site_name'] ?: 'Welding Company');
$frontendCssFiles = ['contact.css'];

$successMessage = null;
$errors = [];

$name = '';
$phone = '';
$email = '';
$subject = '';
$message = '';
$productId = 0;

// Optional prefills from product/project links
$prefillProductName = trim($_GET['product'] ?? '');
$prefillProjectName = trim($_GET['project'] ?? '');

if ($prefillProductName !== '') {
    $matchedProduct = get_product_by_name($pdo, $prefillProductName);
    if ($matchedProduct) {
        $productId = (int)$matchedProduct['id'];
        $subject = 'Inquiry about product: ' . $matchedProduct['name'];
    } else {
        $subject = 'Inquiry about product: ' . $prefillProductName;
    }
} elseif ($prefillProjectName !== '') {
    $subject = 'Inquiry about similar work: ' . $prefillProjectName;
}

if (is_post_request()) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $productId = (int)($_POST['product_id'] ?? 0);
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($name === '') {
        $errors[] = 'Your name is required.';
    } elseif (mb_strlen($name) > 150) {
        $errors[] = 'Name must not exceed 150 characters.';
    }

    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    } elseif (mb_strlen($phone) > 50) {
        $errors[] = 'Phone number must not exceed 50 characters.';
    }

    if ($email !== '') {
        if (!is_valid_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (mb_strlen($email) > 150) {
            $errors[] = 'Email must not exceed 150 characters.';
        }
    }

    if ($subject !== '' && mb_strlen($subject) > 200) {
        $errors[] = 'Subject must not exceed 200 characters.';
    }

    if ($message === '') {
        $errors[] = 'Message is required.';
    }

    if ($productId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND status = 1 LIMIT 1");
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            $errors[] = 'Selected product does not exist.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO inquiries (name, phone, email, subject, message, product_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name,
            $phone,
            $email !== '' ? $email : null,
            $subject !== '' ? $subject : null,
            $message,
            $productId > 0 ? $productId : null,
            'new'
        ]);

        $successMessage = 'Your inquiry has been sent successfully. We will contact you soon.';

        // reset form
        $name = '';
        $phone = '';
        $email = '';
        $subject = '';
        $message = '';
        $productId = 0;
    }
}

$csrfToken = generate_csrf_token();
$whatsAppLink = get_whatsapp_link($siteSettings['whatsapp'] ?? '', 'Hi, I would like to ask about your metal work services.');

require_once __DIR__ . '/includes/frontend-header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <h1>Contact Us</h1>
            <p>
                Get in touch for custom welding work, product inquiries, fabrication projects, and bulk orders.
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info-panel">
                <div class="section-heading">
                    <h2>Let’s Discuss Your Requirement</h2>
                    <p>
                        Contact us if you need custom metal products, fabrication work, or want to ask about any design shown on the website.
                    </p>
                </div>

                <div class="contact-info-grid">
                    <div class="contact-info-card">
                        <h3>Phone</h3>
                        <p><?php echo e($siteSettings['phone'] ?: 'Not added yet'); ?></p>
                    </div>

                    <div class="contact-info-card">
                        <h3>WhatsApp</h3>
                        <?php if (!empty($siteSettings['whatsapp'])): ?>
                            <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener">
                                <?php echo e($siteSettings['whatsapp']); ?>
                            </a>
                        <?php else: ?>
                            <p>Not added yet</p>
                        <?php endif; ?>
                    </div>

                    <div class="contact-info-card">
                        <h3>Email</h3>
                        <p><?php echo e($siteSettings['email'] ?: 'Not added yet'); ?></p>
                    </div>

                    <div class="contact-info-card">
                        <h3>Address</h3>
                        <p><?php echo !empty($siteSettings['address']) ? nl2br(e($siteSettings['address'])) : 'Not added yet'; ?></p>
                    </div>
                </div>
            </div>

            <div class="contact-form-panel">
                <?php if ($successMessage): ?>
                    <div class="public-alert success"><?php echo e($successMessage); ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="public-alert error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="contact-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Your Name <span>*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                maxlength="150"
                                value="<?php echo e($name); ?>"
                                placeholder="Enter your name"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number <span>*</span></label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                maxlength="50"
                                value="<?php echo e($phone); ?>"
                                placeholder="Enter your phone number"
                                required
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
                                placeholder="Enter your email address"
                            >
                        </div>

                        <div class="form-group">
                            <label for="product_id">Related Product</label>
                            <select name="product_id" id="product_id">
                                <option value="">Select Product (Optional)</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo (int)$product['id']; ?>" <?php echo $productId === (int)$product['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            maxlength="200"
                            value="<?php echo e($subject); ?>"
                            placeholder="Enter subject"
                        >
                    </div>

                    <div class="form-group">
                        <label for="message">Message <span>*</span></label>
                        <textarea
                            id="message"
                            name="message"
                            placeholder="Tell us what you need"><?php echo e($message); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Send Inquiry</button>

                        <?php if ($whatsAppLink !== '#'): ?>
                            <a href="<?php echo e($whatsAppLink); ?>" target="_blank" rel="noopener" class="btn btn-dark">
                                Chat on WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/frontend-footer.php'; ?>