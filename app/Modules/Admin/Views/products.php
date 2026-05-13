<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold">Danh sách Sản phẩm</h5>
        <a href="index.php?route=admin/products/add" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Thêm sản phẩm mới
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 100px;">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th style="width: 100px;">Kho</th>
                    <th style="width: 150px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td class="fw-semibold">#<?= $p['id'] ?></td>
                    <td>
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= htmlspecialchars($p['image']) ?>" class="rounded-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" alt="">
                        <?php else: ?>
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($p['brand_name'] ?? '—') ?></div>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?? '—') ?></span></td>
                    <td>
                        <div class="fw-bold"><?= number_format($p['price'], 0, ',', '.') ?> ₫</div>
                    </td>
                    <td>
                        <span class="badge <?= $p['stock'] > 10 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> px-3">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="index.php?route=admin/products/edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary border-0" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="index.php?route=admin/products/delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger border-0" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
