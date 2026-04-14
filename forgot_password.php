<?php
/**
 * forgot_password.php - Yêu cầu đặt lại mật khẩu
 * Gửi email chứa link reset đến user
 */
$page_title = "Quên mật khẩu";
require_once 'includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $email = sanitize($_POST['email'] ?? '');

    if (!isValidEmail($email)) {
        $errors[] = "Email không hợp lệ";
    }

    if (empty($errors)) {
        // Tìm user theo email
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Luôn hiển thị success (tránh leak thông tin email có tồn tại hay không)
        if ($user) {
            // Tạo reset token
            $resetToken = generateToken();
            $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?")
                ->execute([hashToken($resetToken), $user['id']]);

            // Gửi email
            $appUrl = defined('APP_URL') ? APP_URL : 'http://localhost/vhoanghh06';
            $resetLink = $appUrl . '/reset_password.php?token=' . $resetToken . '&email=' . urlencode($email);
            $emailTpl = emailResetTemplate($user['username'], $resetLink);

            sendMail($email, $user['username'], 'Đặt lại mật khẩu - Office Supplies', $emailTpl['html'], $emailTpl['text']);
        }

        $success = true;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark text-center">
                <h4 class="mb-0"><i class="fas fa-key me-2"></i>Quên mật khẩu</h4>
            </div>
            <div class="card-body">

                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-envelope fa-2x mb-2"></i>
                        <h5>Đã gửi hướng dẫn đặt lại mật khẩu!</h5>
                        <p class="mb-0">Nếu email này tồn tại trong hệ thống, bạn sẽ nhận được một email chứa liên kết đặt lại mật khẩu. Liên kết hết hạn sau <strong>1 giờ</strong>.</p>
                    </div>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-primary">Quay lại đăng nhập</a>
                    </div>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $err): ?>
                                <?= htmlspecialchars($err) ?><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted text-center mb-4">Nhập email đã đăng ký. Chúng tôi sẽ gửi bạn một liên kết để đặt lại mật khẩu.</p>

                    <form method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($email ?? '') ?>" required autofocus>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg text-dark">
                                <i class="fas fa-paper-plane me-2"></i>Gửi liên kết đặt lại
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
