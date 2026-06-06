<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Services/ClipboardService.php';
require_once __DIR__ . '/../Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../Repositories/ResourceRepository.php';
require_once __DIR__ . '/../Repositories/ItemRepository.php';

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

        $itemLimit = ($clipboard['mode'] ?? 'multi') === 'single' ? 1 : 50;
        $items = $this->service->getClipboardItems($id, $itemLimit);

        $isOwner = AuthService::isLoggedIn() && (int) $clipboard['owner_id'] === $_SESSION['user_id'];

        $this->render('clipboard/show', [
            'clipboard'  => $clipboard,
            'items'      => $items,
            'isOwner'    => $isOwner,
            'extraCss'   => ['auth.css', 'show.css'],
            'showNav'    => true,
            'showActions' => true,
            'currentPage' => 'clipboard',
            'mainClass'  => 'clipboard-main',
        ], 'Клипборд: ' . htmlspecialchars($clipboard['name']));
    }

    public function create(): void
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

    public function createItem(): void
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        try {
            $id = $this->service->createItem(
                $input,
                $_SESSION['user_id']
            );

            echo json_encode([
                'success' => true,
                'item_id' => $id,
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteItem(): void
    {
        header('Content-Type: application/json');

        if (!AuthService::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $itemId = (int) ($input['item_id'] ?? 0);

        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['error' => 'Item ID is required']);
            return;
        }

        try {
            $this->service->deleteItem($itemId, $_SESSION['user_id']);

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
