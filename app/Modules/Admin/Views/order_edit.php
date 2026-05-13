<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="mb-0 fw-bold">Sửa thông tin giao hàng #<?= $order['id'] ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên người nhận</label>
                        <input type="text" name="shipping_name" class="form-control" value="<?= htmlspecialchars($order['shipping_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số điện thoại</label>
                        <input type="text" name="shipping_phone" class="form-control" value="<?= htmlspecialchars($order['shipping_phone']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ giao hàng</label>
                        <textarea name="shipping_address" class="form-control" rows="4" required><?= htmlspecialchars($order['shipping_address']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="2"><?= htmlspecialchars($order['note'] ?? '') ?></textarea>
                    </div>

                    <div class="mt-4 pt-4 border-top d-flex gap-2 justify-content-end">
                        <a href="index.php?route=admin/orders/detail&id=<?= $order['id'] ?>" class="btn btn-light rounded-pill px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
