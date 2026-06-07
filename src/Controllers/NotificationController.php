<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Services/ClipboardService.php';
require_once __DIR__ . '/../Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../Repositories/ResourceRepository.php';

class NotificationController extends Controller
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

    public function poll(): void
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $count = $this->service->countUnreadNotifications($userId);

        echo json_encode(['unread' => $count]);
    }

    public function list(): void
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $notifications = $this->service->getNotifications($_SESSION['user_id'], 20);

        echo json_encode(['notifications' => $notifications]);
    }

    public function read(): void
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if (!empty($input['all'])) {
            $this->service->markAllNotificationsRead($_SESSION['user_id']);
        } else {
            $id = (int) ($input['id'] ?? 0);
            if ($id) {
                $this->service->markNotificationRead($id, $_SESSION['user_id']);
            }
        }

        echo json_encode(['success' => true]);
    }
}
