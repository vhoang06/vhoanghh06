<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="text-center mb-5">
    <h1 class="display-4 fw-bold">Thương hiệu</h1>
    <p class="text-muted">Khám phá các sản phẩm từ những thương hiệu hàng đầu</p>
</div>

<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($brands as $brand): ?>
        <div class="col">
            <a href="index.php?route=products&brand=<?= $brand['id'] ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 text-center p-4 product-card">
                    <div class="mb-3 d-flex align-items-center justify-content-center" style="height: 100px;">
                        <?php if (!empty($brand['logo'])): ?>
                            <img src="<?= htmlspecialchars($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <?php else: ?>
                            <i class="fas fa-tag fa-4x text-light"></i>
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($brand['name']) ?></h5>
                    <div class="mt-2 text-primary small">Xem sản phẩm →</div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($brands)): ?>
    <div class="alert alert-light text-center py-5 border">
        <p class="text-muted mb-0">Hiện chưa có thương hiệu nào được cập nhật.</p>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
