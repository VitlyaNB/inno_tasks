<?php

session_start();

// Удаляем все данные сессии
$_SESSION = [];

// Уничтожаем саму сессию
session_destroy();

// Перенаправляем на страницу входа
header("Location: /front/html/login.html");
exit;
