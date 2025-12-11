<?php
global $pdo;
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не разрешён');
}

$email = trim($_POST['email'] ?? '');
$code  = trim($_POST['code'] ?? '');

if ($email === '' || $code === '') {
    exit('Введите email и код');
}

// Получаем токен и срок действия из БД
$stmt = $pdo->prepare('SELECT reset_token, reset_expires FROM users WHERE email = ?');
$stmt->execute([$email]);
$row = $stmt->fetch();

if (!$row) {
    exit('Пользователь не найден');
}

// Проверка срока действия
if (strtotime($row['reset_expires']) < time()) {
    exit('Код истёк');
}

// Проверка совпадения кода
if ($row['reset_token'] !== $code) {
    exit('Неверный код');
}

// Если всё ок → сохраняем в сессии, что код подтверждён
$_SESSION['reset_email'] = $email;

header("Location: /front/html/password_reset.html");
exit;
