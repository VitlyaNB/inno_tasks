<?php

declare(strict_types=1);

use App\Domain\Repository\InterestRepository;

require_once __DIR__ . '/../../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

$currentUser = Api\Auth::user();
$interestRepository = new InterestRepository();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;

    $total = $interestRepository->getTotalCount();
    $interests = $interestRepository->getAllWithPagination($limit, $offset);

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