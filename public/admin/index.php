<?php
// admin/index.php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra quyền admin
requireAdmin();

$page_title = "Admin Dashboard";

// Thống kê nhanh
$total_products   = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_brands     = $pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn();
$total_users      = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
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
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .sidebar a { color: rgba(255,255,255,0.85); }
        .sidebar a:hover, .sidebar .active { background: #343a40; color: white; }
        .card { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
            <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags me-2"></i> Danh mục</a></li>
            <li class="nav-item"><a class="nav-link" href="brands.php"><i class="fas fa-copyright me-2"></i> Thương hiệu</a></li>
            <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-receipt me-2"></i> Đơn hàng</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users me-2"></i> Người dùng</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="../index.php?route=logout?redirect=admin"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="col-md-9 col-lg-10 p-4">
        <h1 class="mb-4">Dashboard Quản trị</h1>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white text-center p-4">
                    <h5>Sản phẩm</h5>
                    <h2 class="mb-0"><?= number_format($total_products) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white text-center p-4">
                    <h5>Danh mục</h5>
                    <h2 class="mb-0"><?= number_format($total_categories) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white text-center p-4">
                    <h5>Thương hiệu</h5>
                    <h2 class="mb-0"><?= number_format($total_brands) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark text-center p-4">
                    <h5>Người dùng</h5>
                    <h2 class="mb-0"><?= number_format($total_users) ?></h2>
                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header">Thông tin nhanh</div>
            <div class="card-body">
                <p class="text-muted">Chào mừng bạn đến với bảng điều khiển quản trị. Bạn có thể quản lý sản phẩm, danh mục, thương hiệu từ menu bên trái.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
