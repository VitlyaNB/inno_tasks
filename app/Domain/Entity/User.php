<?php

namespace App\Domain\Entity;

class User
{
    public function __construct(
        public ?int    $id,
        public string  $name,
        public string  $email,
        public string  $passwordHash,
        public string  $role,
        public ?string $createdAt = null
    )
    {
    }
}
