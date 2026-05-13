<?php require_once __DIR__ . '/layout/header.php'; ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { height: 38px; border: 1px solid #dee2e6; border-radius: 0.375rem; padding-top: 5px; }
    .product-row { background: #f8f9fa; border-radius: 12px; padding: 15px; margin-bottom: 10px; position: relative; }
    .btn-remove { position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; }
</style>

<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="mb-0 fw-bold">Tạo Đơn hàng mới</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="row g-4">
                        <!-- Thông tin khách hàng & Giao hàng -->
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-user-circle me-2"></i>Thông tin khách hàng</h6>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Tìm tài khoản khách</label>
                                <select name="user_id" class="form-select searchable-select" required>
                                    <option value="">Gõ tên hoặc email để tìm...</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>">
                                            <?= htmlspecialchars($u['username']) ?> 
                                            <?= !empty($u['email']) ? '(' . htmlspecialchars($u['email']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tên người nhận</label>
                                <input type="text" name="shipping_name" class="form-control" placeholder="Họ tên người nhận (không bắt buộc)...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Số điện thoại</label>
                                <input type="text" name="shipping_phone" class="form-control" placeholder="SĐT liên hệ (không bắt buộc)...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Địa chỉ giao hàng</label>
                                <textarea name="shipping_address" class="form-control" rows="3" placeholder="Địa chỉ chi tiết (không bắt buộc)..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú thêm..."></textarea>
                            </div>
                        </div>

                        <!-- Sản phẩm & Thanh toán -->
                        <div class="col-md-7">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-shopping-cart me-2"></i>Sản phẩm trong đơn</h6>
                            
                            <div id="product-container">
                                <!-- Một dòng sản phẩm mẫu -->
                                <div class="product-row shadow-sm border">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label extra-small text-muted mb-1">Tìm sản phẩm</label>
                                            <select name="product_id[]" class="form-select product-search" required>
                                                <option value="">Gõ tên sản phẩm để tìm...</option>
                                                <?php foreach ($products as $p): ?>
                                                    <option value="<?= $p['id'] ?>" data-stock="<?= $p['stock'] ?>">
                                                        <?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['stock'] ?>) - <?= number_format($p['price'], 0, ',', '.') ?> ₫
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label extra-small text-muted mb-1">Số lượng (Max: <span class="stock-display">--</span>)</label>
                                            <input type="number" name="quantity[]" class="form-control qty-input" value="1" min="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mt-3 w-100 py-2 border-dashed" onclick="addProductRow()">
                                <i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm khác
                            </button>

                            <div class="mt-4 pt-4 border-top">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Phương thức thanh toán</label>
                                        <select name="payment_method" class="form-select">
                                            <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                            <option value="bank">Chuyển khoản ngân hàng</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Trạng thái ban đầu</label>
                                        <select name="status" class="form-select">
                                            <option value="pending">Chờ xử lý</option>
                                            <option value="processing">Đang xử lý</option>
                                            <option value="completed">Đã hoàn thành</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top d-flex gap-2 justify-content-end">
                        <a href="index.php?route=admin/orders" class="btn btn-light rounded-pill px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">TẠO ĐƠN HÀNG NGAY</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    initSelect2();
    
    // Lắng nghe sự kiện thay đổi sản phẩm để cập nhật Max Stock
    $(document).on('change', '.product-search', function() {
        const selectedOption = $(this).find('option:selected');
        const stock = selectedOption.data('stock');
        const row = $(this).closest('.product-row');
        const qtyInput = row.find('.qty-input');
        const stockDisplay = row.find('.stock-display');
        
        if (stock !== undefined) {
            qtyInput.attr('max', stock);
            stockDisplay.text(stock);
            if (parseInt(qtyInput.val()) > stock) {
                qtyInput.val(stock);
            }
        } else {
            qtyInput.removeAttr('max');
            stockDisplay.text('--');
        }
    });
});

function initSelect2() {
    $('.searchable-select').select2({
        width: '100%',
        placeholder: "Gõ để tìm kiếm..."
    });
    
    $('.product-search').each(function() {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                width: '100%',
                placeholder: "Tìm sản phẩm..."
            });
        }
    });
}

function addProductRow() {
    const container = document.getElementById('product-container');
    const firstRow = container.querySelector('.product-row');
    
    // Destroy select2 before cloning to avoid issues
    const newRow = firstRow.cloneNode(true);
    
    // Clear values
    const select = newRow.querySelector('select');
    select.value = '';
    newRow.querySelector('input').value = 1;
    newRow.querySelector('.stock-display').innerText = '--';
    
    // Add remove button
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-danger btn-remove shadow-sm';
    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
    removeBtn.onclick = function() { newRow.remove(); };
    newRow.appendChild(removeBtn);
    
    container.appendChild(newRow);
    
    // Re-init Select2
    $(select).removeClass('select2-hidden-accessible').next('.select2-container').remove();
    initSelect2();
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
