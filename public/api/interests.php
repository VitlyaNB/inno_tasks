<?php

require_once __DIR__ . '/../../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

$currentUser = Api\Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;

    $db = \App\Database\Connection::get();

    $stmt = $db->query("SELECT COUNT(*) as count FROM interests");
    $total = $stmt->fetch()['count'];

    $stmt = $db->prepare("
        SELECT i.*, u.name as user_name, u.email as user_email 
        FROM interests i 
        JOIN users u ON i.user_id = u.id 
        ORDER BY i.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    $interests = $stmt->fetchAll();

    echo json_encode([
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => ceil($total / $limit),
        'data' => $interests
    ]);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}