<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-block">
            <h3><?php echo e($siteSettings['site_name'] ?: 'Welding Company'); ?></h3>
            <p>
                Custom welding and metal fabrication for homes, businesses, and project-based work.
            </p>
        </div>

        <div class="footer-block">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>products.php">Products</a></li>
                <li><a href="<?php echo BASE_URL; ?>portfolio.php">Portfolio</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php">Contact</a></li>
            </ul>
        </div>

        <div class="footer-block">
            <h4>Contact</h4>
            <ul>
                <?php if (!empty($siteSettings['phone'])): ?>
                    <li>Phone: <?php echo e($siteSettings['phone']); ?></li>
                <?php endif; ?>

                <?php if (!empty($siteSettings['whatsapp'])): ?>
                    <li>WhatsApp: <?php echo e($siteSettings['whatsapp']); ?></li>
                <?php endif; ?>

                <?php if (!empty($siteSettings['email'])): ?>
                    <li>Email: <?php echo e($siteSettings['email']); ?></li>
                <?php endif; ?>

                <?php if (!empty($siteSettings['address'])): ?>
                    <li><?php echo nl2br(e($siteSettings['address'])); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo e($siteSettings['site_name'] ?: 'Welding Company'); ?>. All rights reserved.</p>
    </div>
</footer>

<script src="<?php echo ASSETS_URL; ?>js/frontend/main.js"></script>
</body>
</html>