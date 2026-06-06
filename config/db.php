<?php
// config/db.php

/**
 * Настройки за връзка с MySQL контейнера в Docker.
 * За разработка използваме IP адреса 127.0.0.1, за да избегнем проблеми 
 * с резолвването на 'localhost' в Docker мрежите под Windows/macOS.
 */
$host     = '127.0.0.1'; 
$port     = '3306';        // Портът, който си мапнал в docker-compose / docker run
$db       = 'blackbox_db'; // Името на базата данни, която си създал в Docker
$user     = 'root';        // Твоят Docker MySQL потребител
$pass     = 'secret';      // Твоята Docker MySQL парола
$charset  = 'utf8mb4';     // Задължително за правилна поддръжка на емоджита и специални символи

// Data Source Name (DSN) - дефинира драйвъра, хоста, порта, базата и енкодинга
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

/**
 * Важни PDO атрибути за архитектурата на проекта:
 * 1. ATTR_ERRMODE => ERRMODE_EXCEPTION: Хвърля PDOException при SQL грешка (критично за дебъгване във ФМИ).
 * 2. ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC: Връща данните като чисти асоциативни масиви.
 * 3. ATTR_EMULATE_PREPARES => false: Изключва емулацията на prepared statements. Накрая MySQL реално
 * инжектира данните, което гарантира 100% защита от SQL Injection.
 */
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Инициализиране на глобалния PDO обект
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // В случай на неуспешна връзка (например спрян Docker контейнер)
    // Хвърляме чиста грешка, без да изнасяме пароли в лога
    http_response_code(500);
    die("Грешка при връзка с базата данни: " . $e->getMessage());
}

// Връщаме PDO инстанцията за глобално ползване в index.php
return $pdo;