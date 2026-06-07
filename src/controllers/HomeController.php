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

        $userId = AuthService::isLoggedIn() ? (int) $_SESSION['user_id'] : null;

        $totalClipboards = $this->service->countClipboardsForUser($userId);
        $totalPages = max(1, (int) ceil($totalClipboards / $perPage));
        $pageNumber = min($pageNumber, $totalPages);

        $clipboards = $this->service->getClipboardsPageForUser($userId, $pageNumber, $perPage);

        $traffic = $this->service->getTodayTraffic();

        $isLoggedIn = AuthService::isLoggedIn();

        $this->render('home/index', [
            'clipboards'   => $clipboards,
            'traffic'      => $traffic,
            'pagination'   => [
                'current' => $pageNumber,
                'total'   => $totalPages,
            ],
            'extraCss'     => ['channels.css'],
            'showNav'      => true,
            'showActions'  => $isLoggedIn,
            'currentPage'  => 'home',
            'mainClass'    => 'dashboard-main',
        ], 'Канали');
    }
}