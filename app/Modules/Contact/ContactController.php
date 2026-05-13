<?php

namespace App\Modules\Contact;

use App\Core\Controller;

class ContactController extends Controller
{
    public function index()
    {
        $this->view('Contact', 'index', [
            'page_title' => 'Liên hệ với chúng tôi'
        ]);
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý gửi form liên hệ (Gửi mail hoặc lưu DB)
            // Hiện tại chỉ giả lập thành công
            flash('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.');
            $this->redirect('index.php?route=contact');
        }
    }
}
