<?php
/**
 * Central config - DB connection, session, helpers
 * Được include bởi header.php và các admin pages
 */

// Load environment variables
require_once __DIR__ . '/env.php';

// DB credentials từ env hoặc fallback
// DB credentials từ env hoặc fallback
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'office_supplies');

// Mail defaults
if (!defined('MAIL_USE_SMTP')) define('MAIL_USE_SMTP', false);
if (!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.gmail.com');
if (!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if (!defined('MAIL_USER')) define('MAIL_USER', 'your@email.com');
if (!defined('MAIL_PASS')) define('MAIL_PASS', 'your_smtp_password');
if (!defined('MAIL_FROM')) define('MAIL_FROM', 'no-reply@localhost');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'Office Supplies');

// App settings
if (!defined('APP_URL')) define('APP_URL', 'http://localhost/vhoang_repo');
if (!defined('APP_NAME')) define('APP_NAME', 'Office Supplies');

// Note: Database connection is now handled by App\Core\Database class.
// The legacy $pdo connection here has been removed to avoid conflicts.

// Session
if (session_status() === PHP_SESSION_NONE) {
    // Security: httponly + samesite
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Load helpers
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/mailer.php';

// ==================== AUTH HELPERS ====================

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isEmailVerified() {
    return isset($_SESSION['email_verified']) && $_SESSION['email_verified'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('index.php?route=login');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../index.php");
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function redirect($url) {
    // Đảm bảo không redirectLoop
    if (!headers_sent()) {
        header("Location: $url");
        exit;
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}

// Load SMTP local overrides nếu có
if (file_exists(__DIR__ . '/smtp.local.php')) {
    include __DIR__ . '/smtp.local.php';
}
