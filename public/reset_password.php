<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Helpers;
use App\Support\Validator;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\PasswordResetTokenRepository;
use App\Domain\Service\MailerService;
use App\Domain\Service\PasswordResetService;

Helpers::ensureSession();
\App\Bootstrap::run();

$token = $_GET['token'] ?? '';
$done = false;
$error = null;

if (Helpers::isPost()) {
    $token = $_POST['token'] ?? '';
    $new = $_POST['password'] ?? '';
    if (!Validator::password($new)) {
        $error = 'Пароль минимум 6 символов, только латиница и цифры.';
    } else {
        $service = new PasswordResetService(
            new UserRepository(),
            new PasswordResetTokenRepository(),
            new MailerService()
        );
        $done = $service->reset($token, $new);
        if (!$done) $error = 'Ссылка недействительна или истекла.';
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Сброс пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container container-narrow py-5">
    <h1 class="mb-4">Сброс пароля</h1>
    <?php if ($done && !$error): ?>
        <div class="alert alert-success">Пароль обновлен. Теперь вы можете войти.</div>
        <a class="btn btn-primary" href="/login.php">Войти</a>
    <?php else: ?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo App\Support\Helpers::e($error); ?></div><?php endif; ?>
        <form method="post" class="card p-4">
            <input type="hidden" name="token" value="<?php echo App\Support\Helpers::e($token); ?>">
            <div class="mb-3">
                <label class="form-label">Новый пароль</label>
                <input type="password" name="password" class="form-control" required placeholder="Минимум 6, без спецсимволов">
            </div>
            <button class="btn btn-primary" type="submit">Обновить пароль</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
