<?php
// contact.php
$page_title = "Liên hệ";
require_once 'includes/header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name))     $errors[] = "Vui lòng nhập họ tên";
    if (empty($email))    $errors[] = "Vui lòng nhập email";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";
    if (empty($message))  $errors[] = "Vui lòng nhập nội dung";

    if (empty($errors)) {
            // Lưu tin nhắn vào DB (tạo bảng nếu chưa có)
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(150) NOT NULL,
                    email VARCHAR(150) NOT NULL,
                    message TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $message]);
            } catch (Exception $e) {
                // Nếu không lưu được DB thì vẫn tiếp tục cố gắng gửi email
            }

            $adminEmail = 'viethoangk651@gmail.com';
            $subject = "[lien he] tin nhan tu " . $name;
            $body = "Bạn có tin nhắn mới từ trang liên hệ:\n\n";
            $body .= "Tên: " . $name . "\n";
            $body .= "Email: " . $email . "\n\n";
            $body .= "Nội dung:\n" . $message . "\n";

            $headers = "From: " . $name . " <" . $email . ">\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

            // Thử dùng PHPMailer nếu có, hoặc fallback về mail()
            $mail_sent = false;
            $mail_error = '';

            // Nếu có autoload (composer) và cấu hình SMTP cho phép, dùng PHPMailer
            $autoload = __DIR__ . '/vendor/autoload.php';
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require_once __DIR__ . '/vendor/autoload.php';
            }

            if (defined('MAIL_USE_SMTP') && MAIL_USE_SMTP && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                try {
                    $m = new PHPMailer\PHPMailer\PHPMailer(true);
                    if (MAIL_USE_SMTP) {
                        $m->isSMTP();
                        $m->Host = MAIL_HOST;
                        $m->Port = MAIL_PORT;
                        $m->SMTPAuth = true;
                        $m->Username = MAIL_USER;
                        $m->Password = MAIL_PASS;
                        $m->SMTPSecure = MAIL_PORT == 587 ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                    }
                    $m->setFrom(MAIL_FROM, MAIL_FROM_NAME);
                    $m->addAddress($adminEmail);
                    $m->addReplyTo($email, $name);
                    $m->Subject = $subject;
                    $m->Body = $body;
                    $m->isHTML(false);
                    $mail_sent = $m->send();
                } catch (Exception $e) {
                    $mail_sent = false;
                    $mail_error = $e->getMessage();
                }
            } else {
                // fallback to mail()
                try {
                    $mail_sent = mail($adminEmail, $subject, $body, $headers);
                } catch (Throwable $e) {
                    $mail_sent = false;
                    $mail_error = $e->getMessage();
                }
            }

            if ($mail_sent) {
                $success = true;
            } else {
                $success = true; // message still saved
                $errors[] = "Tin nhắn đã được lưu nhưng mail chưa gửi được. ";
                if ($mail_error) $errors[] = "Lỗi gửi mail: " . htmlspecialchars($mail_error);
                $errors[] = "Vui lòng cấu hình SMTP (xem hướng dẫn).";
            }
    }
}
?>

<h1 class="mb-4">Liên hệ với chúng tôi</h1>

<div class="row">
    <div class="col-md-8">
        <?php if ($success): ?>
            <div class="alert alert-success">
                Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn và sẽ phản hồi sớm nhất.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung liên hệ</label>
                <textarea name="message" class="form-control" rows="6" required><?= htmlspecialchars($message ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Gửi tin nhắn</button>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card mt-4 mt-md-0">
            <div class="card-body">
                <h5>Thông tin liên hệ</h5>
                <p class="mb-2"><strong>Địa chỉ:</strong><br>741 Giải Phóng, Hoàng Mai, Hà Nội</p>
                <p class="mb-2"><strong>Điện thoại:</strong><br>036 995 1001</p>
                <p class="mb-2"><strong>Email:</strong><br>viethoangk651@gmail.com</p>
                <p class="mb-1"><strong>Giờ mở cửa:</strong></p>
                <p class="small mb-1">Thứ Hai - Thứ Sáu: 08:00 - 18:00</p>
                <p class="small mb-0">Thứ Bảy: 08:00 - 12:00</p>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body p-0">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.0242401939467!2d105.83151271524904!3d20.982259294168793!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9f5a1853a9%3A0x3284de7a1fbeaf7d!2zNzQxIEfDtWkgUGjhu5MsIEjDoCBOaMOgLCBIw6AgTuG6u20sIEjhu5MgTm_DoG0sIEjhu5MgTmFuaA!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>