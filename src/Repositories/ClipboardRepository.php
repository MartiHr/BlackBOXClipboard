<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class ClipboardRepository extends BaseRepository
{
    public function create(array $data): int
    {
        $this->query(
            "INSERT INTO clipboards
            (name, description, owner_id, visibility, mode)
            VALUES
            (:name, :description, :owner_id, :visibility, :mode)",
            $data
        );

        return (int)$this->db->lastInsertId();
    }

    public function attachResourceTypes(int $clipboardId, array $typeIds): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO clipboard_allowed_resource_types
            (clipboard_id, resource_type_id)
            VALUES (:clipboard_id, :resource_type_id)"
        );

        foreach ($typeIds as $id) {
            $stmt->execute([
                ':clipboard_id' => $clipboardId,
                ':resource_type_id' => $id
            ]);
        }
    }

    public function getAll(): array
    {
        $stmt = $this->query(
            'SELECT id, name, description, owner_id, visibility, mode, created_at
            FROM clipboards
            ORDER BY id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll(): int
    {
        $stmt = $this->query('SELECT COUNT(*) FROM clipboards');
        return (int) $stmt->fetchColumn();
    }

    public function countForUser(?int $userId): int
    {
        if ($userId) {
            $stmt = $this->query(
                'SELECT COUNT(*) FROM clipboards WHERE visibility = "public" OR owner_id = :owner_id',
                [':owner_id' => $userId]
            );
        } else {
            $stmt = $this->query('SELECT COUNT(*) FROM clipboards WHERE visibility = "public"');
        }
        return (int) $stmt->fetchColumn();
    }

    public function getPage(int $limit, int $offset): array
    {
        $stmt = $this->query(
            'SELECT id, name, description, owner_id, visibility, mode, created_at
            FROM clipboards
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset',
            [
                ':limit' => $limit,
                ':offset' => $offset,
            ]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPageForUser(?int $userId, int $limit, int $offset): array
    {
        if ($userId) {
            $stmt = $this->query(
                'SELECT id, name, description, owner_id, visibility, mode, created_at
                FROM clipboards
                WHERE visibility = "public" OR owner_id = :owner_id
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset',
                [
                    ':owner_id' => $userId,
                    ':limit' => $limit,
                    ':offset' => $offset,
                ]
            );
        } else {
            $stmt = $this->query(
                'SELECT id, name, description, owner_id, visibility, mode, created_at
                FROM clipboards
                WHERE visibility = "public"
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset',
                [
                    ':limit' => $limit,
                    ':offset' => $offset,
                ]
            );
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->query(
            'SELECT id, name, description, owner_id, visibility, mode, created_at
            FROM clipboards
            WHERE id = :id',
            [':id' => $id]
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getItemsByClipboardId(int $clipboardId, int $limit = 10): array
    {
        $stmt = $this->query(
            'SELECT csi.id, csi.title, csi.content, rt.name as type, csi.created_at
            FROM clipboard_share_items csi
            LEFT JOIN resource_types rt ON csi.resource_type_id = rt.id
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
}