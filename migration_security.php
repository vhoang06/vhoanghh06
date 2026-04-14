<?php
/**
 * Migration: Thêm các cột bảo mật vào bảng users
 * Chạy 1 lần khi nâng cấp từ version cũ
 */

require_once 'includes/config.php';

// Chỉ cho chạy từ localhost hoặc CLI
$isLocal = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1' || php_sapi_name() === 'cli');
if (!$isLocal) {
    die('⛔ Migration chỉ được chạy từ localhost hoặc CLI.');
}

$migrations = [
    // Email verification
    ["ALTER TABLE users ADD COLUMN email_verified TINYINT(1) DEFAULT 0 AFTER role", "✓ Thêm cột email_verified"],
    ["ALTER TABLE users ADD COLUMN verify_token VARCHAR(64) DEFAULT NULL AFTER email_verified", "✓ Thêm cột verify_token"],
    ["ALTER TABLE users ADD COLUMN verify_expires DATETIME DEFAULT NULL AFTER verify_token", "✓ Thêm cột verify_expires"],
    
    // Password reset
    ["ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL AFTER verify_expires", "✓ Thêm cột reset_token"],
    ["ALTER TABLE users ADD COLUMN reset_expires DATETIME DEFAULT NULL AFTER reset_token", "✓ Thêm cột reset_expires"],
    
    // Last login tracking
    ["ALTER TABLE users ADD COLUMN last_login DATETIME DEFAULT NULL AFTER reset_expires", "✓ Thêm cột last_login"],
];

echo "<html><head><meta charset='utf-8'><style>body{font-family:monospace;padding:40px;max-width:800px;margin:0 auto;} .ok{color:green;} .skip{color:gray;}</style></head><body>";
echo "<h2>🔧 Running Security Migrations</h2>";

$done = 0;
$skipped = 0;

foreach ($migrations as [$sql, $msg]) {
    try {
        $pdo->exec($sql);
        echo "<p class='ok'>{$msg}</p>";
        $done++;
    } catch (Exception $e) {
        // Cột đã tồn tại
        echo "<p class='skip'>⊘ {$msg} (đã tồn tại)</p>";
        $skipped++;
    }
}

echo "<hr>";
echo "<p>✅ Hoàn thành: {$done} cột mới, {$skipped} đã có sẵn.</p>";
echo "<p><a href='index.php'>← Quay lại trang chủ</a></p>";
echo "</body></html>";
