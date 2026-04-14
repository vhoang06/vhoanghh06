<?php
// register.php
$page_title = "Đăng ký tài khoản";
require_once 'includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    csrf_validate();

    $username = sanitize($_POST['username'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validate
    if (empty($username) || strlen($username) < 3) $errors[] = "Tên đăng nhập phải có ít nhất 3 ký tự";
    if (!isValidEmail($email)) $errors[] = "Email không hợp lệ";
    if (!isValidPassword($password)) $errors[] = "Mật khẩu phải có ít nhất 6 ký tự, bao gồm cả chữ và số";
    if ($password !== $confirm) $errors[] = "Mật khẩu xác nhận không khớp";

    if (empty($errors)) {
        // Kiểm tra trùng username hoặc email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Tên đăng nhập hoặc email đã được sử dụng";
        } else {
            // Tạo verify token
            $verifyToken = generateToken();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, verify_token, verify_expires)
                VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
            ");
            if ($stmt->execute([$username, $email, $hashedPassword, hashToken($verifyToken)])) {
                // Gửi email xác nhận
                $appUrl = defined('APP_URL') ? APP_URL : 'http://localhost/vhoanghh06';
                $verifyLink = $appUrl . '/verify_email.php?token=' . $verifyToken . '&email=' . urlencode($email);
                $emailTpl = emailVerifyTemplate($username, $verifyLink);

                $mailSent = sendMail($email, $username, 'Xác thực tài khoản Office Supplies', $emailTpl['html'], $emailTpl['text']);

                if ($mailSent) {
                    flash('success', "Đăng ký thành công! Vui lòng kiểm tra email <strong>{$email}</strong> và nhấn vào liên kết xác thực.");
                } else {
                    flash('warning', "Đăng ký thành công nhưng không gửi được email xác thực. Liên hệ quản trị viên để được hỗ trợ.");
                }

                redirect('login.php');
            } else {
                $errors[] = "Đăng ký thất bại, vui lòng thử lại";
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">Đăng ký tài khoản</h4>
            </div>
            <div class="card-body">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" minlength="3"
                               value="<?= htmlspecialchars($username ?? '') ?>" required>
                        <small class="text-muted">Tối thiểu 3 ký tự</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                        <small class="text-muted">Tối thiểu 6 ký tự, bao gồm chữ và số</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Đăng ký</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    Đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
