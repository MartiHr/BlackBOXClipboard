<?php
// public/index.php

session_start();

// 1. Вземаме базата данни от Docker
$pdo = require __DIR__ . '/../config/db.php';

// 2. Включваме рутера
require_once __DIR__ . '/Router.php';
$router = new Router($pdo);

// 3. РЕГИСТРИРАМЕ СТРАНИЦИТЕ (Точно тук добавяш новите неща по същия начин!)
// Начин на писане: add('име_в_URL', 'ИмеНаКонтролер', 'метод_в_контролера')

$router->add('home', 'HomeController', 'index');
// $router->add('login', 'AuthController', 'login');         // Показва формата за вход
// $router->add('register', 'AuthController', 'register');   // Показва формата за регистрация
// $router->add('clipboard', 'ClipboardController', 'show'); // Показва конкретен канал
// $router->add('copy', 'ShareController', 'handleCopy');    // Поема качването на данни (POST)

// 4. ОПРЕДЕЛЯМЕ ТЕКУЩАТА СТРАНИЦА
$page = $_GET['page'] ?? 'home';

// 5. ИЗПЪЛНЕНИЕ
// Рутерът сам ще направи "new AuthController($pdo)" и ще извика правилния метод!
$router->dispatch($page);