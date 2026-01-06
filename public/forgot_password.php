<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Domain\Support\Helpers;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\PasswordResetTokenRepository;
use App\Domain\Service\MailerService;
use App\Domain\Service\PasswordResetService;

Helpers::ensureSession();
\App\Bootstrap::run();

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
<body class="bg-light">
<div class="container container-narrow py-5">
    <h1 class="mb-4">Восстановление пароля</h1>
    <?php if ($sent && !$error): ?>
        <div class="alert alert-success">Если такой email существует, на него отправлено письмо с инструкциями.</div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo App\Support\Helpers::e($error); ?></div>
    <?php endif; ?>

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



    <form method="post" class="card p-4">
        <div class="mb-3">
            <label class="form-label">Ваш email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button class="btn btn-primary" type="submit">Отправить письмо</button>
        <a class="btn btn-link" href="/login.php">Назад к входу</a>
    </form>
</div>
</body>
</html>
