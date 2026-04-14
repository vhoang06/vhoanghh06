<?php
// admin/products.php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

requireAdmin();

$page_title = "Quản lý sản phẩm";

// Tạo thư mục lưu ảnh
$upload_dir = '../assets/images/products/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// Hàm upload ảnh
function uploadImage($file) {
    global $upload_dir;
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2*1024*1024) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) return null;
    $filename = uniqid('prod_') . '.' . $ext;
    $target = $upload_dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $target)) return 'assets/images/products/' . $filename;
    return null;
}

// Xử lý xóa sản phẩm (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    csrf_validate();
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $old_image = $stmt->fetchColumn();
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    if ($old_image && file_exists('../' . $old_image)) unlink('../' . $old_image);

    // Nếu bảng rỗng sau khi xóa thì reset AUTO_INCREMENT về 1
    $cnt = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($cnt === 0) {
        try {
            $pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");
        } catch (Exception $e) {
            // Nếu không có quyền ALTER TABLE thì bỏ qua
        }
    }

    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Xóa sản phẩm thành công!'];
    echo json_encode(['success' => true]);
    exit;
}

// Xử lý cập nhật tồn kho nhanh (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'update_stock') {
    csrf_validate();
    $id = (int)$_POST['id'];
    $stock = (int)$_POST['stock'];
    $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?")->execute([$stock, $id]);
    echo json_encode(['success' => true, 'stock' => $stock]);
    exit;
}

// Xử lý thêm / sửa sản phẩm
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$product = ['name'=>'','category_id'=>0,'brand_id'=>0,'price'=>0,'description'=>'','stock'=>0,'image'=>''];

if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $product = $stmt->fetch() ?: $product;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    csrf_validate();
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $price = (float)str_replace([',','.'], '', $_POST['price'] ?? '0');
    $description = trim($_POST['description'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $image = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $new_image = uploadImage($_FILES['image']);
        if ($new_image) {
            if ($image && file_exists('../' . $image)) unlink('../' . $image);
            $image = $new_image;
        }
    }

    $errors = [];
    if (empty($name)) $errors[] = "Tên sản phẩm không được để trống";
    if ($price <= 0) $errors[] = "Giá phải lớn hơn 0";
    if ($category_id <= 0) $errors[] = "Vui lòng chọn danh mục";

    if (empty($errors)) {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, brand_id=?, price=?, description=?, stock=?, image=? WHERE id=?");
            $stmt->execute([$name, $category_id, $brand_id, $price, $description, $stock, $image, $edit_id]);
            $_SESSION['toast'] = ['type'=>'success', 'message'=>'Cập nhật sản phẩm thành công!'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, category_id, brand_id, price, description, stock, image) VALUES (?,?,?,?,?,?,?)");
            if ($stmt->execute([$name, $category_id, $brand_id, $price, $description, $stock, $image])) {
                $_SESSION['toast'] = ['type'=>'success', 'message'=>'Thêm sản phẩm mới thành công!'];
            }
        }
        header("Location: products.php?reset=" . time());
        exit;
    } else {
        $_SESSION['toast'] = ['type'=>'danger', 'message'=>implode('<br>', array_map('htmlspecialchars', $errors))];
    }
}

// Tìm kiếm, lọc, phân trang (giữ nguyên từ phiên bản trước)
$search = trim($_GET['search'] ?? '');
$cat_filter = (int)($_GET['category'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
$params = [];
if ($search) { $where .= " AND p.name LIKE ?"; $params[] = "%$search%"; }
if ($cat_filter) { $where .= " AND p.category_id = ?"; $params[] = $cat_filter; }

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$pages = ceil($total / $limit);

$sql = "SELECT p.*, c.name AS cat_name, b.name AS brand_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        $where ORDER BY p.id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Office Supplies Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .sidebar a { color: rgba(255,255,255,0.85); }
        .sidebar a:hover, .sidebar .active { background: #343a40; color: white; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 1055; }
        .product-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .stock-display { cursor: pointer; text-decoration: underline dotted; }
        .stock-input { width: 80px; }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="products.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags me-2"></i> Danh mục</a></li>
            <li class="nav-item"><a class="nav-link" href="brands.php"><i class="fas fa-copyright me-2"></i> Thương hiệu</a></li>
            <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-receipt me-2"></i> Đơn hàng</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i> Người dùng</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Quản lý sản phẩm</h1>
            <a href="?edit=0" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Thêm sản phẩm</a>
        </div>

        <!-- Form tìm kiếm & lọc -->
        <form method="get" class="row g-3 mb-4">
            <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $cat_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button></div>
        </form>

        <!-- Toast -->
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

        <!-- Form thêm/sửa sản phẩm -->
        <?php if (isset($_GET['edit'])): ?>
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <?= $edit_id > 0 ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' ?>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="row g-3">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_product" value="1">
                    <div class="col-md-6">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giá (₫) <span class="text-danger">*</span></label>
                        <input type="text" name="price" class="form-control" value="<?= number_format($product['price'], 0, ',', '.') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thương hiệu</label>
                        <select name="brand_id" class="form-select">
                            <option value="">-- Không --</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= $b['id'] ?>" <?= $product['brand_id'] == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Ảnh sản phẩm</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if ($product['image']): ?>
                            <img src="../<?= htmlspecialchars($product['image']) ?>" alt="Current" class="img-thumbnail mt-2" style="max-height:140px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <a href="products.php" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bảng sản phẩm -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>STT</th>
                                <th>Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th width="160">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">Không có sản phẩm nào</td></tr>
                            <?php else: ?>
                                <?php $stt = ($page - 1) * $limit + 1; foreach ($products as $p): ?>
                                    <tr>
                                        <td><?= $stt++ ?></td>
                                        <td>
                                            <?php if ($p['image']): ?>
                                                <img src="../<?= htmlspecialchars($p['image']) ?>" class="product-thumb" alt="">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width:60px;height:60px;border-radius:4px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($p['name']) ?></td>
                                        <td><?= htmlspecialchars($p['cat_name'] ?? '—') ?></td>
                                        <td class="fw-bold"><?= number_format($p['price'], 0, ',', '.') ?> ₫</td>
                                        <td>
                                            <span class="stock-display" data-id="<?= $p['id'] ?>"><?= $p['stock'] ?></span>
                                            <input type="number" class="stock-input form-control d-none" value="<?= $p['stock'] ?>" min="0">
                                        </td>
                                        <td>
                                            <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Phân trang -->
        <?php if ($pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $cat_filter ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>
</div>

<!-- Modal xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sản phẩm <strong id="deleteName"></strong> không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toast
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toast').forEach(t => new bootstrap.Toast(t).show());
});

// Inline edit tồn kho
document.querySelectorAll('.stock-display').forEach(el => {
    el.addEventListener('click', () => {
        // Hide display and show input, seed current value
        el.classList.add('d-none');
        const input = el.nextElementSibling;
        input.value = el.textContent.trim();
        input.classList.remove('d-none');
        input.focus();
    });
});

document.querySelectorAll('.stock-input').forEach(input => {
    const finish = () => {
        const display = input.previousElementSibling;
        const id = display.dataset.id;
        let stock = parseInt(input.value, 10);
        if (isNaN(stock) || stock < 0) stock = 0;

        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=update_stock&id=${id}&stock=${stock}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) display.textContent = data.stock;
            display.classList.remove('d-none');
            input.classList.add('d-none');
        })
        .catch(() => {
            // On error revert UI
            display.classList.remove('d-none');
            input.classList.add('d-none');
        });
    };

    const cancel = () => {
        const display = input.previousElementSibling;
        display.classList.remove('d-none');
        input.classList.add('d-none');
        input.value = display.textContent.trim();
    };

    input.addEventListener('blur', finish);
    input.addEventListener('keypress', e => { if (e.key === 'Enter') input.blur(); });
    input.addEventListener('keydown', e => { if (e.key === 'Escape') cancel(); });
});

// Xóa AJAX
let deleteId = null;
const deleteModal = new bootstrap.Modal('#deleteModal');

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        deleteId = btn.dataset.id;
        document.getElementById('deleteName').textContent = btn.dataset.name;
        deleteModal.show();
    });
});

document.getElementById('confirmDelete')?.addEventListener('click', () => {
    if (!deleteId) return;
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=delete&id=${deleteId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            deleteModal.hide();
            // Reload trang sau khi xóa để cập nhật số lượng và hiển thị toast
            location.reload();
        }
    });
});
</script>
</body>
</html>