<?php
// admin/categories.php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

requireAdmin();

$page_title = "Quản lý danh mục";

// Xử lý thêm / sửa danh mục
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$cat_name = '';

if ($edit_id > 0) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $row = $stmt->fetch();
    $cat_name = $row ? $row['name'] : '';
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        $errors[] = "Tên danh mục không được để trống";
    } else {
        if ($edit_id > 0) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $edit_id]);
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Cập nhật danh mục thành công!'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            if ($stmt->execute([$name])) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Thêm danh mục mới thành công!'];
            }
        }
        header("Location: categories.php?reset=" . time());
        exit;
    }
}

// Xử lý xóa danh mục (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'delete_category') {
    $id = (int)$_POST['id'];

    // Kiểm tra có sản phẩm liên kết không
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Danh mục đang được sử dụng trong sản phẩm, không thể xóa!']);
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);

        // Nếu bảng rỗng sau khi xóa thì reset AUTO_INCREMENT về 1
        $cnt = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($cnt === 0) {
            try {
                $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
            } catch (Exception $e) {
                // Nếu không có quyền ALTER TABLE thì bỏ qua
            }
        }

        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Xóa danh mục thành công!'];
        echo json_encode(['success' => true]);
    }
    exit;
}

// Lấy danh sách danh mục
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
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
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link active" href="categories.php"><i class="fas fa-tags me-2"></i> Danh mục</a></li>
            <li class="nav-item"><a class="nav-link" href="brands.php"><i class="fas fa-copyright me-2"></i> Thương hiệu</a></li>
            <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-receipt me-2"></i> Đơn hàng</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i> Người dùng</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="../user/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Quản lý danh mục</h1>
            <a href="?edit=0" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Thêm danh mục</a>
        </div>

        <!-- Toast -->
        <div class="toast-container">
            <?php if (isset($_SESSION['toast'])): 
                $t = $_SESSION['toast'];
                unset($_SESSION['toast']);
            ?>
                <div class="toast align-items-center text-white bg-<?= $t['type'] ?> border-0" role="alert" data-bs-autohide="true" data-bs-delay="4000">
                    <div class="d-flex">
                        <div class="toast-body"><?= $t['message'] ?></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form thêm/sửa -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <?= $edit_id > 0 ? 'Sửa danh mục' : 'Thêm danh mục mới' ?>
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

                <form method="post" class="row g-3">
                    <input type="hidden" name="save_category" value="1">
                    <div class="col-md-8">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat_name) ?>" required autofocus>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <?php if ($edit_id > 0): ?>
                            <a href="categories.php" class="btn btn-secondary flex-grow-1">Hủy</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary flex-grow-1">Lưu</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($categories)): ?>
                    <div class="alert alert-info text-center py-5 mb-0">Chưa có danh mục nào</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên danh mục</th>
                                    <th width="180">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt = 1; foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?= $stt++ ?></td>
                                        <td><?= htmlspecialchars($cat['name']) ?></td>
                                        <td>
                                            <a href="?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Sửa</a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                    data-id="<?= $cat['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($cat['name']) ?>">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa danh mục <strong id="deleteName"></strong>?<br>
                <small class="text-danger">Nếu danh mục đang có sản phẩm liên kết, hệ thống sẽ không cho phép xóa.</small>
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

// Modal xóa
let deleteId = null;
const deleteModal = new bootstrap.Modal('#deleteModal');

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        deleteId = btn.dataset.id;
        document.getElementById('deleteName').textContent = btn.dataset.name;
        deleteModal.show();
    });
});

document.getElementById('confirmDelete')?.addEventListener('click', (event) => {
    event.preventDefault();
    if (!deleteId) return;

    fetch(window.location.pathname, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        },
        body: new URLSearchParams({ action: 'delete_category', id: deleteId })
    })
    .then(async r => {
        const text = await r.text();
        if (!r.ok) {
            throw new Error(`HTTP ${r.status}: ${text}`);
        }
        try {
            return JSON.parse(text);
        } catch (err) {
            throw new Error(`Invalid JSON response: ${text}`);
        }
    })
    .then(data => {
        if (data.success) {
            deleteModal.hide();
            location.reload();
        } else {
            alert(data.message || `Không thể xóa! Response: ${JSON.stringify(data)}`);
        }
    })
    .catch(err => {
        alert(`Không thể xóa! ${err.message}`);
    });
    deleteId = null;
});
</script>
</body>
</html>