<?php

class AuthService 
{
    private PDO $db;

    public function __construct(PDO $db) 
    {
        $this->db = $db;
    }

    public function register(string $username, string $email, string $password): string
    {
        $username = trim($username);
        $email = trim($email);

        if (strlen($username) < 3) {
            return 'short_username';
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return 'invalid_email';
        }

        if (strlen($password) < 8) {
            return 'short_password';
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $hashedPassword
            ]);
            return 'success';
        } catch (PDOException $e) {
            error_log("Register error: " . $e->getMessage());
            if ($e->getCode() == 23000) {
                return 'duplicate';
            }
            return 'error';
        }
    }

    public function login(string $username, string $password): string
    {
        $username = trim($username);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if ($this->isBruteForceLocked($username, $ip)) {
            return 'locked';
        }

        $stmt = $this->db->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $this->clearLoginAttempts($username, $ip);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return 'success';
        }

        $this->recordLoginAttempt($username, $ip);
        return 'invalid';
    }

    private function isBruteForceLocked(string $username, string $ip): bool
    {
        $stmt = $this->db->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE username = :username AND ip = :ip");
        $stmt->execute([':username' => $username, ':ip' => $ip]);
        $row = $stmt->fetch();

        if ($row && $row['attempts'] > 5 && (time() - $row['last_attempt']) < 600) {
            return true;
        }

        return false;
    }

    private function recordLoginAttempt(string $username, string $ip): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (username, ip, attempts, last_attempt) 
            VALUES (:username, :ip, 1, :now)
            ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = :now2
        ");
        $now = time();
        $stmt->execute([
            ':username' => $username,
            ':ip' => $ip,
            ':now' => $now,
            ':now2' => $now
        ]);
    }

    private function clearLoginAttempts(string $username, string $ip): void
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE username = :username AND ip = :ip");
        $stmt->execute([':username' => $username, ':ip' => $ip]);
    }

    public static function isLoggedIn(): bool 
    {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo "Forbidden";
            exit;
        }
    }

    public function logout(): void 
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
}