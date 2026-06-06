<?php

require_once __DIR__ . '/Controller.php';
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
        $clipboards = $this->service->getAllClipboards();

        $this->render('home/index', [
            'clipboards'   => $clipboards,
            'extraCss'     => ['channels.css'],
            'showNav'      => true,
            'showActions'  => true,
            'currentPage'  => 'home',
            'mainClass'    => 'dashboard-main',
        ], 'Канали');
    }
}