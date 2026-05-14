<?php
// brands.php
$page_title = "Thương hiệu";
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM brands ORDER BY name");
$brands = $stmt->fetchAll();
?>

<h1 class="mb-4">Các thương hiệu</h1>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($brands as $brand): ?>
        <div class="col">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($brand['name']) ?></h5>
                    <p class="text-muted small">Nhiều sản phẩm chất lượng cao</p>
                    <a href="products.php?brand=<?= $brand['id'] ?>" class="btn btn-outline-primary">
                        Xem sản phẩm
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($brands)): ?>
    <div class="alert alert-info mt-4">Hiện chưa có thương hiệu nào.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
