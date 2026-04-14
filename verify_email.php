<?php
/**
 * verify_email.php - Xác thực email sau khi đăng ký
 * URL: verify_email.php?token=ABC123&email=user@example.com
 */
$page_title = "Xác thực email";
require_once 'includes/header.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (empty($token) || empty($email)) {
    flash('danger', 'Liên kết xác thực không hợp lệ.');
    redirect('login.php');
}

// Tìm user có email và token khớp, chưa hết hạn
$stmt = $pdo->prepare("SELECT id, username, email_verified FROM users WHERE email = ? AND verify_token = ? AND verify_expires > NOW()");
$stmt->execute([$email, hashToken($token)]);
$user = $stmt->fetch();

if (!$user) {
    // Token không đúng hoặc hết hạn
    if ($user === null) {
        // Không tìm thấy user hoặc token sai
        flash('danger', 'Liên kết xác thực không đúng hoặc đã hết hạn. Vui lòng đăng ký lại.');
    } else {
        // Token hết hạn
        flash('danger', 'Liên kết xác thực đã hết hạn (24 giờ). Vui lòng đăng ký lại.');
    }
    redirect('register.php');
}

if ($user['email_verified']) {
    flash('info', 'Email của bạn đã được xác thực trước đó. Vui lòng đăng nhập.');
    redirect('login.php');
}

// Xác thực thành công
$pdo->prepare("UPDATE users SET email_verified = 1, verify_token = NULL, verify_expires = NULL WHERE id = ?")->execute([$user['id']]);

?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow text-center">
            <div class="card-body py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h3 class="text-success mb-3">Xác thực thành công!</h3>
                <p class="text-muted">Chào mừng <strong><?= htmlspecialchars($user['username']) ?></strong> đến với Office Supplies.</p>
                <p class="text-muted">Tài khoản của bạn đã được kích hoạt.</p>
                <div class="mt-4">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
