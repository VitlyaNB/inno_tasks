<?php
global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: /front/html/password_request.html");
    exit;
}

$email = $_SESSION['reset_email'];
$password = trim($_POST['password'] ?? '');

if ($password === '') {
    exit('Введите пароль');
}

$hashed = password_hash($password, PASSWORD_DEFAULT);


$stmt = $pdo->prepare("UPDATE users 
                       SET password = ?, reset_token = NULL, reset_expires = NULL 
                       WHERE email = ?");
$stmt->execute([$hashed, $email]);

unset($_SESSION['reset_email']);

echo "Пароль успешно обновлён. Теперь можно <a href='/front/html/login.html'>войти</a>.";
