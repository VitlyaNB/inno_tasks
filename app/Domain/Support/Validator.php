<?php

namespace App\Support;

class Validator
{
    public static function name(string $name): bool
    {
        $name = trim($name);
        return $name !== '' && mb_strlen($name) <= 100;
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && mb_strlen($email) <= 190;
    }

    // Min 6 chars, only letters and digits (no special symbols)
    public static function password(string $password): bool
    {
        return (bool)preg_match('/^[A-Za-z0-9]{6,}$/', $password);
    }

    public static function interestTitle(string $title): bool
    {
        $title = trim($title);
        return $title !== '' && mb_strlen($title) <= 255;
    }
}
