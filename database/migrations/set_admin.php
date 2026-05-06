<?php
// set_admin.php
require_once 'includes/config.php';

try {
    // Cập nhật tất cả tài khoản thành admin (an toàn cho lần đầu)
    $pdo->exec("UPDATE users SET role='admin'");
    
    echo "<h2 style='color: green; text-align: center; margin-top: 50px;'>✓ Cập nhật thành công!</h2>";
    echo "<p style='text-align: center; font-size: 16px;'>Tất cả tài khoản hiện tại đã được cấp quyền admin.</p>";
    echo "<p style='text-align: center;'><a href='admin/login.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Đi đến Admin Login</a></p>";

} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}
?>
