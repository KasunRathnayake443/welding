<?php
require_once __DIR__ . '/includes/guest.php';

$pageTitle = 'Admin Login - ' . APP_NAME;
$pageCssFiles = ['login.css'];

$errors = [];
$email = '';

if (is_post_request()) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errors[] = 'Invalid request. Please refresh the page and try again.';
    }

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!is_valid_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (mb_strlen($email) > 150) {
        $errors[] = 'Email address is too long.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, full_name, email, password FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $adminUser = $stmt->fetch();

        if (!$adminUser || !password_verify($password, $adminUser['password'])) {
            $errors[] = 'Invalid email or password.';
        } else {
            session_regenerate_id(true);

            $_SESSION['admin_id'] = (int)$adminUser['id'];
            $_SESSION['admin_name'] = $adminUser['full_name'];
            $_SESSION['last_activity'] = time();

            set_flash('success', 'Login successful. Welcome back.');
            redirect(ADMIN_URL . 'dashboard.php');
        }
    }
}

$flashError = get_flash('error');
$flashSuccess = get_flash('success');
$csrfToken = generate_csrf_token();

require_once __DIR__ . '/includes/header.php';
?>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Sign in to manage products, portfolio items, inquiries, and website settings.</p>
        </div>

        <?php if ($flashError): ?>
            <div class="alert alert-error"><?php echo e($flashError); ?></div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?>
            <div class="alert alert-success"><?php echo e($flashSuccess); ?></div>
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

        <form method="POST" action="" class="admin-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">

            <div class="form-group">
                <label for="email">Email Address <span>*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo e($email); ?>"
                    placeholder="Enter your email address"
                    maxlength="150"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password <span>*</span></label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>