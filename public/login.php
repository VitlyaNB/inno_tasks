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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .login-box {
            max-width: 420px;
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

        .login-box h2 {
            text-align: center;
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-success {
            background: transparent;
            border: 2px solid #28a745;
            color: #28a745;
        }

        .btn-outline-success:hover {
            background: #28a745;
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 2px solid #6c757d;
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
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .d-flex {
            display: flex;
            gap: 10px;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .w-50 {
            width: 48%;
        }

        .me-2 {
            margin-right: 10px;
        }

        @media (max-width: 480px) {
            .login-box {
                margin: 20px;
                padding: 30px 20px;
            }

            .d-flex {
                flex-direction: column;
            }

            .w-50 {
                width: 100%;
            }
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
        <a href="/register.php" class="btn btn-outline-success w-50 me-2">Регистрация</a>
        <a href="/forgot_password.php" class="btn btn-outline-secondary w-50">Забыли пароль?</a>
    </div>
</div>
</body>
</html>
