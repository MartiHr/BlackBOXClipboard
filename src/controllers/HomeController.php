<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Services/ClipboardService.php';
require_once __DIR__ . '/../Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../Repositories/ResourceRepository.php';

class HomeController extends Controller 
{
    private ClipboardService $service;

    public function __construct(PDO $db)
    {
        parent::__construct($db);

        $this->service = new ClipboardService(
            new ClipboardRepository($db),
            new ResourceTypeRepository($db),
            $db
        );
    }

    public function index(): void 
    {
        $pageNumber = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $pageNumber = max(1, $pageNumber);
        $perPage = 5;

        $filters = [];
        if (!empty($_GET['search'])) $filters['search'] = trim($_GET['search']);
        if (!empty($_GET['visibility'])) $filters['visibility'] = $_GET['visibility'];
        if (!empty($_GET['mode'])) $filters['mode'] = $_GET['mode'];
        if (!empty($_GET['sort'])) $filters['sort'] = $_GET['sort'];
        if (!empty($_GET['order'])) $filters['order'] = $_GET['order'];

        $userId = AuthService::isLoggedIn() ? (int) $_SESSION['user_id'] : null;
        $isAdmin = AuthService::isLoggedIn() && ($_SESSION['role'] ?? null) === 'admin';

        if ($isAdmin) {
            $totalClipboards = $this->service->countClipboards($filters);
            $totalPages = max(1, (int) ceil($totalClipboards / $perPage));
            $pageNumber = min($pageNumber, $totalPages);
            $clipboards = $this->service->getClipboardsPage($pageNumber, $perPage, $filters);
        } else {
            $totalClipboards = $this->service->countClipboardsForUser($userId, $filters);
            $totalPages = max(1, (int) ceil($totalClipboards / $perPage));
            $pageNumber = min($pageNumber, $totalPages);
            $clipboards = $this->service->getClipboardsPageForUser($userId, $pageNumber, $perPage, $filters);
        }

        $traffic = $this->service->getTodayTraffic();

        $isLoggedIn = AuthService::isLoggedIn();

        $this->render('home/index', [
            'clipboards'   => $clipboards,
            'traffic'      => $traffic,
            'pagination'   => [
                'current' => $pageNumber,
                'total'   => $totalPages,
            ],
            'filters'      => $filters,
            'extraCss'     => ['channels.css'],
            'showNav'      => true,
            'showActions'  => $isLoggedIn,
            'currentPage'  => 'home',
            'mainClass'    => 'dashboard-main',
        ], 'Канали');
    }
}