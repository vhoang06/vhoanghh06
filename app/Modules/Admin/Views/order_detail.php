<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Chi tiết Đơn hàng #<?= $order['id'] ?></h4>
    <a href="index.php?route=admin/orders" class="btn btn-light rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <!-- Danh sách sản phẩm -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="mb-0 fw-bold">Sản phẩm trong đơn</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['image']): ?>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div class="text-muted small">ID: #<?= $item['product_id'] ?></div>
                            </td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                            <td><?= $item['quantity'] ?></td>
                            <td class="text-end fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                            <td class="text-end fw-bold text-danger h5"><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Thông tin vận chuyển -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Thông tin giao hàng</h5>
                <a href="index.php?route=admin/orders/edit&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> Sửa thông tin
                </a>
            </div>
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <p class="text-muted small mb-1">Người nhận</p>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($order['shipping_name']) ?></p>
                </div>
                <div class="col-sm-6 mb-3">
                    <p class="text-muted small mb-1">Số điện thoại</p>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($order['shipping_phone']) ?></p>
                </div>
                <div class="col-12 mb-3">
                    <p class="text-muted small mb-1">Địa chỉ</p>
                    <p class="fw-bold mb-0"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
                <div class="col-12">
                    <p class="text-muted small mb-1">Ghi chú</p>
                    <p class="mb-0 italic"><?= htmlspecialchars($order['note'] ?: 'Không có ghi chú') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Trạng thái & Thanh toán -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-4">Trạng thái & Thanh toán</h5>
            
            <div class="mb-4">
                <p class="text-muted small mb-2">Trạng thái đơn hàng</p>
                <form action="index.php?route=admin/orders/update" method="POST">
                    <input type="hidden" name="id" value="<?= $order['id'] ?>">
                    <select name="status" class="form-select border-0 bg-light rounded-3 mb-2" onchange="this.form.submit()">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                    <p class="small text-muted"><i class="fas fa-info-circle me-1"></i> Thay đổi sẽ được cập nhật ngay lập tức.</p>
                </form>
            </div>

            <div class="mb-0">
                <p class="text-muted small mb-1">Phương thức thanh toán</p>
                <p class="fw-bold mb-0">
                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                    <?= $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng' ?>
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
            <h6 class="mb-3">Thời gian đặt hàng</h6>
            <h4 class="mb-0"><?= date('H:i', strtotime($order['created_at'])) ?></h4>
            <p class="mb-0"><?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
