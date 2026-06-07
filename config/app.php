<?php

require_once __DIR__ . '/Env.php';

$envPath = __DIR__ . '/../.env';

$env = Env::load($envPath);

$basePath = $env['BASE_PATH'] ?? '';
$basePath = rtrim($basePath, '/');
define('BASE_PATH', $basePath);

define('APP_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $basePath);
