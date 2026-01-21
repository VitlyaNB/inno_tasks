<?php
declare(strict_types=1);

use App\Domain\Support\Helpers;
use App\Domain\Repository\InterestRepository;
use App\Domain\Service\InterestService;
use App\Domain\DTO\InterestDTO;
use App\Domain\DTO\UserDTO;

require_once __DIR__ . '/../vendor/autoload.php';
Helpers::requireUser();

$userDto = UserDTO::fromSession();
$userId = $userDto->id;

$service = new InterestService(new InterestRepository());

if (Helpers::isPost()) {
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['add', 'edit'])) {
        $interestDto = InterestDTO::fromPost($action);

        if ($interestDto && $interestDto->validate()) {
            if ($action === 'add') {
                $service->add($interestDto);
            } elseif ($action === 'edit') {
                $service->update($interestDto);
            }
        }
    }

    header('Location: /user.php');
    exit;
}

if (isset($_GET['delete'])) {
    $interestId = (int)$_GET['delete'];

    if ($interestId > 0) {
        $interestDto = new InterestDTO($interestId, '', $userId);
        $service->delete($interestDto);
    }

    header('Location: /user.php');
    exit;
}

$interests = $service->list($userId);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="mb-3">Добро пожаловать, уважаемый пользователь!</h2>
        <p>Ваши интересы:</p>

        <?php if (empty($interests)): ?>
            <p class="text-muted">Интересов пока нет.</p>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($interests as $interest): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($interest->title) ?>
                        <div>
                            <button class="btn btn-sm btn-warning"
                                    onclick="editInterest(<?= $interest->id ?>, '<?= htmlspecialchars($interest->title, ENT_QUOTES) ?>')">
                                Редактировать
                            </button>

                            <a href="/user.php?delete=<?= $interest->id ?>" class="btn btn-sm btn-danger">Удалить</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Форма добавления -->
        <form method="POST" action="/user.php" class="mb-3">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label for="interest" class="form-label">Новый интерес</label>
                <input type="text" name="interest" id="interest" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Добавить</button>
        </form>

        <form method="POST" action="/user.php" id="editForm" class="mb-3 d-none">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="mb-3">
                <label for="editInterest" class="form-label">Редактировать интерес</label>
                <input type="text" name="interest" id="editInterest" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Отмена</button>
        </form>

        <a href="/logout.php" class="btn btn-danger mt-3">Выйти</a>
    </div>
</div>

<script>
    function editInterest(id, value) {
        document.getElementById('editForm').classList.remove('d-none');
        document.getElementById('editId').value = id;
        document.getElementById('editInterest').value = value;
        window.scrollTo(0, document.body.scrollHeight);
    }
    function cancelEdit() {
        document.getElementById('editForm').classList.add('d-none');
    }
</script>
</body>
</html>
