<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<h1 class="mb-4">Thanh toán</h1>

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">Thông tin giao hàng</h5>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="index.php?route=checkout" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Tên người nhận</label>
                        <input type="text" name="shipping_name" class="form-control" required placeholder="Họ và tên">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="shipping_phone" class="form-control" required placeholder="VD: 0369xxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ nhận hàng</label>
                        <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="VD: Giao giờ hành chính..."></textarea>
                    </div>

                    <h5 class="mb-3">Phương thức thanh toán</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                        <label class="form-check-link" for="cod">
                            Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank">
                        <label class="form-check-link" for="bank">
                            Chuyển khoản ngân hàng
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                        <i class="fas fa-check-circle me-2"></i>Xác nhận đặt hàng
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">Tóm tắt đơn hàng</h5>
                <div class="order-summary">
                    <?php 
                    $total = 0;
                    foreach ($cart as $id => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <span class="fw-bold"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="text-muted small">x <?= $item['quantity'] ?></span>
                            </div>
                            <div class="text-danger fw-bold">
                                <?= number_format($subtotal, 0, ',', '.') ?> ₫
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="h5 mb-0">Tổng cộng:</span>
                    <span class="h4 mb-0 text-danger fw-bold"><?= number_format($total, 0, ',', '.') ?> ₫</span>
                </div>
                <hr class="my-4">
                <p class="small text-muted"><i class="fas fa-info-circle me-2"></i>Đơn hàng sẽ được xử lý trong vòng 24h làm việc.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
