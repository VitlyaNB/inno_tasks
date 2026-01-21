<?php

declare(strict_types=1);

namespace App;

use App\Domain\Repository\UserRepository;

final class Bootstrap
{
    public static function run(): void
    {
        $users = new UserRepository();
        $admin = $users->findByEmail('admin@example.com');
        if (!$admin) {
            $hash = password_hash('123', PASSWORD_DEFAULT);
            $users->create('Administrator', 'admin@example.com', $hash, 'admin');
        }
    }
}
