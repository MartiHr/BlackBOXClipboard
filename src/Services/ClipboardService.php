<?php

require_once __DIR__ . '/../Repositories/ItemRepository.php';

class ClipboardService
{
    private ClipboardRepository $clipRepo;
    private ResourceTypeRepository $typeRepo;
    private ItemRepository $itemRepo;
    private PDO $db;

    public function __construct(
        ClipboardRepository $clipRepo,
        ResourceTypeRepository $typeRepo,
        PDO $db
    ) {
        $this->clipRepo = $clipRepo;
        $this->typeRepo = $typeRepo;
        $this->itemRepo = new ItemRepository($db);
        $this->db = $db;
    }

    public function createClipboard(array $data, int $userId): int
    {
        $allowedTypes = $data['allowed_resource_types'] ?? [];

        if (!is_array($allowedTypes)) {
            throw new Exception("allowed_resource_types must be an array");
        }

        $this->db->beginTransaction();

        try {
            $existingIds = $this->typeRepo->existsMany($allowedTypes);

            if (count($existingIds) !== count($allowedTypes)) {
                throw new Exception("Invalid resource type(s) detected");
            }

            $clipboardId = $this->clipRepo->create([
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
                'owner_id' => $userId,
                'visibility' => $data['visibility'] ?? 'private',
                'mode' => $data['mode'] ?? 'multi'
            ]);

            if (!empty($allowedTypes)) {
                $this->clipRepo->attachResourceTypes($clipboardId, $allowedTypes);
            }

            $this->db->commit();

            return $clipboardId;

        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAllResourceTypes(): array
    {
        return $this->typeRepo->getAllTypes();
    }

    public function getAllClipboards(): array
    {
        return $this->clipRepo->getAll();
    }

    public function countClipboards(): int
    {
        return $this->clipRepo->countAll();
    }

    public function countClipboardsForUser(?int $userId): int
    {
        return $this->clipRepo->countForUser($userId);
    }

    public function getClipboardsPage(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->clipRepo->getPage($perPage, $offset);
    }

    public function getClipboardsPageForUser(?int $userId, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->clipRepo->getPageForUser($userId, $perPage, $offset);
    }

    public function getClipboardById(int $id): ?array
    {
        return $this->clipRepo->getById($id);
    }

    public function getItemById(int $id): ?array
    {
        return $this->itemRepo->getById($id);
    }

    public function getClipboardItems(int $clipboardId, int $limit = 50): array
    {
        $items = $this->itemRepo->getByClipboardId($clipboardId, $limit);

        return array_map(function ($item) {
            $content = $item['content'] ?? '';
            $displayType = $this->detectType($content);

            $fileExt = '';
            if (!empty($item['file_path'])) {
                $content = '/file?id=' . $item['id'];
                $fileExt = strtolower(pathinfo($item['file_path'], PATHINFO_EXTENSION));
                $displayType = in_array($fileExt, ['jpg','jpeg','png','gif','webp','svg','bmp']) ? 'image' : 'file';
            }

            return [
                'id' => (int) $item['id'],
                'content' => $content,
                'display_type' => $displayType,
                'type_label' => $this->typeLabel($displayType, $fileExt),
                'user_name' => htmlspecialchars($item['user_name'] ?? ''),
                'created_at' => $item['created_at'],
                'time_ago' => $this->timeAgo($item['created_at']),
            ];
        }, $items);
    }

    public function createItem(array $data, int $userId): int
    {
        $content = trim($data['content'] ?? '');
        if ($content === '') {
            throw new Exception('Content is required');
        }

        $clipboardId = (int) $data['clipboard_id'];
        $clipboard = $this->clipRepo->getById($clipboardId);
        if (!$clipboard) {
            throw new Exception('Clipboard not found');
        }

        if (($clipboard['mode'] ?? 'multi') === 'single') {
            $oldFiles = $this->itemRepo->getFilePathsByClipboardId($clipboardId);
            foreach ($oldFiles as $old) {
                $oldPath = UPLOADS_DIR . '/' . basename($old['file_path']);
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }
            $this->itemRepo->deleteByClipboardId($clipboardId);
        }

        $type = $data['type'] ?? $this->detectType($content);
        $resourceType = $this->itemRepo->getResourceTypeByCategory($type);
        if (!$resourceType) {
            $resourceType = $this->itemRepo->getResourceTypeByCategory('text');
        }
        if (!$resourceType) {
            throw new Exception('No resource types configured in database');
        }

        $title = substr($content, 0, 80);
        $storedContent = $content;

        $itemId = $this->itemRepo->create([
            'clipboard_id' => $clipboardId,
            'user_id' => $userId,
            'resource_type_id' => (int) $resourceType['id'],
            'title' => $title,
            'content' => $storedContent,
        ]);

        if (str_starts_with($content, 'data:')) {
            $savedPath = $this->saveDataUriToDisk($content, $itemId);
            if ($savedPath) {
                $this->itemRepo->updateFilePath($itemId, $savedPath);
                $this->itemRepo->updateContent($itemId, 'Качено изображение');
            }
        }

        return $itemId;
    }

    private function saveDataUriToDisk(string $dataUri, int $itemId): ?string
    {
        if (!preg_match('/^data:([^;]+);base64,(.+)$/', $dataUri, $m)) {
            return null;
        }

        $mime = $m[1];
        $data = base64_decode($m[2], true);
        if ($data === false) {
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
            'text/plain' => 'txt',
            'text/html' => 'html',
            'application/json' => 'json',
            'application/xml' => 'xml',
            'application/pdf' => 'pdf',
            'application/zip' => 'zip',
            default => 'bin',
        };

        $filename = $itemId . '.' . $ext;
        $path = UPLOADS_DIR . '/' . $filename;

        if (!is_dir(UPLOADS_DIR)) {
            mkdir(UPLOADS_DIR, 0755, true);
        }

        if (file_put_contents($path, $data) === false) {
            return null;
        }

        return 'uploads/' . $filename;
    }

    public function getTodayTraffic(): array
    {
        $raw = $this->itemRepo->getTodayTraffic();
        $bytes = (int) ($raw['total_bytes'] ?? 0);
        $count = (int) ($raw['count'] ?? 0);

        if ($bytes >= 1073741824) {
            $display = round($bytes / 1073741824, 1) . ' GB';
        } elseif ($bytes >= 1048576) {
            $display = round($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            $display = round($bytes / 1024, 1) . ' KB';
        } else {
            $display = $bytes . ' B';
        }

        $dailyTarget = 10 * 1048576; // 10 MB daily target
        $percent = min(100, round(($bytes / $dailyTarget) * 100));

        return [
            'display' => $display,
            'percent' => $percent,
            'count' => $count,
        ];
    }

    public function deleteItem(int $itemId, int $userId): void
    {
        $item = $this->itemRepo->getById($itemId);
        if (!$item) {
            throw new Exception('Item not found');
        }

        $clipboard = $this->clipRepo->getById((int) $item['clipboard_id']);
        if (!$clipboard) {
            throw new Exception('Clipboard not found');
        }

        if ((int) $clipboard['owner_id'] !== $userId) {
            throw new Exception('Not authorized to delete items in this clipboard');
        }

        if (!empty($item['file_path'])) {
            $oldPath = UPLOADS_DIR . '/' . basename($item['file_path']);
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        $this->itemRepo->deleteById($itemId);
    }

    public function getAllowedResourceTypesForClipboard(int $clipboardId): array
    {
        return $this->clipRepo->getAllowedResourceTypes($clipboardId);
    }

    private function detectType(string $content): string
    {
        $trimmed = trim($content);

        if (preg_match('/^https?:\/\/.+\.(jpg|jpeg|png|gif|webp|svg|bmp)(\?.*)?$/i', $trimmed) || str_starts_with($trimmed, 'data:image/')) {
            return 'image';
        }

        if (str_starts_with($trimmed, 'data:')) {
            if (preg_match('/^data:([^;]+);base64,/', $trimmed, $m)) {
                $mime = strtolower($m[1]);
                if (preg_match('/^(text\/|application\/(json|xml|javascript|x-javascript|x-php|x-shellscript|x-sh|x-python|x-java-source|x-csrc|x-c\+\+src))/i', $mime)) {
                    return 'code';
                }
            }

            if (preg_match('/^data:.*;base64,(.*)$/', $trimmed, $m)) {
                $decoded = base64_decode($m[1], true);
                if ($decoded !== false && preg_match('/\b(function|class|if|else|for|while|foreach|switch|case|return|echo|print|require|include)\b|[{};]|<\?php|^(import|export|const|let|var|def|lambda)\s/i', $decoded)) {
                    return 'code';
                }
            }

            return 'file';
        }

        if (preg_match('/^https?:\/\//i', $trimmed)) {
            return 'link';
        }

        if (preg_match('/\b(function|class|if|else|for|while|foreach|switch|case|return|echo|print|require|include)\s*\(/i', $trimmed) ||
            preg_match('/[{};]/', $trimmed) ||
            preg_match('/<\?php/', $trimmed) ||
            preg_match('/^(import|export|const|let|var|def|lambda)\s/i', $trimmed)) {
            return 'code';
        }

        return 'text';
    }

    private function typeLabel(string $type, string $ext = ''): string
    {
        if ($type === 'file') {
            $labels = [
                'pdf' => 'PDF',
                'zip' => 'ZIP',
                'rar' => 'RAR',
                'tar' => 'TAR',
                'json' => 'JSON',
                'xml' => 'XML',
                'csv' => 'CSV',
                'txt' => 'TXT',
                'doc' => 'DOC',
                'docx' => 'DOCX',
                'yaml' => 'YAML',
                'md' => 'MD',
            ];
            return $labels[$ext] ?? strtoupper($ext);
        }

        return match ($type) {
            'text' => 'ТЕКСТ',
            'link' => 'ЛИНК',
            'code' => 'КОД',
            'image' => 'ИЗОБРАЖЕНИЕ',
            default => strtoupper($type),
        };
    }

    private function timeAgo(string $timestamp): string
    {
        $time = strtotime($timestamp);
        $diff = time() - $time;

        if ($diff < 60) return 'току-що';
        if ($diff < 3600) return floor($diff / 60) . ' мин.';
        if ($diff < 86400) return floor($diff / 3600) . ' ч.';
        return date('j M', $time);
    }
}
