<?php
// cart.php
$page_title = "Giỏ hàng";
require_once 'includes/header.php';

// Xử lý thêm sản phẩm vào giỏ (từ trang sản phẩm hoặc trang chủ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    csrf_validate();
    $product_id = (int)$_POST['product_id'];
    
    $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id'       => $product['id'],
                'name'     => $product['name'],
                'price'    => $product['price'],
                'image'    => $product['image'] ?? null,  // Lưu ảnh vào session
                'quantity' => 1
            ];
        }
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Đã thêm sản phẩm vào giỏ hàng!'];
        redirect('cart.php');
    }
}

// Xử lý checkout với sản phẩm được chọn từ giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_selected'])) {
    $selected_items = $_POST['selected_items'] ?? [];
    if (empty($selected_items)) {
        $_SESSION['toast'] = ['type' => 'warning', 'message' => 'Vui lòng chọn ít nhất một sản phẩm!'];
        redirect('cart.php');
    }
    
    // Tạo session tạm để lưu sản phẩm được chọn
    $_SESSION['checkout_items'] = [];
    foreach ($selected_items as $key) {
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['checkout_items'][] = $_SESSION['cart'][$key];
        }
    }
    
    // Chuyển hướng đến trang checkout
    redirect('checkout.php');
}

// Xử lý xóa sản phẩm
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] ?? [] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!'];
    redirect('cart.php');
}

// Tính tổng tiền
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info text-center py-5 shadow">
            <i class="fas fa-shopping-cart fa-4x mb-3 text-primary"></i>
            <h4>Giỏ hàng đang trống</h4>
            <p>Hãy thêm sản phẩm để tiếp tục mua sắm nhé!</p>
            <a href="products.php" class="btn btn-primary btn-lg mt-3">
                <i class="fas fa-arrow-left me-2"></i>Xem sản phẩm
            </a>
        </div>
    <?php else: ?>
        <form method="post" id="cart-form">
        <?= csrf_field() ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40"><input type="checkbox" id="select-all" class="form-check-input"></th>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th width="100">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $key => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                    ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_items[]" value="<?= $key ?>" class="form-check-input product-checkbox">
                            </td>
                            <td style="width: 80px;">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                                         style="width:60px; height:60px; object-fit:cover; border-radius:4px;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width:60px;height:60px;border-radius:4px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                            <td><?= $item['quantity'] ?></td>
                            <td class="fw-bold text-danger"><?= number_format($subtotal, 0, ',', '.') ?> ₫</td>
                            <td>
                                <a href="cart.php?remove=<?= $item['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="4" class="text-end fw-bold fs-5">TỔNG TIỀN:</td>
                        <td class="fw-bold fs-5 text-danger"><?= number_format($total, 0, ',', '.') ?> ₫</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-end mt-5">
            <div class="mb-3">
                <p class="fs-5"><strong>Tổng cộng (sản phẩm chọn):</strong> <span class="text-danger" id="selected-total">0 ₫</span></p>
            </div>
            <div class="d-flex justify-content-end align-items-center gap-3">
                <a href="products.php" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                </a>
                <button type="submit" name="checkout_selected" class="btn btn-success btn-lg" id="checkout-btn">
                    <i class="fas fa-credit-card me-2"></i>Thanh toán sản phẩm chọn
                </button>
            </div>
        </div>
        </form>
    <?php endif; ?>
</div>

<script>
const selectAllCheckbox = document.getElementById('select-all');
const productCheckboxes = document.querySelectorAll('.product-checkbox');
const selectedTotal = document.getElementById('selected-total');

function updateSelectedTotal() {
    let total = 0;
    productCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const row = checkbox.closest('tr');
            const priceText = row.querySelectorAll('td')[5].textContent.replace(/[^0-9]/g, '');
            total += parseInt(priceText);
        }
    });
    selectedTotal.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
}

selectAllCheckbox.addEventListener('change', () => {
    productCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateSelectedTotal();
});

productCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        selectAllCheckbox.checked = Array.from(productCheckboxes).every(cb => cb.checked);
        updateSelectedTotal();
    });
});

document.getElementById('checkout-btn').addEventListener('click', (e) => {
    const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
    if (checkedCount === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
        return false;
    }
    // Form sẽ submit bình thường nếu có sản phẩm được chọn
});
</script>

<?php require_once 'includes/footer.php'; ?>