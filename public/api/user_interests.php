<?php

declare(strict_types=1);

use App\Domain\Repository\InterestRepository;
use App\Domain\Support\Validator;

require_once __DIR__ . '/../../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

$currentUser = Api\Auth::requireAuth();
$interestRepository = new InterestRepository();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $interests = $interestRepository->byUserId($currentUser->id);
    echo json_encode($interests);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['title']) || empty(trim($input['title']))) {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
        exit;
    }

    $title = trim($input['title']);

    if (!Validator::interestTitle($title)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title must be 1-255 characters']);
        exit;
    }

    $interestId = $interestRepository->create($currentUser->id, $title);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'interest_id' => $interestId,
        'interest' => [
            'id' => $interestId,
            'user_id' => $currentUser->id,
            'title' => $title,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}