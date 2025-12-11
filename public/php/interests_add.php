<?php

global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /front/html/login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $interest = trim($_POST['interest'] ?? '');

    if ($interest !== '') {
        $stmt = $pdo->prepare("INSERT INTO interests (user_id, interest) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $interest]);
    }
}

header("Location: PageForUsers.php");
exit;
