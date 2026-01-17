<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Helpers;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\InterestRepository;
use App\Domain\Service\InterestService;
use App\Domain\DTO\InterestDTO;

Helpers::requireAdmin();

$userId = (int)($_GET['user_id'] ?? 0);

$userRepository = new UserRepository();
$user = $userRepository->findById($userId);

if (!$user) {
    Helpers::redirect('/admin.php');
    exit;
}

$interestService = new InterestService(new InterestRepository());

if (Helpers::isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $interestService->deleteAdmin($id);
        }
    } else {
        $interestDto = InterestDTO::forAdmin($userId);

        if ($interestDto && $interestDto->validate()) {
            if ($action === 'add') {
                $interestService->add($interestDto);
            } elseif ($action === 'edit') {
                $interestService->update($interestDto);
            }
        }
    }

    header("Location: /admin_user_interest.php?user_id=" . $userId);
    exit;
}

$interests = $interestService->getUserInterests($userId);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Интересы пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container container-narrow py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Интересы: <?php echo htmlspecialchars($user->email); ?></h1>
        <a class="btn btn-outline-secondary" href="/admin.php">Назад</a>
    </div>

    <div class="card p-3 mb-4">
        <form method="post" class="row g-2">
            <input type="hidden" name="action" value="add">
            <div class="col-9">
                <input type="text"
                       name="title"
                       class="form-control"
                       placeholder="Добавить интерес"
                       required
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>
            <div class="col-3">
                <button class="btn btn-primary w-100" type="submit">Добавить</button>
            </div>
        </form>
    </div>

    <table class="table table-striped table-fixed">
        <thead>
        <tr>
            <th class="w-10">ID</th>
            <th class="w-60">Интерес</th>
            <th class="w-30">Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($interests as $interest): ?>
            <tr>
                <td><?= (int)$interest['id'] ?></td>
                <td><?= htmlspecialchars($interest['title']) ?></td>
                <td>
                    <form method="post" class="d-inline-flex gap-2">
                        <input type="hidden" name="id" value="<?= (int)$interest['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button class="btn btn-sm btn-danger"
                                type="submit"
                                onclick="return confirm('Удалить интерес?')">
                            Удалить
                        </button>
                    </form>
                    <form method="post" class="d-inline-flex gap-2 ms-2">
                        <input type="hidden" name="id" value="<?= (int)$interest['id'] ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="text"
                               name="title"
                               class="form-control form-control-sm"
                               placeholder="Новое название"
                               value="<?= htmlspecialchars($interest['title']) ?>"
                               required
                               style="width: 220px;">
                        <button class="btn btn-sm btn-secondary" type="submit">Редактировать</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Интересы пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container container-narrow py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Интересы: <?php echo App\Support\Helpers::e($user->email); ?></h1>
        <a class="btn btn-outline-secondary" href="/admin.php">Назад</a>
    </div>

    <div class="card p-3 mb-4">
        <form method="post" class="row g-2">
            <input type="hidden" name="action" value="add">
            <div class="col-9">
                <input type="text" name="title" class="form-control" placeholder="Добавить интерес" required>
            </div>
            <div class="col-3">
                <button class="btn btn-primary w-100" type="submit">Добавить</button>
            </div>
        </form>
    </div>

    <table class="table table-striped table-fixed">
        <thead>
        <tr>
            <th class="w-10">ID</th>
            <th class="w-60">Интерес</th>
            <th class="w-30">Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($interests as $i): ?>
            <tr>
                <td><?php echo (int)$i['id']; ?></td>
                <td><?php echo App\Support\Helpers::e($i['title']); ?></td>
                <td>
                    <form method="post" class="d-inline-flex gap-2">
                        <input type="hidden" name="id" value="<?php echo (int)$i['id']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button class="btn btn-sm btn-danger" type="submit">Удалить</button>
                    </form>
                    <form method="post" class="d-inline-flex gap-2 ms-2">
                        <input type="hidden" name="id" value="<?php echo (int)$i['id']; ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Новое название" required style="width: 220px;">
                        <button class="btn btn-sm btn-secondary" type="submit">Редактировать</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
