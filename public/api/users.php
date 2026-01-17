<?php

require_once __DIR__ . '/../../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

$currentUser = Api\Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($currentUser->role !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }

    $userRepository = new \App\Domain\Repository\UserRepository();
    $users = $userRepository->all();

    echo json_encode($users);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($currentUser->role !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $required = ['name', 'email', 'password', 'role'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            exit;
        }
    }

    $name = trim($input['name']);
    $email = trim($input['email']);
    $password = $input['password'];
    $role = $input['role'];

    if (!in_array($role, ['admin', 'user'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Role must be "admin" or "user"']);
        exit;
    }

    $authService = new \App\Domain\Service\AuthService(new \App\Domain\Repository\UserRepository());
    $userId = $authService->register($name, $email, $password);

    if (!$userId) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    if ($role !== 'user') {
        $db = \App\Database\Connection::get();
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $userId]);
    }

    http_response_code(201);
    echo json_encode(['success' => true, 'user_id' => $userId]);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}