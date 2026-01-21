<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum Roles: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}