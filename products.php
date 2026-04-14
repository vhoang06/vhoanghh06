<?php
// products.php
$page_title = "Sản phẩm";
require_once 'includes/header.php';

// Lấy tham số từ URL
$search = trim($_GET['q'] ?? '');
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

// Xây dựng query
$sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if ($brand_id > 0) {
    $sql .= " AND p.brand_id = ?";
    $params[] = $brand_id;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Lấy danh mục để lọc
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $cat_stmt->fetchAll();

// Lấy thương hiệu để lọc
$brand_stmt = $pdo->query("SELECT * FROM brands ORDER BY name");
$brands = $brand_stmt->fetchAll();
?>

<h1 class="mb-4">Danh sách sản phẩm</h1>

<!-- Bộ lọc & tìm kiếm -->
<div class="row mb-4">
    <div class="col-md-4">
        <form action="" method="get" class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">Tìm</button>
        </form>
    </div>
    
    <div class="col-md-8">
        <div class="d-flex flex-wrap gap-2 justify-content-end mb-2">
            <a href="products.php" class="btn btn-outline-secondary <?= $category_id == 0 ? 'active' : '' ?>">Tất cả danh mục</a>
            <?php foreach ($categories as $cat): ?>
                <a href="products.php?category=<?= $cat['id'] ?><?= $brand_id > 0 ? '&brand=' . $brand_id : '' ?>" 
                   class="btn btn-outline-secondary <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($brands)): ?>
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <a href="products.php<?= $category_id > 0 ? '?category=' . $category_id : '' ?>" class="btn btn-sm btn-outline-secondary <?= $brand_id == 0 ? 'active' : '' ?>">Tất cả thương hiệu</a>
            <?php foreach ($brands as $br): ?>
                <a href="products.php?brand=<?= $br['id'] ?><?= $category_id > 0 ? '&category=' . $category_id : '' ?>" 
                   class="btn btn-sm btn-outline-secondary <?= $brand_id == $br['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($br['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info text-center py-5">
        Không tìm thấy sản phẩm nào phù hợp.
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($products as $p): ?>
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
                        <p class="text-muted small">
                            <?= htmlspecialchars($p['brand_name'] ?? '—') ?> • 
                            <?= htmlspecialchars($p['category_name']) ?>
                        </p>
                        <div class="price mb-3 mt-auto">
                            <?= number_format($p['price'], 0, ',', '.') ?> ₫
                        </div>
                        <p class="small text-muted flex-grow-1"><?= htmlspecialchars(substr($p['description'] ?? '', 0, 60)) ?>...</p>
                        
                        <form method="post" action="cart.php">
                            <?= csrf_field() ?>
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
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>