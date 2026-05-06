<?php require_once dirname(__DIR__, 4) . '/includes/header.php'; ?>

<h1 class="mb-4">Giỏ hàng của bạn</h1>

<?php if (empty($cart)): ?>
    <div class="alert alert-info py-5 text-center">
        <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
        <p class="lead">Giỏ hàng của bạn đang trống.</p>
        <a href="index.php?route=products" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
<?php else: ?>
    <form action="index.php?route=update_cart" method="POST">
        <?= csrf_field() ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th style="width: 150px;">Số lượng</th>
                        <th>Tổng</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($cart as $id => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <?php if ($item['image']): ?>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: contain;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 80px; height: 80px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($item['name']) ?></div>
                            </td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                            <td>
                                <input type="number" name="quantity[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm">
                            </td>
                            <td class="fw-bold text-danger"><?= number_format($subtotal, 0, ',', '.') ?> ₫</td>
                            <td>
                                <a href="index.php?route=remove_from_cart&id=<?= $id ?>" class="text-danger" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light border-top">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                        <td colspan="2" class="h4 fw-bold text-danger"><?= number_format($total, 0, ',', '.') ?> ₫</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php?route=products" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
            </a>
            <div>
                <button type="submit" class="btn btn-info text-white me-2">
                    <i class="fas fa-sync-alt me-2"></i>Cập nhật giỏ hàng
                </button>
                <a href="index.php?route=checkout" class="btn btn-success">
                    Tiến hành thanh toán<i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php require_once dirname(__DIR__, 4) . '/includes/footer.php'; ?>
