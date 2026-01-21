<?php
declare(strict_types=1);

namespace App\Domain\Entity;

final class User
{
    public function __construct(
        public ?int    $id,
        public string  $name,
        public string  $email,
        public string  $passwordHash,
        public string  $role,
        public ?string $createdAt = null
    ) {}

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function createdAt(): ?string
    {
        return $this->createdAt;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }

    public function changePassword(string $password): void
    {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function updatePasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function promoteToAdmin(): void
    {
        $this->role = 'admin';
    }

    public function demoteToUser(): void
    {
        $this->role = 'user';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \InvalidArgumentException("Property $name does not exist");
    }
}