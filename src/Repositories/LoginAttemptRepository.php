<?php

class LoginAttemptRepository
{
    public function __construct(private PDO $db) {}

    public function find(string $username, string $ip): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT attempts, last_attempt 
             FROM login_attempts 
             WHERE username = :username AND ip = :ip"
        );

        $stmt->execute([
            ':username' => $username,
            ':ip' => $ip
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function increment(string $username, string $ip, int $time): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (username, ip, attempts, last_attempt)
            VALUES (:username, :ip, 1, :time)
            ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1,
                last_attempt = :time2
        ");

        $stmt->execute([
            ':username' => $username,
            ':ip' => $ip,
            ':time' => $time,
            ':time2' => $time
        ]);
    }

    public function clear(string $username, string $ip): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM login_attempts 
            WHERE username = :username AND ip = :ip
        ");

        $stmt->execute([
            ':username' => $username,
            ':ip' => $ip
        ]);
    }
}