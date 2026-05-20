<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php?route=home">Trang chủ</a></li>
    <li class="breadcrumb-item"><a href="index.php?route=products">Sản phẩm</a></li>
    <li class="breadcrumb-item"><a href="index.php?route=products&category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
  </ol>
</nav>

<div class="row mb-5">
    <!-- Ảnh sản phẩm -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <?php if (!empty($product['image'])): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height: 500px; object-fit: contain; background: #fff;">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px; color: #adb5bd;">
                    <i class="fas fa-box-open fa-5x"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="col-md-6">
        <div class="ps-md-4">
            <h6 class="text-primary fw-bold text-uppercase mb-2"><?= htmlspecialchars($product['category_name']) ?></h6>
            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-light text-dark border me-2">
                    <i class="fas fa-tag me-1 text-primary"></i> <?= htmlspecialchars($product['brand_name'] ?? 'No Brand') ?>
                </span>
                <span class="text-muted small">
                    <i class="fas fa-check-circle me-1 text-success"></i> Còn hàng: <?= (int)$product['stock'] ?>
                </span>
            </div>

            <div class="price-detail mb-4">
                <span class="h2 text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?> ₫</span>
            </div>

            <div class="description-section mb-4">
                <h5 class="fw-bold mb-3">Mô tả sản phẩm</h5>
                <div class="text-muted leading-relaxed">
                    <?= nl2br(htmlspecialchars($product['description'] ?: 'Thông tin chi tiết của sản phẩm đang được cập nhật...')) ?>
                </div>
            </div>

            <hr class="my-4">

            <form method="post" action="index.php?route=add_to_cart" class="row g-3 align-items-center">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="col-auto">
                    <label for="quantity" class="form-label mb-0">Số lượng:</label>
                </div>
                <div class="col-3 col-md-2">
                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                </div>
                <div class="col">
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg w-100 py-3 shadow-sm">
                        <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-2">
                <p class="small text-muted mb-1"><i class="fas fa-truck me-2"></i>Giao hàng tận nơi nhanh chóng</p>
                <p class="small text-muted"><i class="fas fa-undo me-2"></i>Đổi trả miễn phí trong 7 ngày</p>
            </div>
        </div>
    </div>
</div>

<!-- Sản phẩm liên quan -->
<?php if (!empty($related_products)): ?>
    <div class="related-section mt-5 pt-5 border-top">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Sản phẩm liên quan</h3>
            <a href="index.php?route=products&category=<?= $product['category_id'] ?>" class="text-decoration-none">Xem thêm <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php foreach ($related_products as $rp): ?>
                <div class="col">
                    <div class="card product-card h-100 shadow-sm border-0 position-relative">
                        <?php if (!empty($rp['image'])): ?>
                            <img src="<?= htmlspecialchars($rp['image']) ?>" class="product-img" alt="<?= htmlspecialchars($rp['name']) ?>">
                        <?php else: ?>
                            <div class="card-img-top-placeholder">
                                <i class="fas fa-box-open fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title text-truncate">
                                <a href="index.php?route=product_detail&id=<?= $rp['id'] ?>" class="text-decoration-none text-dark stretched-link">
                                    <?= htmlspecialchars($rp['name']) ?>
                                </a>
                            </h6>
                            <div class="price text-danger fw-bold small">
                                <?= number_format($rp['price'], 0, ',', '.') ?> ₫
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
