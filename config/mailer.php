<?php
/**
 * Mail Helper - Gửi email qua PHPMailer hoặc fallback mail()
 */

function sendMail($to, $toName, $subject, $bodyHtml, $bodyText = null) {
    $autoload = __DIR__ . '/../vendor/autoload.php';
    
    if (defined('MAIL_USE_SMTP') && MAIL_USE_SMTP && file_exists($autoload)) {
        require_once $autoload;
        
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return sendViaPHPMailer($to, $toName, $subject, $bodyHtml, $bodyText);
        }
    }
    
    // Fallback: mail()
    return sendViaMail($to, $subject, $bodyText ?: strip_tags($bodyHtml));
}

function sendViaPHPMailer($to, $toName, $subject, $bodyHtml, $bodyText = null) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->Port       = MAIL_PORT;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = MAIL_PORT == 587
            ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS
            : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $bodyHtml;
        if ($bodyText) {
            $mail->AltBody = $bodyText;
        }
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail error: " . $e->getMessage());
        return false;
    }
}

function sendViaMail($to, $subject, $body) {
    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Template email xác nhận
 */
function emailVerifyTemplate($username, $verifyLink) {
    $bodyHtml = "
    <h2>Chào {$username}!</h2>
    <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>Office Supplies</strong>.</p>
    <p>Để kích hoạt tài khoản, vui lòng nhấn vào liên kết bên dưới:</p>
    <p><a href='{$verifyLink}' style='background:#0d6efd;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;display:inline-block;'>Kích hoạt tài khoản</a></p>
    <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email.</p>
    <hr>
    <p><small>Office Supplies - 741 Giải Phóng, Hoàng Mai, Hà Nội</small></p>
    ";
    
    $bodyText = "Chào {$username}!\n\nCảm ơn bạn đã đăng ký tài khoản tại Office Supplies.\n\nĐể kích hoạt tài khoản, vui lòng truy cập:\n{$verifyLink}\n\nNếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email.";
    
    return ['html' => $bodyHtml, 'text' => $bodyText];
}

/**
 * Template email đặt lại mật khẩu
 */
function emailResetTemplate($username, $resetLink) {
    $bodyHtml = "
    <h2>Chào {$username}!</h2>
    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
    <p>Nhấn vào liên kết bên dưới để đặt lại mật khẩu (hết hạn sau 1 giờ):</p>
    <p><a href='{$resetLink}' style='background:#dc3545;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;display:inline-block;'>Đặt lại mật khẩu</a></p>
    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
    <hr>
    <p><small>Office Supplies - 741 Giải Phóng, Hoàng Mai, Hà Nội</small></p>
    ";
    
    $bodyText = "Chào {$username}!\n\nChúng tôi nhận được yêu cầu đặt lại mật khẩu.\n\nĐể đặt lại mật khẩu, truy cập:\n{$resetLink}\n\nLiên kết hết hạn sau 1 giờ.\n\nNếu bạn không yêu cầu, vui lòng bỏ qua email này.";
    
    return ['html' => $bodyHtml, 'text' => $bodyText];
}
