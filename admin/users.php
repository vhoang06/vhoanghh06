<?php
// admin/users.php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

requireAdmin();

$page_title = "Quản lý người dùng";

// Xử lý xóa người dùng (AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    csrf_validate();
    $id = (int)$_POST['id'];
    
    // Không cho xóa chính mình
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản của chính bạn!']);
    } else {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);

        // Nếu bảng rỗng sau khi xóa thì reset AUTO_INCREMENT về 1
        $cnt = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($cnt === 0) {
            try {
                $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
            } catch (Exception $e) {
                // Nếu không có quyền ALTER TABLE thì bỏ qua
            }
        }

        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Xóa người dùng thành công!'];
        echo json_encode(['success' => true]);
    }
    exit;
}

// Lấy danh sách người dùng
$users = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC")->fetchAll();
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
            <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags me-2"></i> Danh mục</a></li>
            <li class="nav-item"><a class="nav-link" href="brands.php"><i class="fas fa-copyright me-2"></i> Thương hiệu</a></li>
            <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-receipt me-2"></i> Đơn hàng</a></li>
            <li class="nav-item"><a class="nav-link active" href="users.php"><i class="fas fa-users me-2"></i> Người dùng</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <main class="col-md-9 col-lg-10 p-4">
        <h1 class="mb-4">Quản lý người dùng</h1>

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

        <!-- Danh sách người dùng -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                    <div class="alert alert-info text-center py-5 mb-0">Chưa có người dùng nào</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>STT</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Email</th>
                                    <th>Ngày tạo</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt = 1; foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $stt++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                <span class="badge bg-primary ms-2">Bạn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <button class="btn btn-sm btn-danger delete-btn" 
                                                        data-id="<?= $user['id'] ?>" 
                                                        data-username="<?= htmlspecialchars($user['username']) ?>">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted text-sm">-</span>
                                            <?php endif; ?>
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
                <h5 class="modal-title">Xác nhận xóa người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa người dùng <strong id="deleteUsername"></strong>?
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
        document.getElementById('deleteUsername').textContent = btn.dataset.username;
        deleteModal.show();
    });
});

document.getElementById('confirmDelete')?.addEventListener('click', () => {
    if (!deleteId) return;

    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=delete_user&id=${deleteId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            deleteModal.hide();
            // Reload trang sau khi xóa để cập nhật số lượng và hiển thị toast
            location.reload();
        } else {
            alert(data.message || 'Không thể xóa!');
        }
    });
    deleteId = null;
});
</script>
</body>
</html>
