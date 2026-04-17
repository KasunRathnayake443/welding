<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if (admin_is_logged_in() && !is_admin_session_expired()) {
    redirect(ADMIN_URL . 'dashboard.php');
}