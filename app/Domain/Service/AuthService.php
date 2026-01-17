<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Repository\UserRepository;
use App\Domain\DTO\LoginDTO;
use App\Domain\DTO\RegisterDTO;
use App\Domain\DTO\UserDTO;

class AuthService
{
    public function __construct(
        private UserRepository $users
    ) {}

    public function register(RegisterDTO $dto): ?UserDTO
    {
        $email = strtolower(trim($dto->email));

        // Проверяем существование пользователя
        if ($this->users->findByEmail($email)) {
            return null;
        }

        $hash = password_hash($dto->password, PASSWORD_DEFAULT);

        $userId = $this->users->create($dto->name, $email, $hash, 'user');

        if (!$userId) {
            return null;
        }

        $user = $this->users->findById($userId);

        return $user ? UserDTO::fromEntity($user) : null;
    }

    public function login(LoginDTO $dto): ?UserDTO
    {
        $user = $this->users->findByEmail(strtolower(trim($dto->email)));

        if (!$user) {
            return null;
        }

        if (!password_verify($dto->password, $user->passwordHash)) {
            return null;
        }

        return UserDTO::fromEntity($user);
    }
}