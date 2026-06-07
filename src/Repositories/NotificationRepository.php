<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class NotificationRepository extends BaseRepository
{
    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO clipboard_notifications
             (user_id, clipboard_id, clipboard_share_item_id, type, message)
             VALUES (:user_id, :clipboard_id, :clipboard_share_item_id, :type, :message)',
            $data
        );
        return (int) $this->db->lastInsertId();
    }

    public function getByUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->query(
            'SELECT n.*, c.name as clipboard_name
             FROM clipboard_notifications n
             JOIN clipboards c ON n.clipboard_id = c.id
             WHERE n.user_id = :user_id
             ORDER BY n.created_at DESC
             LIMIT :limit',
            [':user_id' => $userId, ':limit' => $limit]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->query(
            "SELECT COUNT(*) FROM clipboard_notifications
             WHERE user_id = :user_id AND is_read = 0",
            [':user_id' => $userId]
        );
        return (int) $stmt->fetchColumn();
    }

    public function markAsRead(int $id, int $userId): void
    {
        $this->query(
            'UPDATE clipboard_notifications SET is_read = 1
             WHERE id = :id AND user_id = :user_id',
            [':id' => $id, ':user_id' => $userId]
        );
    }

    public function markAllAsRead(int $userId): void
    {
        $this->query(
            'UPDATE clipboard_notifications SET is_read = 1
             WHERE user_id = :user_id AND is_read = 0',
            [':user_id' => $userId]
        );
    }
}
