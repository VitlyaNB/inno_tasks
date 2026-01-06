<?php

namespace App\Domain\Repository;

use App\Database\Connection;

class PasswordResetTokenRepository
{
    public function create(int $userId, string $token, string $expiresAt): void
    {
        $stmt = Connection::get()->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expiresAt]);
    }

    public function findValid(string $token): ?array
    {
        $stmt = Connection::get()->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function deleteByUserId(int $userId): void
    {
        $stmt = Connection::get()->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
}
