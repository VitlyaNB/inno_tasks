<?php
declare(strict_types=1);

namespace App\Domain\DTO;

class InterestDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly int $userId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            title: trim($data['title'] ?? ''),
            userId: (int)($data['user_id'] ?? 0)
        );
    }

    public static function fromPost(string $action): ?self
    {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? $_POST['interest'] ?? '');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if ($action === 'add' && empty($title)) {
            return null;
        }

        if (in_array($action, ['edit', 'delete']) && $id <= 0) {
            return null;
        }

        return new self($id, $title, $userId);
    }

    public static function forAdmin(int $userId): ?self
    {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $action = $_POST['action'] ?? '';

        if ($action === 'add' && empty($title)) {
            return null;
        }

        if (in_array($action, ['edit', 'delete']) && $id <= 0) {
            return null;
        }

        return new self($id, $title, $userId);
    }

    public function validate(): bool
    {
        return !empty($this->title) && strlen($this->title) <= 255;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user_id' => $this->userId
        ];
    }
}