<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Domain\Support\Helpers;

require_once __DIR__ . '/../vendor/autoload.php';

Helpers::ensureSession();
Bootstrap::run();

header('Location: /login.php');
