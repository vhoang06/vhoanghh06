<?php
// includes/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');           
define('DB_PASS', '');               
define('DB_NAME', 'office_supplies');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
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
    header("Location: $url");
    exit;
}

if (file_exists(__DIR__ . '/smtp.local.php')) {
    include __DIR__ . '/smtp.local.php';
}

// Mail / PHPMailer configuration (optional)
// If you want to send via SMTP, copy includes/smtp.example.php to includes/smtp.local.php
// and fill your credentials. By default SMTP is disabled.
if (!defined('MAIL_USE_SMTP')) define('MAIL_USE_SMTP', false);
if (!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.gmail.com');
if (!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if (!defined('MAIL_USER')) define('MAIL_USER', 'your@email.com');
if (!defined('MAIL_PASS')) define('MAIL_PASS', 'your_smtp_password');
if (!defined('MAIL_FROM')) define('MAIL_FROM', 'no-reply@localhost');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'Office Supplies');