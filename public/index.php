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

$pdo = require __DIR__ . '/../config/db.php';

require_once __DIR__ . '/../config/storage.php';
require_once __DIR__ . '/../config/mail.php';

require_once __DIR__ . '/../src/Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../src/Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../src/Repositories/ItemRepository.php';
require_once __DIR__ . '/../src/Repositories/ResourceRepository.php';
require_once __DIR__ . '/../src/Repositories/SubscriptionRepository.php';
require_once __DIR__ . '/../src/Repositories/ExpirationRepository.php';
require_once __DIR__ . '/../src/Services/ClipboardService.php';

$gcService = new ClipboardService(
    new ClipboardRepository($pdo),
    new ResourceTypeRepository($pdo),
    $pdo
);
$gcService->runGC();
unset($gcService);

// 2. Включваме рутера
require_once __DIR__ . '/Router.php';
$router = new Router($pdo);

// 3. РЕГИСТРИРАМЕ СТРАНИЦИТЕ (Точно тук добавяш новите неща по същия начин!)
// Начин на писане: add('име_в_URL', 'ИмеНаКонтролер', 'метод_в_контролера')

$router->get('home', 'HomeController', 'index');
$router->get('clipboard', 'ClipboardController', 'show');
$router->get('clipboard/create', 'ClipboardController', 'createGet');
$router->post('clipboard/create', 'ClipboardController', 'create');
$router->post('clipboard/item/create', 'ClipboardController', 'createItem');
$router->post('clipboard/item/delete', 'ClipboardController', 'deleteItem');
$router->post('clipboard/subscribe', 'ClipboardController', 'toggleSubscribe');
$router->get('clipboard/edit', 'ClipboardController', 'editGet');
$router->post('clipboard/edit', 'ClipboardController', 'editPost');
$router->post('clipboard/subscribe/email', 'ClipboardController', 'updateEmailPref');
$router->get('clipboard/share', 'ClipboardController', 'shareGet');
$router->post('clipboard/share', 'ClipboardController', 'sharePost');
$router->get('clipboard/updates', 'ClipboardController', 'getUpdates');
$router->get('file', 'ClipboardController', 'serveFile');
$router->get('notifications/poll', 'NotificationController', 'poll');
$router->get('notifications/list', 'NotificationController', 'list');
$router->post('notifications/read', 'NotificationController', 'read');
$router->get('login', 'AuthController', 'loginGet');
$router->post('login', 'AuthController', 'loginPost');
$router->get('register', 'AuthController', 'registerGet');
$router->post('register', 'AuthController', 'registerPost');
$router->get('logout', 'AuthController', 'logout');
$router->get('profile', 'ProfileController', 'show');
$router->get('admin', 'AdminController', 'index');
$router->get('admin/export', 'AdminController', 'export');
$router->post('admin/import', 'AdminController', 'import');
// $router->get('clipboard', 'ClipboardController', 'show'); // Показва конкретен канал
// $router->post('copy', 'ShareController', 'handleCopy');    // Поема качването на данни (POST)

// 4. ОПРЕДЕЛЯМЕ ТЕКУЩАТА СТРАНИЦА от URL
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = trim($uri, '/');
$page = $uri ?: 'home';

// 5. ИЗПЪЛНЕНИЕ
// Рутерът сам ще направи "new AuthController($pdo)" и ще извика правилния метод!
$router->dispatch($page);