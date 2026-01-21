<?php

declare(strict_types=1);

namespace App\Domain\DTO;

final readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $confirmPassword
    ) {}

    public static function fromPost(): self
    {
        return new self(
            name: trim($_POST['name'] ?? ''),
            email: trim($_POST['email'] ?? ''),
            password: $_POST['password'] ?? '',
            confirmPassword: $_POST['confirm'] ?? ''
        );
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Имя обязательно';
        } elseif (mb_strlen($this->name) > 100) {
            $errors[] = 'Имя не должно превышать 100 символов';
        }

        if (empty($this->email)) {
            $errors[] = 'Email обязателен';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Неверный формат email';
        } elseif (mb_strlen($this->email) > 190) {
            $errors[] = 'Email не должен превышать 190 символов';
        }

        if (empty($this->password)) {
            $errors[] = 'Пароль обязателен';
        } elseif (!preg_match('/^[A-Za-z0-9]{6,}$/', $this->password)) {
            $errors[] = 'Пароль должен содержать минимум 6 символов (только буквы и цифры)';
        }

        if ($this->password !== $this->confirmPassword) {
            $errors[] = 'Пароли не совпадают';
        }

        return $errors;
    }
}