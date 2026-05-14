<?php
// checkout.php
$page_title = "Thanh toán";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/checkout_functions.php';
require_once __DIR__ . '/../includes/order_functions.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php');
}

// Xử lý checkout với sản phẩm được chọn từ giỏ hàng
if (empty($_SESSION['checkout_items'])) {
    $_SESSION['toast'] = ['type' => 'warning', 'message' => 'Giỏ hàng của bạn đang trống!'];
    redirect('cart.php');
}

// Sử dụng checkout_items nếu có, nếu không dùng toàn bộ giỏ hàng
$cart_items = $_SESSION['checkout_items'] ?? [];

// Tính tổng tiền
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$errors = [];
$success = false;
$order_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shipping_name'])) {
    $shipping_name    = trim($_POST['shipping_name'] ?? '');
    $shipping_phone   = trim($_POST['shipping_phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $note             = trim($_POST['note'] ?? '');

    if (empty($shipping_name))    $errors[] = "Vui lòng nhập họ tên người nhận";
    if (empty($shipping_phone))   $errors[] = "Vui lòng nhập số điện thoại";
    if (empty($shipping_address)) $errors[] = "Vui lòng nhập địa chỉ giao hàng";

    if (empty($errors)) {
        $result = placeOrder($pdo, $_SESSION['user_id'], $cart_items, $shipping_name, $shipping_phone, $shipping_address, $note);
        if ($result['success']) {
            $order_id = $result['order_id'];
            $success = true;
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Đặt hàng thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.'
            ];

            if (!empty($_SESSION['checkout_items'])) {
                $checked_product_ids = getCheckedProductIds($_SESSION['checkout_items']);
                $_SESSION['cart'] = removeCheckedCartItems($_SESSION['cart'] ?? [], $checked_product_ids);
            } else {
                unset($_SESSION['cart']);
            }
            unset($_SESSION['checkout_items']);
        } else {
            $errors = array_merge($errors, $result['errors']);
        }
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4 text-center">Thanh toán đơn hàng</h1>

    <?php if ($success): ?>
        <div class="alert alert-success text-center py-5">
            <i class="fas fa-check-circle fa-4x mb-3 text-success"></i>
            <h3>Đặt hàng thành công!</h3>
            <p>Cảm ơn bạn đã tin tưởng và mua sắm tại <strong>Office Supplies</strong>.</p>
            <p><strong>Mã đơn hàng:</strong> #<?= $order_id ?></p>
            <p><strong>Tổng tiền:</strong> <span class="fs-4 fw-bold text-danger"><?= number_format($total, 0, ',', '.') ?> ₫</span></p>
            <p>Phương thức: <strong>Thanh toán khi nhận hàng (COD)</strong></p>
            <p>Chúng tôi sẽ liên hệ xác nhận và giao hàng trong thời gian sớm nhất.</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-primary btn-lg me-3">Về trang chủ</a>
                <a href="orders.php" class="btn btn-outline-primary btn-lg">Xem đơn hàng của tôi</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Form thông tin giao hàng -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Họ và tên người nhận <span class="text-danger">*</span></label>
                                <input type="text" name="shipping_name" class="form-control" required 
                                       value="<?= htmlspecialchars($_POST['shipping_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="shipping_phone" class="form-control" required 
                                       value="<?= htmlspecialchars($_POST['shipping_phone'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ giao hàng chi tiết <span class="text-danger">*</span></label>
                                <textarea name="shipping_address" class="form-control" rows="3" required>
<?= htmlspecialchars($_POST['shipping_address'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Ghi chú cho đơn hàng (tùy chọn)</label>
                                <textarea name="note" class="form-control" rows="2"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i> Xác nhận đặt hàng (COD)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cart_items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                                    </div>
                                    <span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫</span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between fw-bold px-0 border-top mt-2">
                                <span>Tổng tiền</span>
                                <span class="text-danger fs-5"><?= number_format($total, 0, ',', '.') ?> ₫</span>
                            </li>
                        </ul>
                        <small class="text-muted d-block mt-3">
                            <i class="fas fa-truck me-1"></i> Giao hàng miễn phí toàn quốc (COD)
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
