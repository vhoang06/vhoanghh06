<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="mb-0 fw-bold"><?= $product ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên sản phẩm</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required placeholder="Nhập tên sản phẩm...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mô tả sản phẩm</label>
                                <textarea name="description" class="form-control" rows="8" placeholder="Mô tả chi tiết sản phẩm..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Danh mục</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Thương hiệu</label>
                                <select name="brand_id" class="form-select">
                                    <option value="0">Chọn thương hiệu</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?= $brand['id'] ?>" <?= (isset($product['brand_id']) && $product['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($brand['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Giá niêm yết (₫)</label>
                                <input type="number" name="price" class="form-control" value="<?= $product['price'] ?? '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Số lượng trong kho</label>
                                <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? 0 ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Link ảnh sản phẩm</label>
                                <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($product['image'] ?? '') ?>" placeholder="https://...">
                                <?php if (!empty($product['image'])): ?>
                                    <div class="mt-2 text-center">
                                        <img src="<?= htmlspecialchars($product['image']) ?>" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top d-flex gap-2 justify-content-end">
                        <a href="index.php?route=admin/products" class="btn btn-light rounded-pill px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
