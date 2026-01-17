<?php
declare(strict_types=1);

namespace App\Domain\Entity;

class Interest
{
    public function __construct(
        public ?int    $id,
        public int     $userId,
        public string  $title,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    // Геттеры
    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function createdAt(): ?string
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function changeTitle(string $title): void
    {
        $this->title = $title;
    }

    public function updateTimestamps(): void
    {
        $now = date('Y-m-d H:i:s');

        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }

        $this->updatedAt = $now;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }

    public function belongsToUser(int $userId): bool
    {
        return $this->userId === $userId;
    }

    public function hasTitle(): bool
    {
        return !empty(trim($this->title));
    }
}