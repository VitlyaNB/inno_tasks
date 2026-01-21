<?php
declare(strict_types=1);

use App\Domain\Support\Helpers;
use App\Domain\Service\AuthService;
use App\Domain\Repository\UserRepository;
use App\Domain\DTO\RegisterDTO;
use App\Domain\DTO\UserDTO;

require_once __DIR__ . '/../vendor/autoload.php';
Helpers::ensureSession();

$errors = [];

if (Helpers::isPost()) {

    $registerDto = RegisterDTO::fromPost();

    $errors = $registerDto->validate();

    if (empty($errors)) {
        $service = new AuthService(new UserRepository());
        $userDto = $service->register($registerDto);

        if ($userDto === null) {
            $errors[] = 'Пользователь с таким email уже существует';
        } else {
            $userDto->toSession();

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
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .register-box {
            max-width: 480px;
            width: 100%;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-box h2 {
            text-align: center;
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-control {
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            border-color: #f5576c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(245, 87, 108, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 2px solid rgba(108, 117, 125, 0.5);
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 1.5rem;
            list-style: none;
            padding-left: 20px;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-danger ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .alert-danger li {
            margin-bottom: 0.5rem;
        }

        .alert-danger li:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 480px) {
            .register-box {
                margin: 20px;
                padding: 30px 20px;
            }
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

        <button type="submit" class="btn btn-primary w-100 mb-3">Зарегистрироваться</button>
        <a href="/login.php" class="btn btn-outline-secondary w-100">Назад к входу</a>
    </form>
</div>
</body>
</html>
