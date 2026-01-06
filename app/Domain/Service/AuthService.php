<?php

namespace App\Domain\Service;

use App\Domain\Repository\UserRepository;

class AuthService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function register(string $name, string $email, string $password): ?int
    {
        $email = strtolower(trim($email));
        if ($this->users->findByEmail($email)) {
            return null;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->users->create($name, $email, $hash, 'user');
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->users->findByEmail(strtolower(trim($email)));
        if (!$user) return null;
        if (!password_verify($password, $user->passwordHash)) return null;
        return ['id' => $user->id, 'role' => $user->role, 'name' => $user->name, 'email' => $user->email];
    }
}
