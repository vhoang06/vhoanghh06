<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">Đăng nhập (MVC)</h4>
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
                    <a href="index.php?route=forgot_password">Quên mật khẩu?</a>
                </div>

                <div class="text-center mt-4">
                    Chưa có tài khoản? <a href="index.php?route=register">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
