<?php

require_once __DIR__ . '/../config/app.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => BASE_PATH ?: '/',
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

require_once __DIR__ . '/Router.php';
$router = new Router($pdo);

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

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (BASE_PATH !== '' && str_starts_with($uri, BASE_PATH)) {
    $uri = substr($uri, strlen(BASE_PATH));
}
$uri = trim($uri, '/');
$page = $uri ?: 'home';

$router->dispatch($page);