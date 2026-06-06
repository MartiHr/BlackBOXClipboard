<?php

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
}