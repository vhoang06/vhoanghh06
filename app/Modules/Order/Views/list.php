<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<h1 class="mb-4">Đơn hàng của tôi</h1>

<?php if (empty($orders)): ?>
    <div class="alert alert-info py-5 text-center">
        <p class="lead">Bạn chưa có đơn hàng nào.</p>
        <a href="index.php?route=products" class="btn btn-primary">Mua sắm ngay</a>
    </div>
<?php else: ?>
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0 bg-white">
            <thead class="table-light">
                <tr>
                    <th>Mã ĐH</th>
                    <th>Ngày đặt</th>
                    <th>Số sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><span class="fw-bold">#<?= $o['id'] ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                        <td><?= $o['items_count'] ?></td>
                        <td class="fw-bold text-danger"><?= number_format($o['total_amount'], 0, ',', '.') ?> ₫</td>
                        <td>
                            <?php 
                            $status_map = [
                                'pending' => ['label' => 'Chờ xử lý', 'class' => 'bg-warning'],
                                'processing' => ['label' => 'Đang xử lý', 'class' => 'bg-info text-white'],
                                'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-success'],
                                'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-danger']
                            ];
                            $s = $status_map[$o['status']] ?? ['label' => $o['status'], 'class' => 'bg-secondary'];
                            ?>
                            <span class="badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
                        </td>
                        <td><span class="text-uppercase small"><?= $o['payment_method'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
