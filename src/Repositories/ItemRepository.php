<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class ItemRepository extends BaseRepository
{
    public function create(array $data): int
    {
        $data['content_size'] = strlen($data['content'] ?? '');

        $this->query(
            "INSERT INTO clipboard_share_items
            (clipboard_id, user_id, resource_type_id, title, content, content_size)
            VALUES
            (:clipboard_id, :user_id, :resource_type_id, :title, :content, :content_size)",
            $data
        );

        return (int) $this->db->lastInsertId();
    }

    public function getByClipboardId(int $clipboardId, int $limit = 50): array
    {
        $stmt = $this->query(
            'SELECT csi.id, csi.title, csi.content, rt.name as type,
                    u.username as user_name, csi.created_at
            FROM clipboard_share_items csi
            LEFT JOIN resource_types rt ON csi.resource_type_id = rt.id
            LEFT JOIN users u ON csi.user_id = u.id
            WHERE csi.clipboard_id = :clipboard_id
            ORDER BY csi.created_at DESC
            LIMIT :limit',
            [
                ':clipboard_id' => $clipboardId,
                ':limit' => $limit,
            ]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->query(
            'SELECT csi.id, csi.clipboard_id, csi.content, rt.name as type
            FROM clipboard_share_items csi
            LEFT JOIN resource_types rt ON csi.resource_type_id = rt.id
            WHERE csi.id = :id',
            [':id' => $id]
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function deleteById(int $id): void
    {
        $this->query(
            'DELETE FROM clipboard_share_items WHERE id = :id',
            [':id' => $id]
        );
    }

    public function deleteByClipboardId(int $clipboardId): void
    {
        $this->query(
            'DELETE FROM clipboard_share_items WHERE clipboard_id = :clipboard_id',
            [':clipboard_id' => $clipboardId]
        );
    }

    public function getTodayTraffic(): array
    {
        $stmt = $this->query(
            "SELECT COUNT(*) as count, COALESCE(SUM(content_size), 0) as total_bytes
            FROM clipboard_share_items
            WHERE created_at >= CURDATE()"
        );

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getResourceTypeByCategory(string $category): ?array
    {
        $stmt = $this->query(
            'SELECT id FROM resource_types WHERE category = :category LIMIT 1',
            [':category' => $category]
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
