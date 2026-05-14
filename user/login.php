<?php
// login.php
$page_title = "Đăng nhập";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate
    if (empty($username)) $errors[] = "Tên đăng nhập không được để trống";
    if (empty($password)) $errors[] = "Mật khẩu không được để trống";

    if (empty($errors)) {
        $user = authenticateUser($pdo, $username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect('../admin/index.php');
            } else {
                redirect('index.php');
            }
        } else {
            $errors[] = "Tên đăng nhập hoặc mật khẩu không chính xác";
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

                <div class="text-center mt-4">
                    Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
