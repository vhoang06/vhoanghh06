<?php
// admin/login.php
session_start();
require_once '../includes/config.php';

// Nếu đã đăng nhập và là admin, chuyển qua dashboard
if (isLoggedIn() && isAdmin()) {
    redirect('index.php');
}

$errors = [];
$success = '';
$showRegister = false;

// Kiểm tra xem đã có admin chưa
try {
    $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
} catch (Exception $e) {
    $adminCount = 0;
}

$hasAdmin = $adminCount > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        $showRegister = true;

        if ($hasAdmin) {
            $errors[] = "Đã có admin. Đăng ký admin mới chỉ mở khi chưa có tài khoản admin.";
        } else {
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (empty($username)) $errors[] = "Tên đăng nhập không được để trống";
            if (empty($email))    $errors[] = "Email không được để trống";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";
            if (strlen($password) < 6) $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
            if ($password !== $confirm) $errors[] = "Mật khẩu xác nhận không khớp";

            if (empty($errors)) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    $errors[] = "Tên đăng nhập hoặc email đã được sử dụng";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
                    if ($stmt->execute([$username, $email, $hashed])) {
                        $success = "Tài khoản admin đã được tạo thành công. Bạn hãy đăng nhập ngay.";
                        $hasAdmin = true;
                    } else {
                        $errors[] = "Đăng ký thất bại, vui lòng thử lại";
                    }
                }
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username)) $errors[] = "Tên đăng nhập không được để trống";
        if (empty($password)) $errors[] = "Mật khẩu không được để trống";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['role'] === 'admin') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    redirect('index.php');
                } else {
                    $errors[] = "Tài khoản này không có quyền truy cập Admin Panel";
                }
            } else {
                $errors[] = "Tên đăng nhập hoặc mật khẩu không chính xác";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Office Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .admin-login-container {
            width: 100%;
            max-width: 520px;
        }
        .admin-login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .admin-login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .admin-login-header h2 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 28px;
        }
        .admin-login-header p {
            color: rgba(255, 255, 255, 0.8);
            margin: 8px 0 0 0;
            font-size: 14px;
        }
        .admin-login-body {
            padding: 40px;
            background: white;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .btn-admin-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            color: white;
        }
        .btn-admin-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: #fee;
            color: #c33;
        }
        .alert-success {
            background-color: #e8f7ea;
            color: #256029;
        }
        .info-box {
            background: #f3f7ff;
            border: 1px solid #d9e2ff;
            border-radius: 10px;
            padding: 16px 18px;
            color: #1f2f68;
            margin-bottom: 20px;
        }
        .register-toggle {
            display: block;
            width: 100%;
            margin-top: 20px;
            text-align: center;
            color: #667eea;
            font-weight: 600;
            cursor: pointer;
        }
        .register-toggle:hover {
            color: #764ba2;
        }
        .register-form {
            display: none;
            margin-top: 25px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .back-link a:hover {
            color: #764ba2;
        }
        .mb-3 {
            margin-bottom: 20px;
        }
        .icon-lock {
            font-size: 48px;
            color: white;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <div class="icon-lock">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Admin Login</h2>
                <p>Office Supplies Management System</p>
            </div>
            <div class="admin-login-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    <?php if ($hasAdmin): ?>
                        Nhập thông tin admin để vào trang quản trị.
                    <?php else: ?>
                        Chưa có admin nào. Vui lòng tạo tài khoản admin đầu tiên.
                    <?php endif; ?>
                </div>

                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="username">
                            <i class="fas fa-user me-2"></i>Tên đăng nhập hoặc Email
                        </label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?= htmlspecialchars($username ?? '') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <i class="fas fa-key me-2"></i>Mật khẩu
                        </label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <input type="hidden" name="action" value="login">

                    <div class="d-grid">
                        <button type="submit" class="btn btn-admin-login btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </button>
                    </div>
                </form>

                <div class="register-toggle" id="toggleRegister">
                    <?php if ($hasAdmin): ?>
                        Đăng ký admin mới
                    <?php else: ?>
                        Chưa có admin? Tạo admin ngay
                    <?php endif; ?>
                </div>
                <div class="register-form" id="registerForm">
                    <form method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="reg_username">
                                <i class="fas fa-user-plus me-2"></i>Tên đăng nhập
                            </label>
                            <input type="text" id="reg_username" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($username ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="reg_email">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" id="reg_email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($email ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="reg_password">
                                <i class="fas fa-key me-2"></i>Mật khẩu
                            </label>
                            <input type="password" id="reg_password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="reg_confirm_password">
                                <i class="fas fa-key me-2"></i>Xác nhận mật khẩu
                            </label>
                            <input type="password" id="reg_confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <input type="hidden" name="action" value="register">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-admin-login btn-lg" <?php if ($hasAdmin) echo 'disabled'; ?> >
                                <i class="fas fa-user-plus me-2"></i>Đăng ký admin
                            </button>
                        </div>
                        <?php if ($hasAdmin): ?>
                            <p class="mt-3 text-center text-muted">Đã tồn tại admin. Đăng ký admin mới chỉ thực hiện được khi chưa có admin.</p>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="back-link">
                    <a href="../login.php">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại trang đăng nhập chung
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggle = document.getElementById('toggleRegister');
        const form = document.getElementById('registerForm');

        if (toggle && form) {
            toggle.addEventListener('click', () => {
                form.style.display = form.style.display === 'block' ? 'none' : 'block';
            });
        }
    </script>
</body>
</html>
