<?php

namespace App\Modules\Home;

use App\Core\Controller;

class HomeController extends Controller
{
    private $homeModel;

    public function __construct()
    {
        $this->homeModel = new HomeModel();
    }

    public function index()
    {
        $categories = $this->homeModel->getCategories();
        
        $data = [
            'page_title' => 'Trang chủ',
            'categories' => $categories,
            'homeModel' => $this->homeModel
        ];

        $this->view('Home', 'index', $data);
    }
}
