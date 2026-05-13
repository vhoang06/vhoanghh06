<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 text-center p-5">
            <div class="mb-4">
                <?php if ($type === 'success'): ?>
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle fa-5x text-danger"></i>
                <?php endif; ?>
            </div>
            
            <h2 class="fw-bold mb-3"><?= $page_title ?></h2>
            <p class="lead text-muted mb-4"><?= $message ?></p>
            
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <?php if ($type === 'success'): ?>
                    <a href="index.php?route=login" class="btn btn-primary btn-lg px-5 rounded-pill">Đăng nhập ngay</a>
                <?php else: ?>
                    <a href="index.php?route=home" class="btn btn-outline-secondary btn-lg px-5 rounded-pill">Về trang chủ</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
