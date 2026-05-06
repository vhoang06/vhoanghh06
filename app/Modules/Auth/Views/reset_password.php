<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Đặt lại mật khẩu</h2>

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
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                        <div class="mt-3">
                            <a href="index.php?route=login" class="btn btn-primary btn-sm">Đăng nhập ngay</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="POST" action="index.php?route=reset_password&email=<?= urlencode($_GET['email']) ?>&token=<?= urlencode($_GET['token']) ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Cập nhật mật khẩu</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
