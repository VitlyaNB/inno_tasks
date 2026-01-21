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
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px 0;
        }

        .container {
            max-width: 1200px;
        }

        .admin-card {
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

        .admin-card h2 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .modern-table {
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .modern-table th {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modern-table td {
            padding: 18px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.6);
            color: #2c3e50;
            transition: all 0.3s ease;
        }

        .modern-table tr:hover td {
            background: rgba(255, 255, 255, 0.8);
            transform: scale(1.01);
        }

        .modern-table tr:last-child td {
            border-bottom: none;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger {
            background: rgba(220, 53, 69, 0.8);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 154, 158, 0.4);
        }

        .btn-secondary {
            background: rgba(108, 117, 125, 0.8);
            color: white;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
        }

        .btn-secondary:hover {
            background: #6c757d;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
        }

        .text-muted {
            color: #6c757d;
            font-style: italic;
        }

        .interests-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .interests-list li {
            background: rgba(79, 172, 254, 0.1);
            border-radius: 6px;
            padding: 4px 8px;
            margin: 2px 0;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-section {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .admin-card {
                margin: 10px;
                padding: 25px 15px;
            }

            .modern-table {
                font-size: 0.9rem;
            }

            .modern-table th,
            .modern-table td {
                padding: 12px 8px;
            }

            .admin-card h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-card">
        <h2>Админ‑панель</h2>

        <table class="modern-table">
            <thead>
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

        <div class="logout-section">
            <a href="/login.php" class="btn btn-secondary">Выйти из админ-панели</a>
        </div>
    </div>
</div>
</body>
</html>
