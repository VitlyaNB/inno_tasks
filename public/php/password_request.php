<?php

global $pdo;
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не разрешён');
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    exit('Введите email');
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    exit('Пользователь не найден');
}

include 'send_reset_code.php';
