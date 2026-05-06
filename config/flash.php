<?php
/**
 * Flash Message System
 * Lưu thông báo giữa các request qua session
 */

function flash($type, $message) {
    $_SESSION['flash'][] = [
        'type' => $type,    // success, danger, warning, info
        'message' => $message
    ];
}

function flashDisplay() {
    if (empty($_SESSION['flash'])) return '';
    
    $output = '';
    foreach ($_SESSION['flash'] as $msg) {
        $type = htmlspecialchars($msg['type']);
        $message = htmlspecialchars($msg['message']);
        $output .= "<div class=\"alert alert-{$type} alert-dismissible fade show\" role=\"alert\">";
        $output .= $message;
        $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        $output .= "</div>\n";
    }
    
    unset($_SESSION['flash']);
    return $output;
}

function flashOnce($type, $message) {
    flash($type, $message);
    // Redirect sau khi set flash
    if (!headers_sent()) {
        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header("Location: $back");
        exit;
    }
}
