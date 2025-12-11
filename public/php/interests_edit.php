<?php
global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $interest = trim($_POST['interest'] ?? '');
    if ($id && $interest !== '') {
        $stmt = $pdo->prepare("UPDATE interests SET interest = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$interest, $id, $_SESSION['user_id']]);
        http_response_code(200);
        exit;
    }
}
http_response_code(400);
