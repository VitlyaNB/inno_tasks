<?php

namespace App\Domain\Service;

use App\Config\Config;
use App\Domain\Repository\PasswordResetTokenRepository;
use App\Domain\Repository\UserRepository;

class PasswordResetService
{
    public function __construct(
        private UserRepository               $users,
        private PasswordResetTokenRepository $tokens,
        private MailerService                $mailer
    )
    {
    }

    public function request(string $email): bool
    {
        $user = $this->users->findByEmail(strtolower(trim($email)));
        if (!$user) return true; // do not reveal existence

        $token = bin2hex(random_bytes(32));
        $expires = (new \DateTime('+1 hour'))->format('Y-m-d H:i:s');
        $this->tokens->deleteByUserId($user->id);
        $this->tokens->create($user->id, $token, $expires);

        $resetLink = Config::appUrl() . "/reset_password.php?token={$token}";
        $html = "<p>Для восстановления пароля перейдите по ссылке:</p>
                 <p><a href=\"{$resetLink}\">Сбросить пароль</a></p>
                 <p>Ссылка действительна 1 час.</p>";
        return $this->mailer->send($user->email, 'Восстановление пароля', $html);
    }

    public function reset(string $token, string $newPassword): bool
    {
        $row = $this->tokens->findValid($token);
        if (!$row) return false;
        $userId = (int)$row['user_id'];
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo = \App\Database\Connection::get();
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $userId]);
        $this->tokens->deleteByUserId($userId);
        return true;
    }
}
