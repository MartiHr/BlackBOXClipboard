<?php

require_once __DIR__ . '/Core/BaseRepository.php';

class SubscriptionRepository extends BaseRepository
{
    public function subscribe(int $clipboardId, int $userId, bool $notifyEmail = true): void
    {
        $this->query(
            'INSERT INTO clipboard_subscriptions (clipboard_id, user_id, notify_email)
             VALUES (:clipboard_id, :user_id, :notify_email)
             ON DUPLICATE KEY UPDATE updated_at = NOW(), notify_email = :notify_email2',
            [
                ':clipboard_id' => $clipboardId,
                ':user_id' => $userId,
                ':notify_email' => $notifyEmail ? 1 : 0,
                ':notify_email2' => $notifyEmail ? 1 : 0,
            ]
        );
    }

    public function unsubscribe(int $clipboardId, int $userId): void
    {
        $this->query(
            'DELETE FROM clipboard_subscriptions
             WHERE clipboard_id = :clipboard_id AND user_id = :user_id',
            [':clipboard_id' => $clipboardId, ':user_id' => $userId]
        );
    }

    public function isSubscribed(int $clipboardId, int $userId): bool
    {
        $stmt = $this->query(
            'SELECT id FROM clipboard_subscriptions
             WHERE clipboard_id = :clipboard_id AND user_id = :user_id
             LIMIT 1',
            [':clipboard_id' => $clipboardId, ':user_id' => $userId]
        );
        return (bool) $stmt->fetch();
    }

    public function getNotifyEmail(int $clipboardId, int $userId): bool
    {
        $stmt = $this->query(
            'SELECT notify_email FROM clipboard_subscriptions
             WHERE clipboard_id = :clipboard_id AND user_id = :user_id
             LIMIT 1',
            [':clipboard_id' => $clipboardId, ':user_id' => $userId]
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (bool) $row['notify_email'] : false;
    }

    public function updateEmailPref(int $clipboardId, int $userId, bool $enabled): void
    {
        $this->query(
            'UPDATE clipboard_subscriptions SET notify_email = :notify_email
             WHERE clipboard_id = :clipboard_id AND user_id = :user_id',
            [
                ':notify_email' => $enabled ? 1 : 0,
                ':clipboard_id' => $clipboardId,
                ':user_id' => $userId,
            ]
        );
    }

    public function getSubscribers(int $clipboardId): array
    {
        $stmt = $this->query(
            'SELECT cs.user_id, cs.notify_email, u.email
             FROM clipboard_subscriptions cs
             JOIN users u ON cs.user_id = u.id
             WHERE cs.clipboard_id = :clipboard_id',
            [':clipboard_id' => $clipboardId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->query(
            'SELECT cs.*, c.name as clipboard_name
             FROM clipboard_subscriptions cs
             JOIN clipboards c ON cs.clipboard_id = c.id
             WHERE cs.user_id = :user_id
             ORDER BY cs.created_at DESC',
            [':user_id' => $userId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
