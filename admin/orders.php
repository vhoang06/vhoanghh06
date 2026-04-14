<?php
// admin/orders.php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

requireAdmin();

$page_title = 'Quản lý đơn hàng';

// Các trạng thái đơn hàng và nhãn hiển thị tiếng Việt
$statusLabels = [
    'pending'    => 'Chờ xác nhận',
    'processing' => 'Đang xử lý',
    'completed'  => 'Đã xác nhận',
    'cancelled'  => 'Đã hủy',
];

// Xử lý cập nhật trạng thái (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['completed','cancelled'];
    if ($order_id > 0 && in_array($status, $allowed)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $order_id]);
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Cập nhật trạng thái đơn hàng thành công!'];
    }
    header('Location: orders.php');
    exit;
}

// Xử lý xem chi tiết (GET id)
$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($view_id > 0) {
    $stmt = $pdo->prepare('SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?');
    $stmt->execute([$view_id]);
    $order = $stmt->fetch();
    if (!$order) {
        $_SESSION['toast'] = ['type'=>'danger','message'=>'Không tìm thấy đơn hàng'];
        header('Location: orders.php'); exit;
    }
    $items = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
    $items->execute([$view_id]);
    $order_items = $items->fetchAll();
}

// Lấy danh sách đơn hàng chờ xác nhận
$orders = $pdo->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.status = 'pending' ORDER BY o.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Office Supplies Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style> body { background: #f4f6f9; } .sidebar { min-height: 100vh; background: #212529; color:white; } .sidebar a{ color: rgba(255,255,255,0.85);} </style>
</head>
<body>
<div class="d-flex">
    <div class="sidebar col-md-3 col-lg-2 p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags me-2"></i> Danh mục</a></li>
            <li class="nav-item"><a class="nav-link" href="brands.php"><i class="fas fa-copyright me-2"></i> Thương hiệu</a></li>
            <li class="nav-item"><a class="nav-link active" href="orders.php"><i class="fas fa-receipt me-2"></i> Đơn hàng</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i> Người dùng</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
        <h1 class="mb-4">Quản lý đơn hàng</h1>

        <div class="toast-container">
            <?php if (isset($_SESSION['toast'])): $t = $_SESSION['toast']; unset($_SESSION['toast']); ?>
                <div class="toast align-items-center text-white bg-<?= $t['type'] ?> border-0" role="alert" data-bs-autohide="true" data-bs-delay="4000">
                    <div class="d-flex">
                        <div class="toast-body"><?= $t['message'] ?></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($view_id > 0): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Đơn hàng #<?= $order['id'] ?></strong>
                        <div class="small">Người đặt: <?= htmlspecialchars($order['username'] ?? $order['user_id']) ?> — <?= htmlspecialchars($order['email'] ?? '') ?></div>
                    </div>
                    <div>
                        <form method="post" class="d-inline-flex align-items-center">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select form-select-sm me-2">
                                <?php foreach (['pending','completed','cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $order['status']==$s ? 'selected' : '' ?>><?= htmlspecialchars($statusLabels[$s] ?? ucfirst($s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-light">Cập nhật</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Tổng tiền:</strong> <?= number_format($order['total_amount'],0,',','.') ?> ₫</p>
                    <p><strong>Phương thức:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Trạng thái:</strong> <?= htmlspecialchars($statusLabels[$order['status']] ?? $order['status']) ?></p>
                    <p><strong>Địa chỉ giao hàng:</strong><br><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    <p><strong>Ghi chú:</strong><br><?= nl2br(htmlspecialchars($order['note'])) ?></p>

                    <h5 class="mt-3">Sản phẩm</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th><th>Thành tiền</th></tr></thead>
                            <tbody>
                                <?php foreach ($order_items as $it): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($it['name'] ?? ('ID '.$it['product_id'])) ?></td>
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
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Người đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr><td colspan="6" class="text-center py-5">Chưa có đơn hàng nào</td></tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $index => $o): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($o['username'] ?? $o['user_id']) ?></td>
                                            <td><?= number_format($o['total_amount'],0,',','.') ?> ₫</td>
                                            <td><?= htmlspecialchars($statusLabels[$o['status']] ?? $o['status']) ?></td>
                                            <td><?= htmlspecialchars($o['created_at']) ?></td>
                                            <td>
                                                <a href="orders.php?view=<?= $o['id'] ?>" class="btn btn-sm btn-primary">Xem</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', ()=>{document.querySelectorAll('.toast').forEach(t=>new bootstrap.Toast(t).show());});</script>
</body>
</html>
