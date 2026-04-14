<?php
// index.php
$page_title = "Trang chủ";
require_once 'includes/header.php';

// Lấy danh sách danh mục
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>

<h1 class="mb-4 text-center">Chào mừng đến với Office Supplies</h1>

<!-- Thanh tìm kiếm -->
<div class="row justify-content-center mb-5">
    <div class="col-md-8 col-lg-6">
        <form action="products.php" method="GET" class="input-group input-group-lg">
            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm sản phẩm..." required>
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i> Tìm
            </button>
        </form>
    </div>
</div>

<?php foreach ($categories as $cat): ?>
    <h3 class="mt-5 mb-3"><?= htmlspecialchars($cat['name']) ?></h3>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
        <?php
        $stmt = $pdo->prepare("
            SELECT p.*, b.name AS brand_name 
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.category_id = ? 
            ORDER BY p.id DESC 
            LIMIT 4
        ");
        $stmt->execute([$cat['id']]);
        $products = $stmt->fetchAll();

        foreach ($products as $p):
        ?>
            <div class="col">
                <div class="card product-card h-100 shadow-sm">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" class="product-img" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="card-img-top-placeholder">
                            <i class="fas fa-box-open fa-3x"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                        <p class="text-muted small"><?= htmlspecialchars($p['brand_name'] ?? '—') ?></p>
                        <div class="price mb-3 mt-auto">
                            <?= number_format($p['price'], 0, ',', '.') ?> ₫
                        </div>
                        <form method="post" action="cart.php">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mb-5">
        <a href="products.php?category=<?= $cat['id'] ?>" class="btn btn-primary">
            Xem tất cả <?= htmlspecialchars($cat['name']) ?> →
        </a>
    </div>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>