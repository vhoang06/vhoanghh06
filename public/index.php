<?php
/**
 * Front Controller
 */

// Autoloading
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Manual fallback if composer hasn't been run yet
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/../app/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) return;

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

// Load configurations
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/config.php';

use App\Core\Router;

$router = new Router();

// Core & Home
$router->add('home', 'App\Modules\Home\HomeController', 'index');

// Auth
$router->add('login', 'App\Modules\Auth\AuthController', 'login');
$router->add('logout', 'App\Modules\Auth\AuthController', 'logout');
$router->add('register', 'App\Modules\Auth\AuthController', 'register');
$router->add('verify_email', 'App\Modules\Auth\AuthController', 'verify_email');
$router->add('forgot_password', 'App\Modules\Auth\AuthController', 'forgot_password');
$router->add('reset_password', 'App\Modules\Auth\AuthController', 'reset_password');

// Products
$router->add('products', 'App\Modules\Product\ProductController', 'index');
$router->add('product_detail', 'App\Modules\Product\ProductController', 'detail');
$router->add('brands', 'App\Modules\Product\ProductController', 'brands');

// Cart
$router->add('cart', 'App\Modules\Cart\CartController', 'index');
$router->add('add_to_cart', 'App\Modules\Cart\CartController', 'add');
$router->add('update_cart', 'App\Modules\Cart\CartController', 'update');
$router->add('remove_from_cart', 'App\Modules\Cart\CartController', 'remove');

// Orders
$router->add('orders', 'App\Modules\Order\OrderController', 'index');
$router->add('checkout', 'App\Modules\Order\OrderController', 'checkout');

// Admin
$router->add('admin', 'App\Modules\Admin\AdminController', 'index');
$router->add('admin/products', 'App\Modules\Admin\AdminController', 'products');
$router->add('admin/products/add', 'App\Modules\Admin\AdminController', 'product_add');
$router->add('admin/products/edit', 'App\Modules\Admin\AdminController', 'product_edit');
$router->add('admin/products/delete', 'App\Modules\Admin\AdminController', 'product_delete');
$router->add('admin/orders', 'App\Modules\Admin\AdminController', 'orders');
$router->add('admin/orders/add', 'App\Modules\Admin\AdminController', 'order_add');
$router->add('admin/orders/edit', 'App\Modules\Admin\AdminController', 'order_edit');
$router->add('admin/orders/detail', 'App\Modules\Admin\AdminController', 'order_detail');
$router->add('admin/orders/update', 'App\Modules\Admin\AdminController', 'order_update');

// Contact
$router->add('contact', 'App\Modules\Contact\ContactController', 'index');
$router->add('contact/submit', 'App\Modules\Contact\ContactController', 'submit');

// Dispatch
$url = $_GET['route'] ?? 'home';
$router->dispatch($url);
