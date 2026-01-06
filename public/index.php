<?php

require_once __DIR__ . '/../vendor/autoload.php';

\App\Domain\Support\Helpers::ensureSession();
\App\Bootstrap::run();

header('Location: /login.php');
