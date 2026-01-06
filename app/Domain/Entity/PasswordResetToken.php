<?php

namespace App\Domain\Entity;

class PasswordResetToken
{
    public function __construct(
        public ?int    $id,
        public int     $userId,
        public string  $token,
        public string  $expiresAt,
        public ?string $createdAt = null
    )
    {
    }
}
