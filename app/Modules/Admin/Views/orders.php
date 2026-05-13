<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold">Quản lý Đơn hàng</h5>
        <a href="index.php?route=admin/orders/add" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Tạo đơn hàng mới
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Mã Đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th style="width: 150px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td class="fw-bold text-primary">#<?= $o['id'] ?></td>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($o['shipping_name'] ?? $o['customer_name']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($o['shipping_phone'] ?? '') ?></div>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                    <td class="fw-bold"><?= number_format($o['total_amount'], 0, ',', '.') ?> ₫</td>
                    <td>
                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($o['payment_method']) ?></span>
                    </td>
                    <td>
                        <?php
                        $status_class = [
                            'pending' => 'bg-warning-subtle text-warning',
                            'processing' => 'bg-info-subtle text-info',
                            'completed' => 'bg-success-subtle text-success',
                            'cancelled' => 'bg-danger-subtle text-danger'
                        ];
                        $status_text = [
                            'pending' => 'Chờ xử lý',
                            'processing' => 'Đang xử lý',
                            'completed' => 'Đã giao',
                            'cancelled' => 'Đã hủy'
                        ];
                        ?>
                        <span class="badge <?= $status_class[$o['status']] ?? 'bg-secondary' ?> px-3">
                            <?= $status_text[$o['status']] ?? $o['status'] ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="index.php?route=admin/orders/detail&id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="index.php?route=admin/orders/update" method="POST" class="d-flex gap-1">
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Chờ</option>
                                    <option value="processing" <?= $o['status'] == 'processing' ? 'selected' : '' ?>>Xử lý</option>
                                    <option value="completed" <?= $o['status'] == 'completed' ? 'selected' : '' ?>>Xong</option>
                                    <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'selected' : '' ?>>Hủy</option>
                                </select>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
