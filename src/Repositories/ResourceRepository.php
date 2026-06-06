<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class ResourceTypeRepository extends BaseRepository
{
    public function existsMany(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->query(
            "SELECT id FROM resource_types WHERE id IN ($placeholders)",
            $ids
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAllTypes(): array
    {
        $stmt = $this->query(
            'SELECT id, name FROM resource_types ORDER BY id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}