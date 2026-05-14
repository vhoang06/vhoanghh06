<?php
// logout.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/logout_functions.php';

// Xóa toàn bộ session
session_unset();
session_destroy();

// Chuyển hướng dựa trên tham số
$target = getLogoutRedirectTarget($_GET['redirect'] ?? null);
redirect($target);
