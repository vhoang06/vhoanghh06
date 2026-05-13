<?php

namespace App\Modules\Admin;

use App\Core\Controller;
use App\Modules\Product\ProductModel;

class AdminController extends Controller
{
    private $adminModel;
    private $productModel;

    public function __construct()
    {
        // Security check
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: " . APP_URL . "/index.php?route=login");
            exit;
        }
        
        $this->adminModel = new AdminModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $stats = $this->adminModel->getStats();
        $recent_orders = $this->adminModel->getRecentOrders(5);
        $this->view('Admin', 'dashboard', [
            'page_title' => 'Admin Dashboard',
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'active_menu' => 'dashboard'
        ]);
    }

    public function products()
    {
        $products = $this->adminModel->getAllProducts();
        $this->view('Admin', 'products', [
            'page_title' => 'Quản lý Sản phẩm',
            'products' => $products,
            'active_menu' => 'products'
        ]);
    }

    public function product_add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            if ($this->adminModel->saveProduct($data)) {
                $this->redirect('index.php?route=admin/products&success=1');
            }
        }
        
        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();
        
        $this->view('Admin', 'product_form', [
            'page_title' => 'Thêm Sản phẩm',
            'categories' => $categories,
            'brands' => $brands,
            'product' => null,
            'active_menu' => 'products'
        ]);
    }

    public function product_edit()
    {
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            $data['id'] = $id;
            if ($this->adminModel->saveProduct($data)) {
                $this->redirect('index.php?route=admin/products&success=1');
            }
        }
        
        $product = $this->productModel->getProductById($id);
        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();
        
        $this->view('Admin', 'product_form', [
            'page_title' => 'Sửa Sản phẩm',
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'active_menu' => 'products'
        ]);
    }

    public function product_delete()
    {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            $this->adminModel->deleteProduct($id);
        }
        $this->redirect('index.php?route=admin/products&deleted=1');
    }

    public function orders()
    {
        $orders = $this->adminModel->getAllOrders();
        $this->view('Admin', 'orders', [
            'page_title' => 'Quản lý Đơn hàng',
            'orders' => $orders,
            'active_menu' => 'orders'
        ]);
    }

    public function order_update()
    {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        if ($id > 0 && $status !== '') {
            $this->adminModel->updateOrderStatus($id, $status);
        }
        $this->redirect('index.php?route=admin/orders&updated=1');
    }

    public function order_detail()
    {
        $id = $_GET['id'] ?? 0;
        $order = $this->adminModel->getOrderById($id);
        if (!$order) {
            $this->redirect('index.php?route=admin/orders');
        }
        
        $items = $this->adminModel->getOrderItems($id);
        
        $this->view('Admin', 'order_detail', [
            'page_title' => 'Chi tiết Đơn hàng #' . $id,
            'order' => $order,
            'items' => $items,
            'active_menu' => 'orders'
        ]);
    }

    public function order_add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => (int)($_POST['user_id'] ?? 1),
                'status' => $_POST['status'] ?? 'pending',
                'payment_method' => $_POST['payment_method'] ?? 'cod',
                'shipping_name' => trim($_POST['shipping_name'] ?? ''),
                'shipping_phone' => trim($_POST['shipping_phone'] ?? ''),
                'shipping_address' => trim($_POST['shipping_address'] ?? ''),
                'note' => trim($_POST['note'] ?? ''),
                'total_amount' => 0
            ];

            $product_ids = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $items = [];
            $total = 0;

            foreach ($product_ids as $index => $pid) {
                if (empty($pid)) continue;
                $product = $this->productModel->getProductById($pid);
                if ($product) {
                    $qty = (int)($quantities[$index] ?? 1);
                    $price = (float)$product['price'];
                    $items[] = [
                        'product_id' => $pid,
                        'quantity' => $qty,
                        'price' => $price
                    ];
                    $total += $price * $qty;
                }
            }

            $data['total_amount'] = $total;

            if (!empty($items) && $this->adminModel->createOrder($data, $items)) {
                $this->redirect('index.php?route=admin/orders&success=1');
            }
        }

        $users = $this->adminModel->getAllUsers();
        $products = $this->adminModel->getAllProducts();
        
        $this->view('Admin', 'order_form', [
            'page_title' => 'Tạo Đơn hàng mới',
            'users' => $users,
            'products' => $products,
            'active_menu' => 'orders'
        ]);
    }

    public function order_edit()
    {
        $id = $_GET['id'] ?? 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'shipping_name' => trim($_POST['shipping_name'] ?? ''),
                'shipping_phone' => trim($_POST['shipping_phone'] ?? ''),
                'shipping_address' => trim($_POST['shipping_address'] ?? ''),
                'note' => trim($_POST['note'] ?? '')
            ];
            if ($this->adminModel->updateOrderShipping($id, $data)) {
                $this->redirect('index.php?route=admin/orders/detail&id=' . $id . '&success=1');
            }
        }

        $order = $this->adminModel->getOrderById($id);
        if (!$order) $this->redirect('index.php?route=admin/orders');

        $this->view('Admin', 'order_edit', [
            'page_title' => 'Sửa Đơn hàng #' . $id,
            'order' => $order,
            'active_menu' => 'orders'
        ]);
    }

    private function getPostData()
    {
        return [
            'name' => $_POST['name'] ?? '',
            'category_id' => $_POST['category_id'] ?? 0,
            'brand_id' => $_POST['brand_id'] ?? 0,
            'price' => $_POST['price'] ?? 0,
            'stock' => $_POST['stock'] ?? 0,
            'description' => $_POST['description'] ?? '',
            'image' => $_POST['image'] ?? ''
        ];
    }
}
