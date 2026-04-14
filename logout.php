<?php
// logout.php
require_once 'includes/config.php';

// Xóa toàn bộ session
session_unset();
session_destroy();

// Chuyển hướng dựa trên tham số
if (isset($_GET['redirect']) && $_GET['redirect'] === 'admin') {
    redirect('admin/login.php');
} else {
    redirect('index.php');
}