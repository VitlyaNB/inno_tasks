<?php

namespace Api;

use App\Domain\Repository\UserRepository;

class Auth
{
    public static function check(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?\App\Domain\Entity\User
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $userRepository = new UserRepository();
        return $userRepository->findById($_SESSION['user_id']);
    }

    public static function requireAuth(): \App\Domain\Entity\User
    {
        $user = self::user();

        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }

        return $user;
    }

    public static function requireAdmin(): \App\Domain\Entity\User
    {
        $user = self::requireAuth();

        if ($user->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            exit;
        }

        return $user;
    }

    public static function id(): ?int
    {
        session_start();
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string
    {
        session_start();
        return $_SESSION['user_role'] ?? null;
    }
}