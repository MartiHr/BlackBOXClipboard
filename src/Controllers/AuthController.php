<?php

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

    public function login(): void 
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($username, $password)) {
                $this->redirect('home');
            } else {
                $error = 'Невалидно потребителско име или парола!';
            }
        }

        $this->render('auth/login', ['error' => $error], 'Вход');
    }

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

        $this->render('auth/register', ['message' => $message], 'Регистрация');
    }
}
