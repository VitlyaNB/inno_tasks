<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Database\Connection;
use App\Domain\Entity\User;
use PDO;

final class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        $stmt = Connection::get()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? new User($row['id'], $row['name'], $row['email'], $row['password_hash'], $row['role'], $row['created_at']) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = Connection::get()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new User($row['id'], $row['name'], $row['email'], $row['password_hash'], $row['role'], $row['created_at']) : null;
    }

    public function create(string $name, string $email, string $passwordHash, string $role = 'user'): int
    {
        $stmt = Connection::get()->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $passwordHash, $role]);
        return (int)Connection::get()->lastInsertId();
    }

    public function all(): array
    {
        $stmt = Connection::get()->query("SELECT id, name, email, role, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function delete(int $id): void
    {
        $stmt = Connection::get()->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}
