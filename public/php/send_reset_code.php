<?php
global $pdo;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '../../vendor/autoload.php';

require 'db.php';

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    exit('Email не передан');
}

$code = random_int(100000, 999999);
$expires = date('Y-m-d H:i:s', time() + 600); // 10 минут

$stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?');
$stmt->execute([$code, $expires, $email]);

// Отправка письма
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yourgmail@gmail.com';
    $mail->Password   = 'your_app_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('yourgmail@gmail.com', 'Your App');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Код восстановления пароля';
    $mail->Body    = "Ваш код: <b>{$code}</b><br>Код действителен 10 минут.";
    $mail->AltBody = "Ваш код: {$code}\nКод действителен 10 минут.";

    $mail->send();
    echo 'Код отправлен на почту';
} catch (Exception $e) {
    error_log("Ошибка отправки письма: " . $mail->ErrorInfo);
    exit('Ошибка отправки письма. Попробуйте позже.');
}
