<?php
// orders.php
$page_title = "Đơn hàng của tôi";
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$stmt = $pdo->prepare("
    SELECT o.id, o.total_amount, o.status, o.created_at, 
           COUNT(oi.id) AS item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Xem chi tiết đơn hàng (user)
$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;
if ($view_id > 0) {
    $o_stmt = $pdo->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
    $o_stmt->execute([$view_id, $_SESSION['user_id']]);
    $order = $o_stmt->fetch();
    if ($order) {
        $it_stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $it_stmt->execute([$view_id]);
        $order_items = $it_stmt->fetchAll();
    } else {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Không tìm thấy đơn hàng của bạn.'];
        redirect('orders.php');
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4">Đơn hàng của tôi</h1>

    <?php if (isset($order) && $view_id > 0): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <strong>Đơn hàng #<?= $order['id'] ?></strong>
                    <div class="small">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                </div>
                <div>
                    <span class="badge bg-secondary px-3 py-2"><?= htmlspecialchars($order['status']) ?></span>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Tổng tiền:</strong> <?= number_format($order['total_amount'],0,',','.') ?> ₫</p>
                <p><strong>Địa chỉ giao hàng:</strong><br><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                <p><strong>Ghi chú:</strong><br><?= nl2br(htmlspecialchars($order['note'])) ?></p>

                <h5 class="mt-3">Sản phẩm</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th><th>Thành tiền</th></tr></thead>
                        <tbody>
                            <?php foreach ($order_items as $it): ?>
                                <tr>
                                    <td><?= htmlspecialchars($it['name'] ?? 'ID '.$it['product_id']) ?></td>
                                    <td><?= $it['quantity'] ?></td>
                                    <td><?= number_format($it['price'],0,',','.') ?> ₫</td>
                                    <td><?= number_format($it['price']*$it['quantity'],0,',','.') ?> ₫</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a href="orders.php" class="btn btn-secondary">Quay lại danh sách</a>
    <?php elseif (empty($orders)): ?>
        <div class="alert alert-info text-center py-5 shadow">
            <i class="fas fa-shopping-bag fa-4x mb-4 text-primary"></i>
            <h4>Bạn chưa có đơn hàng nào</h4>
            <p>Hãy bắt đầu mua sắm ngay hôm nay!</p>
            <a href="products.php" class="btn btn-primary btn-lg mt-3">
                <i class="fas fa-shopping-cart me-2"></i>Xem sản phẩm
            </a>
        </div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>STT</th>
                                <th>Ngày đặt</th>
                                <th>Số sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th width="120"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($orders as $order): ?>
                                <?php
                                $status_text = match($order['status']) {
                                    'pending'    => 'Chờ xác nhận',
                                    'processing' => 'Đang xử lý',
                                    'completed'  => 'Hoàn thành',
                                    'cancelled'  => 'Đã hủy',
                                    default      => 'Không xác định',
                                };
                                $status_class = match($order['status']) {
                                    'pending'    => 'bg-warning text-dark',
                                    'processing' => 'bg-info text-white',
                                    'completed'  => 'bg-success text-white',
                                    'cancelled'  => 'bg-danger text-white',
                                    default      => 'bg-secondary text-white',
                                };
                                ?>
                                <tr>
                                    <td><strong><?= $i++ ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td><?= $order['item_count'] ?></td>
                                    <td class="fw-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</td>
                                    <td><span class="badge <?= $status_class ?> px-3 py-2 fs-6"><?= $status_text ?></span></td>
                                    <td>
                                        <a href="orders.php?view=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>