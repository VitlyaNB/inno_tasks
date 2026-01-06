<?php
declare(strict_types=1);

use App\Domain\Support\Helpers;
use App\Domain\Repository\UserRepository;
use App\Domain\Service\AuthService;

require_once __DIR__ . '/../vendor/autoload.php';
Helpers::ensureSession();

$errors = [];

if (Helpers::isPost()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '') {
        $errors[] = 'Имя обязательно';
    }

    if (!Helpers::isValidEmail($email)) {
        $errors[] = 'Некорректный email';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов';
    }

    if ($password !== $confirm) {
        $errors[] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
        $service = new AuthService(new UserRepository());
        $userId = $service->register($name, $email, $password);

        if ($userId === null) {
            $errors[] = 'Пользователь с таким email уже существует';
        } else {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user'] = $email;
            header('Location: /user.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .register-box {
            max-width: 420px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>

    <style>
        body {
            background: linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-box {
            max-width: 420px;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>



</head>
<body>
<div class="register-box">
    <h2 class="mb-4 text-center">Регистрация</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register.php">
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($name ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($email ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="confirm" class="form-label">Подтвердите пароль</label>
            <input type="password" name="confirm" id="confirm" class="form-control" required>
        </div>

        <!-- Кнопка теперь синяя -->
        <button type="submit" class="btn btn-primary w-100 mb-3">Зарегистрироваться</button>
        <a href="/login.php" class="btn btn-outline-secondary w-100">Назад к входу</a>
    </form>
</div>
</body>
</html>
