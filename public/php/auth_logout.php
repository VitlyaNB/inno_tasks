<?php

session_start();

$_SESSION = [];

session_destroy();

header("Location: /front/html/login.html");
exit;
