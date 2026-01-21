<?php

declare(strict_types=1);

namespace App\Domain\Support;

final class Helpers
{
    public static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireUser(): void
    {
        self::ensureSession();

        if (empty($_SESSION['user'])) {
            header('Location: /login.php');
            exit;
        }
    }

    public static function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function dump($var): void
    {
        echo '<pre>' . print_r($var, true) . '</pre>';
    }
}
