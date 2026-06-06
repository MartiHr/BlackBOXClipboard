<?php

require_once __DIR__ . '/Env.php';

$envPath = __DIR__ . '/../.env';
$env = Env::load($envPath);

$host = $env['MYSQL_HOST'] ?? getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = $env['MYSQL_PORT'] ?? getenv('MYSQL_PORT') ?: '3306';
$db   = $env['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE') ?: 'blackbox_db';
$user = $env['MYSQL_USER'] ?? getenv('MYSQL_USER') ?: 'root';
$pass = $env['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD') ?: '';
$charset = $env['MYSQL_CHARSET'] ?? getenv('MYSQL_CHARSET') ?: 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    die("Error connecting to database: " . $e->getMessage());
}

return $pdo;