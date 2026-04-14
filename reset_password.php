<?php
/**
 * reset_password.php - Đặt lại mật khẩu mới
 * URL: reset_password.php?token=ABC123&email=user@example.com
 */
$page_title = "Đặt lại mật khẩu";
require_once 'includes/header.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$errors = [];
$success = false;

// Validate token từ URL
$validToken = false;
if (!empty($token) && !empty($email)) {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$email, hashToken($token)]);
    $user = $stmt->fetch();
    if ($user) $validToken = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    if (!$validToken) {
        $errors[] = "Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
    } else {
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (!isValidPassword($password)) {
            $errors[] = "Mật khẩu phải có ít nhất 6 ký tự, bao gồm cả chữ và số";
        }
        if ($password !== $confirm) {
            $errors[] = "Mật khẩu xác nhận không khớp";
        }

        if (empty($errors)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?")
                ->execute([$hashed, $user['id']]);

            $success = true;
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-danger text-white text-center">
                <h4 class="mb-0"><i class="fas fa-lock me-2"></i>Đặt lại mật khẩu</h4>
            </div>
            <div class="card-body">

                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h5>Đặt lại mật khẩu thành công!</h5>
                        <p>Mật khẩu mới đã được cập nhật.</p>
                        <a href="login.php" class="btn btn-primary mt-2">Đăng nhập ngay</a>
                    </div>
                <?php elseif (!$validToken && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h5>Liên kết không hợp lệ</h5>
                        <p>Liên kết đặt lại mật khẩu không đúng hoặc đã hết hạn (1 giờ).</p>
                        <a href="forgot_password.php" class="btn btn-warning mt-2">Gửi lại liên kết</a>
                    </div>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $err): ?>
                                <?= htmlspecialchars($err) ?><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted text-center mb-4">Nhập mật khẩu mới cho tài khoản <strong><?= htmlspecialchars($user['username']) ?></strong></p>

                    <form method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control" minlength="6" required autofocus>
                            <small class="text-muted">Tối thiểu 6 ký tự, bao gồm chữ và số</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="login.php"><i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
