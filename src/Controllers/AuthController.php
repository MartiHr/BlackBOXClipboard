<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../Repositories/UserRepository.php';

class AuthController extends Controller 
{
    private AuthService $authService;

    public function __construct(PDO $db) 
    {
        parent::__construct($db);
        $this->authService = new AuthService(new UserRepository($db));
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

    public function logout(): void 
    {
        $this->authService->logout();
        $this->redirect('home');
    }

    public function register(): void 
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->register($username, $email, $password)) {
                $message = "<p style='color:green;'>Регистрацията е успешна! <a href='/'>Влезте оттук</a></p>";
            } else {
                $message = "<p style='color:red;'>Потребителското име е заето или възникна грешка!</p>";
            }
        }

        $this->render('auth/register', ['message' => $message], 'Регистрация');
    }
}
