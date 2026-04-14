<?php
// login.php
$page_title = "Đăng nhập";
require_once 'includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    csrf_validate();

    // Rate limiting: 5 lần/5 phút
    if (isRateLimited('login', 5, 300)) {
        $remaining = getRateLimitRemaining('login');
        $errors[] = "Bạn đã thử đăng nhập quá nhiều lần. Vui lòng chờ 5 phút trước khi thử lại.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username)) $errors[] = "Tên đăng nhập không được để trống";
        if (empty($password)) $errors[] = "Mật khẩu không được để trống";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id, username, email, password, role, email_verified FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Kiểm tra email đã xác thực chưa
                if (empty($user['email_verified'])) {
                    $errors[] = "Email của bạn chưa được xác thực. Vui lòng kiểm tra hộp thư và nhấn vào liên kết kích hoạt.";
                    // Gửi lại email xác nhận (tùy chọn - có thể thêm nút "gửi lại")
                } else {
                    // Đăng nhập thành công
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email_verified'] = $user['email_verified'];

                    // Cập nhật last_login
                    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

                    // Reset rate limit sau khi login thành công
                    unset($_SESSION['rate_limit_login_' . $_SERVER['REMOTE_ADDR']]);

                    // Chuyển hướng theo role
                    if ($user['role'] === 'admin') {
                        redirect('admin/index.php');
                    } else {
                        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                        unset($_SESSION['redirect_after_login']);
                        redirect($redirect);
                    }
                }
            } else {
                $errors[] = "Tên đăng nhập hoặc mật khẩu không chính xác";
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">Đăng nhập</h4>
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
                        <label class="form-label">Tên đăng nhập hoặc Email</label>
                        <input type="text" name="username" class="form-control"
                               value="<?= htmlspecialchars($username ?? '') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="forgot_password.php">Quên mật khẩu?</a>
                </div>

                <div class="text-center mt-4">
                    Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
