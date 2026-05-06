<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<div class="row">
    <!-- Sidebar: Bộ lọc -->
    <div class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="fas fa-search me-2"></i>Tìm kiếm</h5>
                <form action="index.php" method="get">
                    <input type="hidden" name="route" value="products">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Tên sản phẩm..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-primary" type="submit">Tìm</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white font-weight-bold">
                <i class="fas fa-list me-2"></i>Danh mục
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php?route=products<?= $brand_id > 0 ? '&brand=' . $brand_id : '' ?>" 
                   class="list-group-item list-group-item-action <?= $category_id == 0 ? 'active' : '' ?>">
                    Tất cả danh mục
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?route=products&category=<?= $cat['id'] ?><?= $brand_id > 0 ? '&brand=' . $brand_id : '' ?>" 
                       class="list-group-item list-group-item-action <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($brands)): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white font-weight-bold">
                <i class="fas fa-tags me-2"></i>Thương hiệu
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php?route=products<?= $category_id > 0 ? '&category=' . $category_id : '' ?>" 
                   class="list-group-item list-group-item-action <?= $brand_id == 0 ? 'active' : '' ?>">
                    Tất cả thương hiệu
                </a>
                <?php foreach ($brands as $br): ?>
                    <a href="index.php?route=products&brand=<?= $br['id'] ?><?= $category_id > 0 ? '&category=' . $category_id : '' ?>" 
                       class="list-group-item list-group-item-action <?= $brand_id == $br['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($br['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($category_id > 0 || $brand_id > 0 || $search !== ''): ?>
            <a href="index.php?route=products" class="btn btn-outline-danger w-100 mb-4">
                <i class="fas fa-times me-2"></i>Xóa tất cả bộ lọc
            </a>
        <?php endif; ?>
    </div>

    <!-- Main: Danh sách sản phẩm -->
    <div class="col-md-9">
        <h1 class="mb-4">Danh sách sản phẩm</h1>
        
        <?php if (empty($products)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                <p class="lead">Không tìm thấy sản phẩm nào phù hợp.</p>
                <a href="index.php?route=products" class="btn btn-primary mt-2">Xem tất cả sản phẩm</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $p): ?>
                    <div class="col">
                        <div class="card product-card h-100 shadow-sm border-0 position-relative">
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" class="product-img" alt="<?= htmlspecialchars($p['name']) ?>">
                            <?php else: ?>
                                <div class="card-img-top-placeholder">
                                    <i class="fas fa-box-open fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="text-primary mb-1"><?= htmlspecialchars($p['category_name']) ?></h6>
                                <h5 class="card-title">
                                    <a href="index.php?route=product_detail&id=<?= $p['id'] ?>" class="text-decoration-none text-dark stretched-link">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($p['brand_name'] ?? '—') ?>
                                </p>
                                
                                <div class="mt-auto" style="z-index: 2; position: relative;">
                                    <div class="price mb-3">
                                        <?= number_format($p['price'], 0, ',', '.') ?> ₫
                                    </div>
                                    
                                    <form method="post" action="index.php?route=add_to_cart">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary w-100">
                                            <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
