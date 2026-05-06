<?php
/**
 * CSRF Token Protection
 * Tạo và validate CSRF token cho mỗi session
 */

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_validate() {
    $token = $_POST['_csrf'] ?? '';
    if (empty($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('CSRF token không hợp lệ. Vui lòng tải lại trang và thử lại.');
    }
}
