<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Helpers;
use App\Support\Validator;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\InterestRepository;

Helpers::requireAdmin();

$userId = (int) ($_GET['user_id'] ?? 0);
$users = new UserRepository();
$user = $users->findById($userId);
if (!$user) Helpers::redirect('/admin.php');

$repo = new InterestRepository();

if (Helpers::isPost()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        if (Validator::interestTitle($title)) $repo->create($userId, $title);
    } elseif ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        if ($id > 0 && Validator::interestTitle($title)) $repo->update($id, $userId, $title);
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) $repo->deleteByIdAdmin($id);
    }
}

$interests = $repo->byUserId($userId);
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
