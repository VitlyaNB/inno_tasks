<?php
declare(strict_types=1);

use App\Domain\Support\Helpers;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\InterestRepository;

require_once __DIR__ . '/../vendor/autoload.php';
Helpers::requireUser();

if ($_SESSION['user_id']) {
    $userRepo = new UserRepository();
    $currentUser = $userRepo->findById((int)$_SESSION['user_id']);
    if (!$currentUser || $currentUser->role !== 'admin') {
        header('Location: /user.php');
        exit;
    }
}

$userRepo = new UserRepository();
$interestRepo = new InterestRepository();

if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    $userRepo->delete($id);
    header('Location: /admin.php');
    exit;
}

if (isset($_GET['delete_interest'])) {
    $id = (int)$_GET['delete_interest'];
    $interestRepo->deleteByIdAdmin($id);
    header('Location: /admin.php');
    exit;
}

$users = $userRepo->all();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ‑панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-4">Админ‑панель</h2>

        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Дата регистрации</th>
                <th>Интересы</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= $u['created_at'] ?></td>
                    <td>
                        <?php
                        $interests = $interestRepo->byUserId((int)$u['id']);
                        if (empty($interests)) {
                            echo '<span class="text-muted">нет</span>';
                        } else {
                            echo '<ul class="mb-0">';
                            foreach ($interests as $i) {
                                echo '<li>' . htmlspecialchars($i['title']) .
                                        ' <a href="/admin.php?delete_interest=' . $i['id'] . '" class="btn btn-sm btn-danger ms-2">Удалить</a></li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="/admin.php?delete_user=<?= $u['id'] ?>" class="btn btn-sm btn-danger">Удалить пользователя</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <a href="/login.php" class="btn btn-secondary mt-3">Выйти</a>
    </div>
</div>
</body>
</html>
