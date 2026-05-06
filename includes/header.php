<?php
// includes/header.php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Supplies<?= isset($page_title) ? ' - ' . $page_title : '' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .navbar-brand { font-weight: bold; color: #0d6efd !important; display: flex; align-items: center; gap: 0.75rem; }
        .brand-logo { width: 42px; height: 42px; }
        .brand-logo img { width: 100%; height: auto; display: block; }
        .product-card { transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.12); }
        .price { color: #e63946; font-weight: bold; font-size: 1.3rem; }
        .product-img { width: 100%; height: 180px; object-fit: contain; background: #f8f9fa; border-radius: 8px 8px 0 0; border-bottom: 1px solid #dee2e6; }
        .card-img-top-placeholder { height: 180px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 1.1rem; border-radius: 8px 8px 0 0; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 1055; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-logo"><img src="assets/images/logo.svg" alt="Office Supplies"></span>
            <span>Office Supplies</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?route=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?route=products">Sản phẩm</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?route=brands">Thương hiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?route=contact">Liên hệ</a></li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?route=cart">
                            <i class="fas fa-shopping-cart"></i> Giỏ hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?route=orders">
                            <i class="fas fa-clipboard-list"></i> Đơn hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">Xin chào, <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="index.php?route=logout">Đăng xuất</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?route=login">Đăng nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?route=register">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4" style="min-height: 70vh;">

<?php // Flash messages display ?>
<?php if (!empty($_SESSION['flash'])):
    foreach ($_SESSION['flash'] as $msg):
        $type = htmlspecialchars($msg['type']);
        $message = $msg['message']; // Đã được escape khi set flash
?>
<div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php
    endforeach;
    unset($_SESSION['flash']);
endif; ?>