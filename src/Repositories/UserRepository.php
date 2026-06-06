<?php

class UserRepository extends BaseRepository
{
    public function create(string $username, string $email, string $passwordHash): bool
    {
        return $this->query(
            "INSERT INTO users (username, email, password_hash)
             VALUES (:username, :email, :password_hash)",
            [
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $passwordHash
            ]
        ) !== false;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM users WHERE username = :username LIMIT 1",
            [':username' => $username]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM users WHERE id = :id",
            [':id' => $id]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}