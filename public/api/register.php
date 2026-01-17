<?php

require_once __DIR__ . '/../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $required = ['name', 'email', 'password'];
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

    if (!\App\Support\Validator::name($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Name must be 1-100 characters']);
        exit;
    }

    if (!\App\Support\Validator::email($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    if (!\App\Support\Validator::password($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters (letters and digits only)']);
        exit;
    }

    $authService = new \App\Domain\Service\AuthService(new \App\Domain\Repository\UserRepository());
    $userId = $authService->register($name, $email, $password);

    if (!$userId) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    $userData = $authService->login($email, $password);

    session_start();
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['user_role'] = $userData['role'];
    $_SESSION['user_name'] = $userData['name'];
    $_SESSION['user_email'] = $userData['email'];

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'user_id' => $userId,
        'user' => [
            'id' => $userData['id'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}