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
            'resourceTypes' => $types
        ], 'Create Clipboard');
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