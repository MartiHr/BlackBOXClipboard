<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Services/ClipboardService.php';
require_once __DIR__ . '/../Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../Repositories/ResourceRepository.php';

class ClipboardController extends Controller
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

    public function createGet(): void
    {
        AuthService::requireLogin();

        $types = $this->service->getAllResourceTypes();

        $this->render('clipboard/create', [
            'resourceTypes' => $types,
            'extraCss'      => ['auth.css', 'create.css'],
            'showNav'       => true,
            'showActions'   => true,
            'currentPage'   => 'create',
            'mainClass'     => 'create-main',
        ], 'Create Clipboard');
    }

    public function show(): void
    {
        AuthService::requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $clipboard = $this->service->getClipboardById($id);

        if (!$clipboard) {
            header('HTTP/1.0 404 Not Found');
            echo 'Clipboard not found';
            return;
        }

        $items = $this->service->getClipboardItems($id, 10);
        $formattedItems = array_map(function ($item) {
            return [
                'title' => htmlspecialchars(substr($item['title'] ?? '', 0, 50)),
                'type' => htmlspecialchars($item['type'] ?? 'text'),
                'summary' => htmlspecialchars(substr($item['title'] ?? '', 0, 100)),
                'timestamp' => $item['created_at'] ? date('j M H:i', strtotime($item['created_at'])) : 'unknown',
                'status' => 'Активен',
            ];
        }, $items);

        $this->render('clipboard/show', [
            'clipboard' => $clipboard,
            'items' => $formattedItems,
            'extraCss' => ['auth.css', 'create.css'],
            'showNav' => true,
            'showActions' => true,
            'currentPage' => 'clipboard',
            'mainClass' => 'clipboard-main',
        ], 'Клипборд: ' . htmlspecialchars($clipboard['name']));
    }

    public function create()
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        try {
            $id = $this->service->createClipboard(
                $input,
                $_SESSION['user_id']
            );

            echo json_encode([
                'success' => true,
                'clipboard_id' => $id
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}