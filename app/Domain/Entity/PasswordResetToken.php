<?php
declare(strict_types=1);

namespace App\Domain\Entity;

final class PasswordResetToken
{
    public function __construct(
        public ?int    $id,
        public int     $userId,
        public string  $token,
        public string  $expiresAt,
        public ?string $createdAt = null
    ) {}

    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function expiresAt(): string
    {
        return $this->expiresAt;
    }

    public function createdAt(): ?string
    {
        return $this->createdAt;
    }

    public function isValid(): bool
    {
        return strtotime($this->expiresAt) > time();
    }

    public function isExpired(): bool
    {
        return !$this->isValid();
    }

    public function renew(int $hours = 1): void
    {
        $this->expiresAt = date('Y-m-d H:i:s', strtotime("+{$hours} hours"));
    }

    public function regenerateToken(): void
    {
        $this->token = bin2hex(random_bytes(32));
    }
}