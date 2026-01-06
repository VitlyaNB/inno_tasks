<?php

namespace App\Config;

class Config
{
    public static function env(string $key, ?string $default = null): string
    {
        $val = getenv($key);
        return $val !== false ? $val : ($default ?? '');
    }

    public static function db(): array
    {
        return [
            'host' => self::env('DB_HOST', 'db'),
            'port' => self::env('DB_PORT', '3306'),
            'name' => self::env('DB_NAME', 'app_db'),
            'user' => self::env('DB_USER', 'app_user'),
            'pass' => self::env('DB_PASS', 'app_pass'),
            'charset' => 'utf8mb4',
        ];
    }

    public static function appUrl(): string
    {
        return rtrim(self::env('APP_URL', 'http://localhost'), '/');
    }

    public static function smtp(): array
    {
        return [
            'host' => self::env('SMTP_HOST', ''),
            'port' => (int)self::env('SMTP_PORT', '465'),
            'user' => self::env('SMTP_USER', ''),
            'pass' => self::env('SMTP_PASS', ''),
            'secure' => self::env('SMTP_SECURE', 'ssl'), // ssl or tls
            'fromEmail' => self::env('SMTP_USER', ''),
            'fromName' => 'Support',
        ];
    }
}
