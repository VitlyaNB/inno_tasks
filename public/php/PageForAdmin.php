<?php
global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /front/html/login.html");
    exit;
}

$totalStmt  = $pdo->query("SELECT COUNT(*) AS c FROM users");
$totalUsers = (int)$totalStmt->fetch()['c'];

$adminsStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'");
$adminsStmt->execute();
$adminsCount = (int)$adminsStmt->fetch()['c'];

$usersStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM users WHERE role = 'user'");
$usersStmt->execute();
$usersCount = (int)$usersStmt->fetch()['c'];

$stmt = $pdo->query("SELECT id, name, email, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();

$users_table = '';
foreach ($users as $user) {
    $id    = (int)$user['id'];
    $name  = htmlspecialchars($user['name']);
    $email = htmlspecialchars($user['email']);
    $role  = htmlspecialchars($user['role']);

    $users_table .= "<tr>
        <td>{$id}</td>
        <td>{$name}</td>
        <td>{$email}</td>
        <td>{$role}</td>
        <td>
            <form action='/php/edit_user.php' method='get' style='display:inline'>
                <input type='hidden' name='id' value='{$id}'>
                <button type='submit' class='btn-edit'>Редактировать</button>
            </form>
            <form action='/php/delete_user.php' method='post' style='display:inline' onsubmit=\"return confirm('Удалить пользователя #{$id}?');\">
                <input type='hidden' name='id' value='{$id}'>
                <button type='submit' class='btn-delete'>Удалить</button>
            </form>
        </td>
    </tr>";
}

$html = file_get_contents(__DIR__ . '/../front/html/PageForAdmin.html');
$html = str_replace('{{admin_name}}', htmlspecialchars($_SESSION['user_name'] ?? 'Админ'), $html);
$html = str_replace('{{users_table}}', $users_table, $html);
$html = str_replace('{{total_users}}', (string)$totalUsers, $html);
$html = str_replace('{{admins_count}}', (string)$adminsCount, $html);
$html = str_replace('{{users_count}}', (string)$usersCount, $html);

echo $html;
