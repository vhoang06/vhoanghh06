<?php
// migration_add_role.php
// Chạy file này một lần để thêm cột role vào bảng users

require_once 'includes/config.php';

try {
    // Kiểm tra xem cột role đã tồn tại chưa
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($stmt->fetch()) {
        die("✓ Cột 'role' đã tồn tại!");
    }

    // Thêm cột role (user hoặc admin)
    $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER password");
    
    echo "✓ Thêm cột 'role' thành công!<br>";
    echo "✓ Tất cả tài khoản hiện tại đều là 'user'<br>";
    echo "✓ Để tạo admin, bạn có thể chạy query: UPDATE users SET role='admin' WHERE username='[username]'";

} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}
?>
