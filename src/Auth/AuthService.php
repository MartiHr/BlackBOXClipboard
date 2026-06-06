<?php

class AuthService
{
    public function __construct(
        private UserRepository $users,
        private LoginAttemptRepository $attempts
    ) {}

    public function register(string $username, string $email, string $password): string
    {
        $username = trim($username);
        $email = trim($email);

        if (strlen($username) < 3) return 'short_username';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'invalid_email';
        }

        if (strlen($password) < 8) return 'short_password';

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            return $this->users->create($username, $email, $hashedPassword)
                ? 'success'
                : 'error';

        } catch (PDOException $e) {
            error_log("Register error: " . $e->getMessage());

            return $e->getCode() == 23000 ? 'duplicate' : 'error';
        }
    }

    public function login(string $username, string $password): string
    {
        $username = trim($username);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        if ($this->isBruteForceLocked($username, $ip)) {
            return 'locked';
        }

        $user = $this->users->findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            $this->attempts->clear($username, $ip);

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            return 'success';
        }

        $this->attempts->increment($username, $ip, time());

        return 'invalid';
    }

    private function isBruteForceLocked(string $username, string $ip): bool
    {
        $row = $this->attempts->find($username, $ip);

        return $row
            && $row['attempts'] > 5
            && (time() - $row['last_attempt']) < 600;
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
        if (($_SESSION['role'] ?? null) !== 'admin') {
            http_response_code(403);
            exit("Forbidden");
        }
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}