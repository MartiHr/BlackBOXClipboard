<?php
// src/Controllers/AuthController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';

class AuthController extends Controller 
{
    private AuthService $authService;

    public function __construct(PDO $db) 
    {
        parent::__construct($db);
        $this->authService = new AuthService($db);
    }

    /**
     * index.php?page=login
     */
    public function login(): void 
    {
        $error = '';

        // Ако формата е изпратена (POST заявка)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? ''; // Тук си взимаш данните от формата
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($username, $password)) {
                $this->redirect('home'); // Пренасочваме към началото при успешен вход
            } else {
                $error = 'Невалидно потребителско име или парола!';
            }
        }

        // Много проста HTML форма за тест
        echo "<h2>Вход в системата</h2>";
        if ($error) echo "<p style='color:red;'>$error</p>";
        echo '
        <form method="POST">
            <input type="text" name="username" placeholder="Потребителско име" required><br><br>
            <input type="password" name="password" placeholder="Парола" required><br><br>
            <button type="submit">Влез</button>
        </form>
        <p>Нямате профил? <a href="index.php?page=register">Регистрирайте се тук</a></p>
        ';
    }

    /**
     * index.php?page=register
     */
    public function register(): void 
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->register($username, $password)) {
                $message = "<p style='color:green;'>Регистрацията е успешна! <a href='index.php?page=login'>Влезте оттук</a></p>";
            } else {
                $message = "<p style='color:red;'>Потребителското име е заето или възникна грешка!</p>";
            }
        }

        echo "<h2>Регистрация</h2>";
        echo $message;
        echo '
        <form method="POST">
            <input type="text" name="username" placeholder="Изберете потребителско име" required><br><br>
            <input type="password" name="password" placeholder="Изберете парола" required><br><br>
            <button type="submit">Регистрирай ме</button>
        </form>
        ';
    }
}