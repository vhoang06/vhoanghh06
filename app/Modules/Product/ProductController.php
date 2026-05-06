<?php

namespace App\Modules\Product;

use App\Core\Controller;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

        $products = $this->productModel->getProducts($search, $category_id, $brand_id);
        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();

        $this->view('Product', 'list', [
            'page_title' => 'Sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'search' => $search,
            'category_id' => $category_id,
            'brand_id' => $brand_id
        ]);
    }

    public function detail()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: " . APP_URL . "/index.php?route=products");
            exit;
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->view('Product', 'error', ['message' => 'Sản phẩm không tồn tại']);
            return;
        }

        $related_products = $this->productModel->getRelatedProducts($product['category_id'], $id);

        $this->view('Product', 'detail', [
            'page_title' => $product['name'],
            'product' => $product,
            'related_products' => $related_products
        ]);
    }

    public function brands()
    {
        $brands = $this->productModel->getBrands();
        $this->view('Product', 'brands', [
            'page_title' => 'Thương hiệu',
            'brands' => $brands
        ]);
    }
}
