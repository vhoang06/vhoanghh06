<?php

namespace App\Modules\Order;

use App\Core\Controller;

class OrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        requireLogin();
        $orders = $this->orderModel->getUserOrders(getCurrentUserId());
        $this->view('Order', 'list', [
            'page_title' => 'Đơn hàng của tôi',
            'orders' => $orders
        ]);
    }

    public function checkout()
    {
        requireLogin();
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            flash('warning', 'Giỏ hàng của bạn đang trống.');
            $this->redirect('index.php?route=products');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();

            $data = [
                'user_id' => getCurrentUserId(),
                'total_amount' => array_reduce($cart, function($carry, $item) { return $carry + ($item['price'] * $item['quantity']); }, 0),
                'shipping_name' => trim($_POST['shipping_name'] ?? ''),
                'shipping_phone' => trim($_POST['shipping_phone'] ?? ''),
                'shipping_address' => trim($_POST['shipping_address'] ?? ''),
                'note' => trim($_POST['note'] ?? ''),
                'payment_method' => $_POST['payment_method'] ?? 'cod'
            ];

            if (empty($data['shipping_name'])) $errors[] = "Vui lòng nhập tên người nhận";
            if (!isValidPhone($data['shipping_phone'])) $errors[] = "Số điện thoại không hợp lệ";
            if (empty($data['shipping_address'])) $errors[] = "Vui lòng nhập địa chỉ nhận hàng";

            if (empty($errors)) {
                $orderId = $this->orderModel->createOrder($data, $cart);
                if ($orderId) {
                    unset($_SESSION['cart']);
                    flash('success', "Đặt hàng thành công! Mã đơn hàng: #{$orderId}");
                    $this->redirect('index.php?route=orders');
                } else {
                    $errors[] = "Đã có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.";
                }
            }
        }

        $this->view('Order', 'checkout', [
            'page_title' => 'Thanh toán',
            'cart' => $cart,
            'errors' => $errors
        ]);
    }
}
