<?php

namespace App\Domain\Repository;

use App\Database\Connection;

class InterestRepository
{
    public function byUserId(int $userId): array
    {
        $stmt = Connection::get()->prepare("SELECT * FROM interests WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $title): int
    {
        $stmt = Connection::get()->prepare("INSERT INTO interests (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, $title]);
        return (int)Connection::get()->lastInsertId();
    }

    public function update(int $id, int $userId, string $title): void
    {
        $stmt = Connection::get()->prepare("UPDATE interests SET title = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $id, $userId]);
    }

    public function delete(int $id, int $userId): void
    {
        $stmt = Connection::get()->prepare("DELETE FROM interests WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    public function deleteByIdAdmin(int $id): void
    {
        $stmt = Connection::get()->prepare("DELETE FROM interests WHERE id = ?");
        $stmt->execute([$id]);
    }
}
