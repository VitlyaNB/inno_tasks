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
    <style>
        body {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px 0;
        }

        .container {
            max-width: 800px;
        }

        .user-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            margin: 20px auto;
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

        .user-card h2 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title {
            color: #5a6c7d;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(79, 172, 254, 0.2);
        }

        .interests-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .interest-item {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .interest-item:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        .interest-text {
            font-weight: 500;
            color: #2c3e50;
            font-size: 1.1rem;
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
            border-color: #4facfe;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            outline: none;
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
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .btn-success {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #fdbb2d, #22c1c3);
            color: white;
            box-shadow: 0 4px 15px rgba(253, 187, 45, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(253, 187, 45, 0.4);
        }

        .btn-danger {
            background: #DC143C;
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 154, 158, 0.4);
        }

        .btn-secondary {
            background: rgba(108, 117, 125, 0.8);
            color: white;
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: #6c757d;
            transform: translateY(-1px);
        }

        .form-section {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-top: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .empty-state {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            margin: 20px 0;
        }

        .d-none {
            display: none !important;
        }

        @media (max-width: 768px) {
            .user-card {
                margin: 10px;
                padding: 25px 20px;
            }

            .interest-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .btn {
                width: 100%;
                margin-right: 0;
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="user-card">
        <h2>Добро пожаловать, уважаемый пользователь!</h2>
        <p class="section-title">Ваши интересы:</p>

        <?php if (empty($interests)): ?>
            <div class="empty-state">
                Интересов пока нет. Добавьте свой первый интерес ниже!
            </div>
        <?php else: ?>
            <ul class="interests-list">
                <?php foreach ($interests as $interest): ?>
                    <li class="interest-item">
                        <span class="interest-text"><?= htmlspecialchars($interest->title) ?></span>
                        <div>
                            <button class="btn btn-warning"
                                    onclick="editInterest(<?= $interest->id ?>, '<?= htmlspecialchars($interest->title, ENT_QUOTES) ?>')">
                                Редактировать
                            </button>
                            <a href="/user.php?delete=<?= $interest->id ?>" class="btn btn-danger"
                               onclick="return confirm('Вы уверены, что хотите удалить этот интерес?')">Удалить</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="form-section">
            <!-- Форма добавления -->
            <form method="POST" action="/user.php" class="mb-4">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label for="interest" class="form-label" style="font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; display: block;">Добавить новый интерес</label>
                    <input type="text" name="interest" id="interest" class="form-control" placeholder="Введите название интереса" required>
                </div>
                <button type="submit" class="btn btn-success">Добавить интерес</button>
            </form>

            <!-- Форма редактирования -->
            <form method="POST" action="/user.php" id="editForm" class="d-none">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="mb-3">
                    <label for="editInterest" class="form-label" style="font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; display: block;">Редактировать интерес</label>
                    <input type="text" name="interest" id="editInterest" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Сохранить изменения</button>
                <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Отмена</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.3);">
            <a href="/logout.php" class="btn btn-danger">Выйти из аккаунта</a>
        </div>
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
