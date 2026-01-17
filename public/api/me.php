<?php

require_once __DIR__ . '/../app/Bootstrap.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');

$user = Api\Auth::requireAuth();

echo json_encode([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'role' => $user->role,
    'created_at' => $user->createdAt
]);