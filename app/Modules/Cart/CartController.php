<?php

namespace App\Modules\Cart;

use App\Core\Controller;

class CartController extends Controller
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
    }

    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];
        $this->view('Cart', 'index', [
            'page_title' => 'Giỏ hàng',
            'cart' => $cart
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)($_POST['quantity'] ?? 1);

            if ($product_id > 0 && $quantity > 0) {
                $product = $this->cartModel->getProductById($product_id);
                if ($product) {
                    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
                    
                    if (isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$product_id] = [
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image' => $product['image'],
                            'quantity' => $quantity
                        ];
                    }
                    flash('success', "Đã thêm <b>{$product['name']}</b> vào giỏ hàng!");
                }
            }
        }
        $this->redirect('cart');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();
            $quantities = $_POST['quantity'] ?? [];
            foreach ($quantities as $id => $qty) {
                $qty = (int)$qty;
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    $_SESSION['cart'][$id]['quantity'] = $qty;
                }
            }
            flash('success', 'Đã cập nhật giỏ hàng!');
        }
        $this->redirect('cart');
    }

    public function remove()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0 && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            flash('info', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }
        $this->redirect('index.php?route=cart');
    }
}
