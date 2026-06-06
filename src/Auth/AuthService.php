<?php

class AuthService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Register user
     */
    public function register(string $username, string $email, string $password): bool
    {
        $username = trim($username);
        $email = trim($email);

        if ($username === '' || $email === '' || $password === '') {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        return $this->users->create($username, $email, $hash);
    }

    /**
     * Login
     */
    public function login(string $username, string $password): bool
    {
        $username = trim($username);

        $user = $this->users->findByUsername($username);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        // session handling
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        return true;
    }

    /**
     * Check login state
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (session_id() !== '') {
            session_destroy();
        }
    }
}