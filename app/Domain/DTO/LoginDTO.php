<?php
declare(strict_types=1);

namespace App\Domain\DTO;

use App\Domain\Entity\User;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $user->role
        );
    }

    public static function fromSession(): ?self
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return new self(
            id: (int)$_SESSION['user_id'],
            name: $_SESSION['name'] ?? '',
            email: $_SESSION['user'] ?? '',
            role: $_SESSION['role'] ?? 'user'
        );
    }

    public function toSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $this->id;
        $_SESSION['name'] = $this->name;
        $_SESSION['user'] = $this->email;
        $_SESSION['role'] = $this->role;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role
        ];
    }
}