<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-boxes"></i>
            </div>
            <h6 class="text-muted mb-1">Tổng sản phẩm</h6>
            <h3 class="mb-0 fw-bold"><?= number_format($stats['total_products']) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h6 class="text-muted mb-1">Tổng đơn hàng</h6>
            <h3 class="mb-0 fw-bold"><?= number_format($stats['total_orders']) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="text-muted mb-1">Khách hàng</h6>
            <h3 class="mb-0 fw-bold"><?= number_format($stats['total_users']) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h6 class="text-muted mb-1">Doanh thu</h6>
            <h3 class="mb-0 fw-bold"><?= number_format($stats['total_revenue'], 0, ',', '.') ?> ₫</h3>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 fw-bold">Hoạt động gần đây</h5>
                <button class="btn btn-sm btn-outline-secondary">Xem tất cả</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr class="text-muted text-center">
                                <td colspan="4" class="py-4">Chưa có dữ liệu mới</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $o): ?>
                            <tr>
                                <td class="fw-bold">#<?= $o['id'] ?></td>
                                <td><?= htmlspecialchars($o['customer_name'] ?? 'Khách vãng lai') ?></td>
                                <td><?= number_format($o['total_amount'], 0, ',', '.') ?> ₫</td>
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
                                        'completed' => 'Đã xong',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    ?>
                                    <span class="badge <?= $status_class[$o['status']] ?? 'bg-secondary' ?> px-3">
                                        <?= $status_text[$o['status']] ?? $o['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="mb-4 fw-bold">Thông báo hệ thống</h5>
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <p class="mb-0 small fw-bold">Hệ thống đã được cập nhật</p>
                    <span class="text-muted extra-small" style="font-size: 0.75rem;">Vừa xong</span>
                </div>
            </div>
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom text-muted opacity-50">
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <p class="mb-0 small fw-bold">Không có thông báo mới</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
