<?php

global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /front/html/login.html");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "Неверный ID";
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Пользователь не найден!";
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя #<?= htmlspecialchars($user['id']) ?></title>
    <link rel="stylesheet" href="/front/css/AdminStyle.css">
</head>
<body>
<div class="admin-container">
    <h1>Редактирование пользователя</h1>
    <form class="admin-form" action="update_user.php" method="post" autocomplete="off" novalidate>
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

        <label>Имя</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Роль</label>
        <select name="role">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <div class="actions">
            <button type="submit" class="btn-edit">Сохранить</button>
            <a href="PageForAdmin.php" class="btn-delete"
               style="text-decoration:none; display:inline-block; text-align:center;">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>
