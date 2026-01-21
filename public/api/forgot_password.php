<?php

declare(strict_types=1);

use App\Domain\Repository\UserRepository;
use App\Domain\Repository\PasswordResetTokenRepository;
use App\Domain\Service\MailerService;
use App\Domain\Service\PasswordResetService;
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

    if (!isset($input['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        exit;
    }

    $email = trim($input['email']);

    if (!Validator::email($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    $userRepository = new UserRepository();
    $passwordResetService = new PasswordResetService(
        $userRepository,
        new PasswordResetTokenRepository(),
        new MailerService()
    );

    $result = $passwordResetService->request($email);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Reset instructions sent to email']);
    } else {
        echo json_encode(['success' => true, 'message' => 'If email exists, reset instructions will be sent']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}