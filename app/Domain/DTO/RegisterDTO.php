<?php

declare(strict_types=1);

namespace App\Domain\DTO;

class RegisterDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $confirmPassword
    )
    {
    }

    public static function fromPost(): self
    {
        return new self(
            name: trim($_POST['name'] ?? ''),
            email: trim($_POST['email'] ?? ''),
            password: $_POST['password'] ?? '',
            confirmPassword: $_POST['confirm'] ?? ''
        );
    }
}