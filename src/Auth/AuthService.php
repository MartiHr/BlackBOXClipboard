<?php
// src/Auth/AuthService.php

class AuthService 
{
    private PDO $db;

    public function __construct(PDO $db) 
    {
        $this->db = $db;
    }

    /**
     * Регистрация на нов потребител със защитена парола
     */
    public function register(string $username, string $password): bool 
    {
        // Хешираме паролата с индустриалния стандарт BCrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
            return $stmt->execute([
                ':username' => trim($username),
                ':password_hash' => $hashedPassword
            ]);
        } catch (PDOException $e) {
            // Ако потребителят вече съществува (Unique constraint)
            return false;
        }
    }

    /**
     * Проверка на парола и влизане в системата
     */
    public function login(string $username, string $password): bool 
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => trim($username)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Записваме потребителя в сесията
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // 'user' или 'admin'
            return true;
        }

        return false;
    }

    /**
     * Проверка дали текущият посетител е логнат
     */
    public static function isLoggedIn(): bool 
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Изход от системата
     */
    public function logout(): void 
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        session_destroy();
    }
}