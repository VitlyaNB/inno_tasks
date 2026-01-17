<?php

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

    $required = ['token', 'password'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            exit;
        }
    }

    $token = trim($input['token']);
    $password = $input['password'];

    if (!\App\Support\Validator::password($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters (letters and digits only)']);
        exit;
    }

    $userRepository = new \App\Domain\Repository\UserRepository();
    $passwordResetService = new \App\Domain\Service\PasswordResetService($userRepository);

    $result = $passwordResetService->reset($token, $password);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or expired token']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}