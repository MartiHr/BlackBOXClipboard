<?php

class ClipboardService
{
    private ClipboardRepository $clipRepo;
    private ResourceTypeRepository $typeRepo;
    private PDO $db;

    public function __construct(
        ClipboardRepository $clipRepo,
        ResourceTypeRepository $typeRepo,
        PDO $db
    ) {
        $this->clipRepo = $clipRepo;
        $this->typeRepo = $typeRepo;
        $this->db = $db;
    }

    public function createClipboard(array $data, int $userId): int
    {
        $allowedTypes = $data['allowed_resource_types'] ?? [];

        if (!is_array($allowedTypes)) {
            throw new Exception("allowed_resource_types must be an array");
        }

        // START TRANSACTION
        $this->db->beginTransaction();

        try {
            // 1. validate resource types via repository
            $existingIds = $this->typeRepo->existsMany($allowedTypes);

            if (count($existingIds) !== count($allowedTypes)) {
                throw new Exception("Invalid resource type(s) detected");
            }

            // 2. create clipboard
            $clipboardId = $this->clipRepo->create([
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
                'owner_id' => $userId,
                'visibility' => $data['visibility'] ?? 'private',
                'mode' => $data['mode'] ?? 'multi'
            ]);

            // 3. attach allowed types
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
}