<?php
/**
 * Environment file loader
 * Đọc .env file và đưa vào define() constants
 */

function loadEnv($path) {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Bỏ qua comment
        if (strpos(trim($line), '#') === 0) continue;
        
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        
        // Bỏ quote nếu có
        $value = trim($value, "\"'");
        
        // Chỉ define nếu chưa có
        if (!defined($key)) {
            define($key, $value);
        }
    }
}

// Tự động load .env nếu tồn tại
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    loadEnv($envPath);
}
