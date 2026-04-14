<?php
/**
 * Security Helpers - Rate Limiter + Input Sanitizer
 */

// ==================== RATE LIMITER ====================
/**
 * Kiểm tra rate limit cho một action (dựa trên IP + action name)
 * @return bool true nếu vượt quá giới hạn
 */
function isRateLimited($action, $maxAttempts = 5, $windowSeconds = 300) {
    $key = 'rate_limit_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
    $now = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'reset' => $now + $windowSeconds];
    }
    
    // Reset nếu hết window
    if ($now > $_SESSION[$key]['reset']) {
        $_SESSION[$key] = ['count' => 0, 'reset' => $now + $windowSeconds];
    }
    
    $_SESSION[$key]['count']++;
    
    return $_SESSION[$key]['count'] > $maxAttempts;
}

function getRateLimitRemaining($action) {
    $key = 'rate_limit_' . $action . '_' . $_SERVER['REMOTE_ADDR'];
    if (!isset($_SESSION[$key])) return 5;
    return max(0, 5 - $_SESSION[$key]['count']);
}

// ==================== INPUT SANITIZER ====================
/**
 * Làm sạch string input - trim + strip tags + htmlspecialchars
 */
function sanitize($input) {
    if (!is_string($input)) return $input;
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email chuẩn hơn
 */
function isValidEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate số điện thoại VN (cơ bản)
 */
function isValidPhone($phone) {
    $phone = trim($phone);
    return preg_match('/^(0[0-9]{9,10}|\+84[0-9]{9,10})$/', $phone);
}

/**
 * Validate password: ít nhất 6 ký tự, có chữ + số
 */
function isValidPassword($password) {
    return strlen($password) >= 6 && preg_match('/[a-zA-Z]/', $password) && preg_match('/[0-9]/', $password);
}

/**
 * Tạo token ngẫu nhiên (cho email verify, password reset)
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Hash token để lưu DB (dùng hash而不是 raw token)
 */
function hashToken($token) {
    return hash('sha256', $token);
}
