<?php

declare(strict_types=1);

namespace App\Domain\DTO;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}

    public static function fromPost(): self
    {
        return new self(
            email: trim($_POST['email'] ?? ''),
            password: $_POST['password'] ?? ''
        );
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->email)) {
            $errors[] = 'Email обязателен';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Неверный формат email';
        }

        if (empty($this->password)) {
            $errors[] = 'Пароль обязателен';
        }

        return $errors;
    }
}