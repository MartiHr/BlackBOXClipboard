<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class ExpirationRepository extends BaseRepository
{
    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO clipboard_share_item_expirations
             (clipboard_share_item_id, expires_at, max_views, one_time)
             VALUES
             (:clipboard_share_item_id, :expires_at, :max_views, :one_time)',
            $data
        );
        return (int) $this->db->lastInsertId();
    }

    public function getByItemId(int $itemId): ?array
    {
        $stmt = $this->query(
            'SELECT * FROM clipboard_share_item_expirations
             WHERE clipboard_share_item_id = :item_id
             LIMIT 1',
            [':item_id' => $itemId]
        );
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getExpired(): array
    {
        $stmt = $this->query(
            "SELECT e.id, e.clipboard_share_item_id, e.expires_at,
                    csi.clipboard_id
             FROM clipboard_share_item_expirations e
             JOIN clipboard_share_items csi ON e.clipboard_share_item_id = csi.id
             WHERE e.is_expired = 0
               AND e.expires_at IS NOT NULL
               AND e.expires_at <= NOW()"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markExpired(int $id): void
    {
        $this->query(
            'UPDATE clipboard_share_item_expirations SET is_expired = 1 WHERE id = :id',
            [':id' => $id]
        );
    }

    public function deleteByItemId(int $itemId): void
    {
        $this->query(
            'DELETE FROM clipboard_share_item_expirations WHERE clipboard_share_item_id = :item_id',
            [':item_id' => $itemId]
        );
    }

    public function incrementViewCount(int $itemId): void
    {
        $this->query(
            'UPDATE clipboard_share_item_expirations
             SET view_count = view_count + 1
             WHERE clipboard_share_item_id = :item_id',
            [':item_id' => $itemId]
        );
    }

    public function getMaxViewed(): array
    {
        $stmt = $this->query(
            "SELECT e.id, e.clipboard_share_item_id, e.max_views, e.view_count,
                    csi.clipboard_id
             FROM clipboard_share_item_expirations e
             JOIN clipboard_share_items csi ON e.clipboard_share_item_id = csi.id
             WHERE e.is_expired = 0
               AND e.max_views IS NOT NULL
               AND e.view_count >= e.max_views"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOneTimeConsumed(): array
    {
        $stmt = $this->query(
            "SELECT e.id, e.clipboard_share_item_id,
                    csi.clipboard_id
             FROM clipboard_share_item_expirations e
             JOIN clipboard_share_items csi ON e.clipboard_share_item_id = csi.id
             WHERE e.is_expired = 0
               AND e.one_time = 1
               AND e.view_count >= 1"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
