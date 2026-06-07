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
        $id = (int) ($_GET['id'] ?? 0);
        $clipboard = $this->service->getClipboardById($id);

        if (!$clipboard) {
            header('HTTP/1.0 404 Not Found');
            echo 'Clipboard not found';
            return;
        }

        $isPublic = ($clipboard['visibility'] ?? 'private') === 'public';
        $isLoggedIn = AuthService::isLoggedIn();
        $isOwner = $isLoggedIn && (int) $clipboard['owner_id'] === $_SESSION['user_id'];

        if (!$isPublic) {
            AuthService::requireLogin();
            if (!$isOwner) {
                http_response_code(403);
                echo 'Достъпът е отказан';
                return;
            }
        }

        $itemLimit = ($clipboard['mode'] ?? 'multi') === 'single' ? 1 : 50;
        $items = $this->service->getClipboardItems($id, $itemLimit);
        $allowedTypes = $this->service->getAllowedResourceTypesForClipboard($id);

        $canPost = $isLoggedIn && ($isPublic || $isOwner);

        $this->render('clipboard/show', [
            'clipboard'   => $clipboard,
            'items'       => $items,
            'allowedTypes' => $allowedTypes,
            'isOwner'     => $isOwner,
            'canPost'     => $canPost,
            'extraCss'    => ['auth.css', 'show.css'],
            'showNav'     => $isLoggedIn,
            'showActions' => $isLoggedIn,
            'currentPage' => 'clipboard',
            'mainClass'   => 'clipboard-main',
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

    public function serveFile(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        $item = $this->service->getItemById($id);

        if (!$item || empty($item['file_path'])) {
            http_response_code(404);
            echo 'File not found';
            return;
        }

        $fullPath = UPLOADS_DIR . '/' . basename($item['file_path']);

        if (!is_file($fullPath)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }

        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        $mime = match (strtolower($ext)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

        readfile($fullPath);
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
