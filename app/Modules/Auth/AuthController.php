<?php

namespace App\Modules\Auth;

use App\Core\Controller;

class AuthController extends Controller
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function login()
    {
        if (isLoggedIn()) $this->redirect('home');

        $errors = [];
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();

            if (isRateLimited('login', 5, 300)) {
                $errors[] = "Bạn đã thử đăng nhập quá nhiều lần. Vui lòng chờ 5 phút trước khi thử lại.";
            } else {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($username)) $errors[] = "Tên đăng nhập không được để trống";
                if (empty($password)) $errors[] = "Mật khẩu không được để trống";

                if (empty($errors)) {
                    $user = $this->authModel->getUserByUsernameOrEmail($username);

                    if ($user && password_verify($password, $user['password'])) {
                        if (empty($user['email_verified'])) {
                            $errors[] = "Email của bạn chưa được xác thực. Vui lòng kiểm tra hộp thư.";
                        } else {
                            // Login success
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['email_verified'] = $user['email_verified'];

                            $this->authModel->updateLastLogin($user['id']);
                            unset($_SESSION['rate_limit_login_' . $_SERVER['REMOTE_ADDR']]);

                            if ($user['role'] === 'admin') {
                                header("Location: admin");
                                exit;
                            } else {
                                $redirect = $_SESSION['redirect_after_login'] ?? 'home';
                                unset($_SESSION['redirect_after_login']);
                                header("Location: " . $redirect);
                                exit;
                            }
                        }
                    } else {
                        $errors[] = "Tên đăng nhập hoặc mật khẩu không chính xác";
                    }
                }
            }
        }

        $this->view('Auth', 'login', [
            'page_title' => 'Đăng nhập',
            'errors' => $errors,
            'username' => $username
        ]);
    }

    public function register()
    {
        if (isLoggedIn()) $this->redirect('home');

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();

            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($username)) $errors[] = "Tên đăng nhập không được để trống";
            if (!isValidEmail($email)) $errors[] = "Email không hợp lệ";
            if (!isValidPassword($password)) $errors[] = "Mật khẩu phải từ 6 ký tự, có cả chữ và số";
            if ($password !== $confirm_password) $errors[] = "Mật khẩu xác nhận không khớp";

            if (empty($errors)) {
                if ($this->authModel->getUserByUsernameOrEmail($username)) {
                    $errors[] = "Tên đăng nhập hoặc email đã tồn tại";
                } else {
                    $verifyToken = generateToken();
                    if ($this->authModel->register($username, $email, $password, hashToken($verifyToken))) {
                        // sendEmail($email, $username, $verifyToken); // Helper needed
                        $success = "Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.";
                    } else {
                        $errors[] = "Đã có lỗi xảy ra, vui lòng thử lại sau.";
                    }
                }
            }
        }

        $this->view('Auth', 'register', [
            'page_title' => 'Đăng ký',
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function logout()
    {
        session_destroy();
        header("Location: home");
        exit;
    }

    public function verify_email()
    {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        if (empty($email) || empty($token)) {
            $this->redirect('home');
        }

        $user = $this->authModel->getUserByVerifyToken($email, hashToken($token));

        if ($user) {
            $this->authModel->verifyEmail($user['id']);
            $message = "Xác thực email thành công! Bây giờ bạn có thể đăng nhập.";
            $type = "success";
        } else {
            $message = "Link xác thực không hợp lệ hoặc đã hết hạn.";
            $type = "danger";
        }

        $this->view('Auth', 'verify_status', [
            'page_title' => 'Xác thực Email',
            'message' => $message,
            'type' => $type
        ]);
    }

    public function forgot_password()
    {
        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();
            $email = trim($_POST['email'] ?? '');

            if (!isValidEmail($email)) {
                $errors[] = "Email không hợp lệ";
            } else {
                $user = $this->authModel->getUserByUsernameOrEmail($email);
                if ($user) {
                    $token = generateToken();
                    $this->authModel->setResetToken($email, hashToken($token));
                    // sendResetEmail($email, $user['username'], $token); // Helper needed
                }
                $success = "Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.";
            }
        }

        $this->view('Auth', 'forgot_password', [
            'page_title' => 'Quên mật khẩu',
            'errors' => $errors,
            'success' => $success
        ]);
    }

    public function reset_password()
    {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        if (empty($email) || empty($token)) {
            $this->redirect('home');
        }

        $user = $this->authModel->getUserByResetToken($email, hashToken($token));
        if (!$user) {
            die("Link không hợp lệ hoặc đã hết hạn.");
        }

        $errors = [];
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (!isValidPassword($password)) $errors[] = "Mật khẩu mới không hợp lệ";
            if ($password !== $confirm) $errors[] = "Mật khẩu xác nhận không khớp";

            if (empty($errors)) {
                $this->authModel->resetPassword($user['id'], $password);
                $success = "Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay.";
            }
        }

        $this->view('Auth', 'reset_password', [
            'page_title' => 'Đặt lại mật khẩu',
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
