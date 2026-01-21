<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;
use App\Domain\Support\Helpers;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\PasswordResetTokenRepository;
use App\Domain\Service\MailerService;
use App\Domain\Service\PasswordResetService;

Helpers::ensureSession();
Bootstrap::run();

$sent = false;
$error = null;

if (Helpers::isPost()) {
    $email = trim($_POST['email'] ?? '');
    $service = new PasswordResetService(
        new UserRepository(),
        new PasswordResetTokenRepository(),
        new MailerService()
    );
    $sent = $service->request($email);
    if (!$sent) $error = 'Не удалось отправить письмо. Проверьте настройки почты.';
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Восстановление пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/styles.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Восстановление пароля</h1>
    <?php if ($sent && !$error): ?>
        <div class="alert alert-success">Если такой email существует, на него отправлено письмо с инструкциями.</div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo App\Support\Helpers::e($error); ?></div>
    <?php endif; ?>

    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #4ca1af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .container {
            max-width: 500px;
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

        .container h1 {
            text-align: center;
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
            background: #4ca1af;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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
            border-color: #fdbb2d;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(253, 187, 45, 0.1);
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
            background: linear-gradient(135deg, #fdbb2d, #22c1c3);
            color: white;
            box-shadow: 0 4px 15px rgba(253, 187, 45, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(253, 187, 45, 0.4);
        }

        .btn-link {
            background: transparent;
            color: #6c757d;
            text-decoration: none;
            border: 2px solid rgba(108, 117, 125, 0.3);
        }

        .btn-link:hover {
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

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .form-label {
            font-weight: 600;
            color: #4ca1af;
            margin-bottom: 0.5rem;
            display: block;
        }

        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 30px 20px;
            }

            .card {
                padding: 20px;
            }
        }
    </style>



    <form method="post" class="card">
        <div class="mb-3">
            <label class="form-label">Ваш email</label>
            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
        </div>
        <button class="btn btn-primary" type="submit">Отправить письмо</button>
        <a class="btn btn-link" href="/login.php">Назад к входу</a>
    </form>
</div>
</body>
</html>
