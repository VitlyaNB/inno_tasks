<?php
global $pdo;
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /front/html/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$template = file_get_contents(__DIR__ . '/../front/html/PageForUsers.html');

$stmt = $pdo->prepare("SELECT id, interest FROM interests WHERE user_id = ? ORDER BY id ASC");
$stmt->execute([$user_id]);
$interests = $stmt->fetchAll();

$table = '<table><tr><th>#</th><th>Интерес</th><th>Действия</th></tr>';
$counter = 1;
foreach ($interests as $row) {
    $table .= '<tr>';
    $table .= '<td>' . $counter++ . '</td>';
    $table .= '<td>' . htmlspecialchars($row['interest']) . '</td>';
    $table .= '<td class="actions">
        <a href="javascript:void(0)" onclick="openEdit(' . $row['id'] . ', \'' . htmlspecialchars($row['interest'], ENT_QUOTES) . '\')">✏️</a>
        <a href="/php/interests_delete.php?id=' . $row['id'] . '" onclick="return confirm(\'Удалить интерес?\')">❌</a>
    </td>';
    $table .= '</tr>';
}
$table .= '</table>';

$template = str_replace('{{username}}', htmlspecialchars($user_name), $template);
$template = str_replace('{{interests_table}}', $table, $template);

echo $template;
