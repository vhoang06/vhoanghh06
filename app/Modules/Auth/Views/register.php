<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Đăng ký tài khoản</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php else: ?>
                    <form method="POST" action="index.php?route=register">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                            <div class="form-text">Tối thiểu 6 ký tự, bao gồm cả chữ và số.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">Đăng ký</button>
                    </form>
                <?php endif; ?>

                <hr class="my-4">
                <div class="text-center">
                    Đã có tài khoản? <a href="index.php?route=login" class="text-decoration-none">Đăng nhập ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
