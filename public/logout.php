<?php
declare(strict_types=1);

use App\Domain\Support\Helpers;

require_once __DIR__ . '/../vendor/autoload.php';

Helpers::ensureSession();

unset($_SESSION['user']);

session_destroy();

header('Location: /login.php');
exit;
