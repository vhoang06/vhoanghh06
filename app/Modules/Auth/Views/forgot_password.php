<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Quên mật khẩu</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors[0]) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php else: ?>
                    <p class="text-muted text-center mb-4">Nhập email của bạn để nhận hướng dẫn đặt lại mật khẩu.</p>
                    <form method="POST" action="index.php?route=forgot_password">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="form-label">Email tài khoản</label>
                            <input type="email" name="email" class="form-control" required placeholder="example@mail.com">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Gửi yêu cầu</button>
                    </form>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="index.php?route=login" class="text-decoration-none">Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
