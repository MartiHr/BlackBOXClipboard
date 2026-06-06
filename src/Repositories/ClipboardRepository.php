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
}