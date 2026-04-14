<?php
/**
 * CSRF Token Protection
 * Tạo và validate CSRF token cho mỗi session
 */

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken()) . '">';
}

function csrfValidate() {
    $token = $_POST['_csrf'] ?? '';
    if (empty($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('CSRF token không hợp lệ. Vui lòng tải lại trang và thử lại.');
    }
}
