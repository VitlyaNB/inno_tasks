<?php
global $pdo;
session_start();
require 'db.php';

header('Content-Type: application/json'); // всегда JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email !== '' && $password !== '') {
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            echo json_encode(['success' => true, 'role' => $user['role']]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Неправильный логин или пароль']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
        exit;
    }
}
