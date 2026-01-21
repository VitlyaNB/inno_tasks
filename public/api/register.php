<?php

declare(strict_types=1);

use App\Domain\Service\AuthService;
use App\Domain\Repository\UserRepository;
use App\Domain\DTO\RegisterDTO;
use App\Domain\Support\Validator;

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

    if (!Validator::name($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Name must be 1-100 characters']);
        exit;
    }

    if (!Validator::email($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    if (!Validator::password($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters (letters and digits only)']);
        exit;
    }

    $registerDto = new RegisterDTO(
        name: $name,
        email: $email,
        password: $password,
        confirmPassword: $password // API doesn't have confirm password, so use password
    );

    $authService = new AuthService(new UserRepository());
    $userDto = $authService->register($registerDto);

    if (!$userDto) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    session_start();
    $_SESSION['user_id'] = $userDto->id;
    $_SESSION['user_role'] = $userDto->role;
    $_SESSION['user_name'] = $userDto->name;
    $_SESSION['user_email'] = $userDto->email;

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'user_id' => $userDto->id,
        'user' => [
            'id' => $userDto->id,
            'name' => $userDto->name,
            'email' => $userDto->email,
            'role' => $userDto->role
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}