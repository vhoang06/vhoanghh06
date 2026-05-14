<?php
// register.php
$page_title = "Đăng ký tài khoản";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $errors = validateRegistrationData($username, $email, $password, $confirm);

    if (empty($errors)) {
        if (isDuplicateUser($pdo, $username, $email)) {
            $errors[] = "Tên đăng nhập hoặc email đã được sử dụng";
        } else {
            $userId = createUser($pdo, $username, $email, $password);
            if ($userId > 0) {
                $success = true;
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

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Đăng ký thành công! Đang chuyển qua trang đăng nhập...
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                    </script>
                <?php endif; ?>

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
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= htmlspecialchars($username ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
