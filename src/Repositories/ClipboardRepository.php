<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class ClipboardRepository extends BaseRepository
{
    private array $columnCache = [];

    public function create(array $data): int
    {
        $columns = ['name', 'description', 'owner_id', 'visibility', 'mode'];
        $placeholders = [':name', ':description', ':owner_id', ':visibility', ':mode'];

        if (isset($data['share_code']) && $this->hasColumn('share_code')) {
            $columns[] = 'share_code';
            $placeholders[] = ':share_code';
        } else {
            unset($data['share_code']);
        }

        $this->query(
            'INSERT INTO clipboards (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')',
            $data
        );

        return (int)$this->db->lastInsertId();
    }

    private function hasColumn(string $column): bool
    {
        if (array_key_exists($column, $this->columnCache)) {
            return $this->columnCache[$column];
        }

        $quotedColumn = $this->db->quote($column);
        $stmt = $this->db->query("SHOW COLUMNS FROM clipboards LIKE $quotedColumn");
        $hasColumn = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        $this->columnCache[$column] = $hasColumn;

        return $hasColumn;
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

    private function buildSearchParams(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $searchVal = '%' . $filters['search'] . '%';
            $conditions[] = '(name LIKE :search1 OR description LIKE :search2)';
            $params[':search1'] = $searchVal;
            $params[':search2'] = $searchVal;
        }

        if (!empty($filters['visibility'])) {
            $conditions[] = 'visibility = :visibility';
            $params[':visibility'] = $filters['visibility'];
        }

        if (!empty($filters['mode'])) {
            $conditions[] = 'mode = :mode';
            $params[':mode'] = $filters['mode'];
        }

        $orderMap = [
            'name' => 'name',
            'created' => 'created_at',
        ];
        $sortField = $orderMap[$filters['sort'] ?? ''] ?? 'id';
        $orderDir = strtoupper($filters['order'] ?? 'DESC');
        if (!in_array($orderDir, ['ASC', 'DESC'], true)) {
            $orderDir = 'DESC';
        }

        return [$conditions, $params, $sortField, $orderDir];
    }

    public function countAll(array $filters = []): int
    {
        [$conditions, $params] = $this->buildSearchParams($filters);
        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $stmt = $this->query("SELECT COUNT(*) FROM clipboards $where", $params);
        return (int) $stmt->fetchColumn();
    }

    public function countForUser(?int $userId, array $filters = []): int
    {
        [$conditions, $params] = $this->buildSearchParams($filters);

        if ($userId) {
            $conditions[] = '(visibility = "public" OR owner_id = :owner_id)';
            $params[':owner_id'] = $userId;
        } else {
            $conditions[] = 'visibility = "public"';
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);
        $stmt = $this->query("SELECT COUNT(*) FROM clipboards $where", $params);
        return (int) $stmt->fetchColumn();
    }

    public function getPage(int $limit, int $offset, array $filters = []): array
    {
        [$conditions, $params, $sortField, $orderDir] = $this->buildSearchParams($filters);
        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->query(
            "SELECT id, name, description, owner_id, visibility, mode, created_at
            FROM clipboards
            $where
            ORDER BY $sortField $orderDir
            LIMIT :limit OFFSET :offset",
            $params
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPageForUser(?int $userId, int $limit, int $offset, array $filters = []): array
    {
        [$conditions, $params, $sortField, $orderDir] = $this->buildSearchParams($filters);

        if ($userId) {
            $conditions[] = '(visibility = "public" OR owner_id = :owner_id)';
            $params[':owner_id'] = $userId;
        } else {
            $conditions[] = 'visibility = "public"';
        }

        $where = 'WHERE ' . implode(' AND ', $conditions);
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->query(
            "SELECT id, name, description, owner_id, visibility, mode, created_at
            FROM clipboards
            $where
            ORDER BY $sortField $orderDir
            LIMIT :limit OFFSET :offset",
            $params
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $columns = ['id', 'name', 'description', 'owner_id', 'visibility', 'mode', 'created_at'];
        if ($this->hasColumn('share_code')) {
            $columns[] = 'share_code';
        }

        $stmt = $this->query(
            'SELECT ' . implode(', ', $columns) . '
            FROM clipboards
            WHERE id = :id',
            [':id' => $id]
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByShareCode(string $shareCode): ?array
    {
        if (!$this->hasColumn('share_code')) {
            return null;
        }

        $columns = ['id', 'name', 'description', 'owner_id', 'visibility', 'mode', 'created_at', 'share_code'];

        $stmt = $this->query(
            'SELECT ' . implode(', ', $columns) . '
             FROM clipboards
             WHERE share_code = :share_code',
            [':share_code' => $shareCode]
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

    public function getAllowedResourceTypes(int $clipboardId): array
    {
        $stmt = $this->query(
            'SELECT rt.id, rt.name, rt.category
            FROM clipboard_allowed_resource_types cart
            JOIN resource_types rt ON cart.resource_type_id = rt.id
            WHERE cart.clipboard_id = :clipboard_id
            ORDER BY rt.name ASC',
            [':clipboard_id' => $clipboardId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): void
    {
        $sets = [];
        $params = [':id' => $id];

        foreach (['name', 'description', 'visibility', 'mode'] as $col) {
            if (array_key_exists($col, $data)) {
                $sets[] = "$col = :$col";
                $params[":$col"] = $data[$col];
            }
        }

        if (!empty($sets)) {
            $this->query(
                'UPDATE clipboards SET ' . implode(', ', $sets) . ' WHERE id = :id',
                $params
            );
        }
    }

    public function clearAllowedResourceTypes(int $clipboardId): void
    {
        $this->query(
            'DELETE FROM clipboard_allowed_resource_types WHERE clipboard_id = :clipboard_id',
            [':clipboard_id' => $clipboardId]
        );
    }

    public function updateShareCode(int $clipboardId, string $shareCode): void
    {
        if (!$this->hasColumn('share_code')) {
            return;
        }

        $this->query(
            'UPDATE clipboards SET share_code = :share_code WHERE id = :id',
            [':share_code' => $shareCode, ':id' => $clipboardId]
        );
    }
}