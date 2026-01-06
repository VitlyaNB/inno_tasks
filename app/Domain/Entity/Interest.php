<?php

namespace App\Domain\Entity;

class Interest
{
    public function __construct(
        public ?int    $id,
        public int     $userId,
        public string  $title,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    )
    {
    }
}
