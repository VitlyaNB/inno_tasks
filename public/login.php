<?php
declare(strict_types=1);

use App\Domain\Support\Helpers;
use App\Domain\Service\AuthService;
use App\Domain\Repository\UserRepository;
use App\Domain\DTO\LoginDTO;
use App\Domain\DTO\UserDTO;

require_once __DIR__ . '/../vendor/autoload.php';
Helpers::ensureSession();

$error = '';

if (Helpers::isPost()) {
    $loginDto = LoginDTO::fromPost();

    $validationErrors = $loginDto->validate();

    if (!empty($validationErrors)) {
        $error = $validationErrors[0];
    } else {
        $service = new AuthService(new UserRepository());
        $userDto = $service->login($loginDto);

        if ($userDto === null) {
            $error = 'Неверный email или пароль';
        } else {
            $userDto->toSession();

            if ($userDto->role === 'admin') {
                header('Location: /admin.php');
            } else {
                header('Location: /user.php');
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2 class="mb-4 text-center">Вход</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Войти</button>
    </form>

    <div class="d-flex justify-content-between">
        <a href="/register.php" class="btn btn-outline-success w-50 me-2">Зарегистрироваться</a>
        <a href="/forgot_password.php" class="btn btn-outline-secondary w-50">Забыли пароль?</a>
    </div>
</div>
</body>
</html>
