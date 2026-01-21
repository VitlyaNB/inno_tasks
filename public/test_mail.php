<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = getenv('MAIL_HOST');
    $mail->Port       = getenv('MAIL_PORT');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('MAIL_USER');
    $mail->Password   = getenv('MAIL_PASS');
    $mail->SMTPSecure = getenv('MAIL_SECURE');
    $mail->setFrom(getenv('MAIL_FROM'), 'Test');
    $mail->addAddress(getenv('MAIL_USER')); // отправляем самому себе

    $mail->isHTML(true);
    $mail->Subject = 'Тестовое письмо';
    $mail->Body    = '<p>Это тестовое письмо из Docker‑приложения.</p>';

    if ($mail->send()) {
        echo "Письмо успешно отправлено!";
    } else {
        echo "Ошибка при отправке.";
    }
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
