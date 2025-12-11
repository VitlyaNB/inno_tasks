<?php

global $pdo;
session_start();
require 'db.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header("Location: /front/html/login.html");
    exit;
}

// Проверяем, что форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $interest = trim($_POST['interest'] ?? '');

    if ($interest !== '') {
        $stmt = $pdo->prepare("INSERT INTO interests (user_id, interest) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $interest]);
    }
}

// После добавления возвращаемся в личный кабинет
header("Location: PageForUsers.php");
exit;
