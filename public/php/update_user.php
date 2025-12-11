<?php

global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /front/html/login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');

    if ($id <= 0 || $name === '' || $email === '') {
        header("Location: PageForAdmin.php");
        exit;
    }

    $adminsCountStmt = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'");
    $adminsCount = (int)$adminsCountStmt->fetch()['c'];

    $currentStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $currentStmt->execute([$id]);
    $currentRoleRow = $currentStmt->fetch();
    $currentRole = $currentRoleRow ? $currentRoleRow['role'] : null;

    if ($currentRole === 'admin' && $adminsCount === 1 && $role !== 'admin') {
        // Единственного админа нельзя понизить
        header("Location: PageForAdmin.php");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    try {
        $stmt->execute([$name, $email, $role, $id]);
    } catch (PDOException $e) {
        header("Location: PageForAdmin.php");
        exit;
    }
}

header("Location: PageForAdmin.php");
exit;
