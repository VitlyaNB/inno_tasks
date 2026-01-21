<?php

declare(strict_types=1);

use App\Domain\Service\AuthService;
use App\Domain\Repository\UserRepository;
use App\Domain\DTO\LoginDTO;
use App\Domain\Support\Validator;

require_once __DIR__ . '/../../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        exit;
    }

    $email = trim($input['email']);
    $password = $input['password'];

    if (!Validator::email($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    $loginDto = new LoginDTO(email: $email, password: $password);
    $authService = new AuthService(new UserRepository());
    $userDto = $authService->login($loginDto);

    if (!$userDto) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    session_start();
    $_SESSION['user_id'] = $userDto->id;
    $_SESSION['user_role'] = $userDto->role;
    $_SESSION['user_name'] = $userDto->name;
    $_SESSION['user_email'] = $userDto->email;

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userDto->id,
            'name' => $userDto->name,
            'email' => $userDto->email,
            'role' => $userDto->role
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}