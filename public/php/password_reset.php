<?php

session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: /front/html/password_request.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сброс пароля</title>
</head>
<body>
<h2>Введите новый пароль</h2>
<form method="post" action="update_password.php">
    <input type="password" name="password" placeholder="Новый пароль" required>
    <button type="submit">Сменить пароль</button>
</form>
</body>
</html>
