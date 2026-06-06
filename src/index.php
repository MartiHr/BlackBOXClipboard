<?php
session_start();

$pdo = require __DIR__ . '/../config/db.php';

require_once __DIR__ . '/Router.php';

$router = new Router($pdo);
$router->add('home', 'HomeController', 'index');

$page = $_GET['page'] ?? 'home';
$router->dispatch($page);
