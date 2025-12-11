<?php
global $pdo;
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name !== '' && $email !== '' && $password !== '') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $role = ($email === 'vitalya.khomich15@gmail.com') ? 'admin' : 'user';

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed, $role]);

        $user_id = $pdo->lastInsertId();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;

        if ($role === 'admin') {
            header("Location: PageForAdmin.php");
        } else {
            header("Location: PageForUsers.php");
        }
        exit;
    } else {
        echo "Ошибка: заполните все поля!";
    }
}
