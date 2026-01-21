<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Database\Connection;

final class InterestRepository
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

    public function getAllWithPagination(int $limit, int $offset): array
    {
        $stmt = Connection::get()->prepare("
            SELECT i.*, u.name as user_name, u.email as user_email
            FROM interests i
            JOIN users u ON i.user_id = u.id
            ORDER BY i.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getTotalCount(): int
    {
        $stmt = Connection::get()->query("SELECT COUNT(*) as count FROM interests");
        return (int)$stmt->fetch()['count'];
    }
}
