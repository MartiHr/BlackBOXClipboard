<?php
session_start();

$pdo = null;

// require_once __DIR__ . '/../config/db.php';
// require_once __DIR__ . '/../src/Services/GarbageCollector.php';

// GarbageCollector::run($pdo);


// ====================================================================
// 2. РУТИРАНЕ (Управление на трафика)
// ====================================================================

// Вземаме коя страница иска потребителят. Ако няма нищо, по подразбиране е 'home'
// Пример: ако URL-ът е index.php?page=login, то $page ще бъде 'login'
$page = $_GET['page'] ?? 'home';

// Проверяваме стойността на $page и извикваме правилния контролер
switch ($page) {
    
    case 'home':
        // Зареждаме контролера за началната страница
        require_once __DIR__ . '/../src/controllers/HomeController.php';
        $controller = new HomeController($pdo ?? null);
        $controller->index();
        break;


    // ====================================================================
    // 3. ПРАВИЛНО ХЕНДЛИРАНЕ НА ГРЕШКИ (Ако потребителят напише глупост)
    // ====================================================================
    default:
        http_response_code(404);
        echo "<h1>Грешка 404: Страницата не е намерена</h1>";
        break;
}