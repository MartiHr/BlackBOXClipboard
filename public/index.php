<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// 1. Вземаме базата данни от Docker
$pdo = require __DIR__ . '/../config/db.php';

// 2. Включваме рутера
require_once __DIR__ . '/Router.php';
$router = new Router($pdo);

// 3. РЕГИСТРИРАМЕ СТРАНИЦИТЕ (Точно тук добавяш новите неща по същия начин!)
// Начин на писане: add('име_в_URL', 'ИмеНаКонтролер', 'метод_в_контролера')

$router->get('home', 'HomeController', 'index');
$router->get('clipboard/create', 'ClipboardController', 'createGet');
$router->post('clipboard/create', 'ClipboardController', 'create');
$router->get('login', 'AuthController', 'loginGet');
$router->post('login', 'AuthController', 'loginPost');
$router->get('register', 'AuthController', 'registerGet');
$router->post('register', 'AuthController', 'registerPost');
$router->get('logout', 'AuthController', 'logout');
// $router->get('clipboard', 'ClipboardController', 'show'); // Показва конкретен канал
// $router->post('copy', 'ShareController', 'handleCopy');    // Поема качването на данни (POST)

// 4. ОПРЕДЕЛЯМЕ ТЕКУЩАТА СТРАНИЦА от URL
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = trim($uri, '/');
$page = $uri ?: 'home';

// 5. ИЗПЪЛНЕНИЕ
// Рутерът сам ще направи "new AuthController($pdo)" и ще извика правилния метод!
$router->dispatch($page);