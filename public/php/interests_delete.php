<?php

global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /front/html/login.html");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM interests WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header("Location: PageForUsers.php");
exit;
