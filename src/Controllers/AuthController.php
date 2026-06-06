<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../Repositories/LoginAttemptRepository.php';

class AuthController extends Controller 
{
    private AuthService $authService;

    public function __construct(PDO $db) 
    {
        parent::__construct($db);
        $this->authService = new AuthService(new UserRepository($db), new LoginAttemptRepository($db));
    }

    public function login(): void 
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = $this->authService->login($username, $password);

            if ($result === 'success') {
                $this->redirect('home');
            } elseif ($result === 'locked') {
                $error = 'Твърде много опити. Изчакайте 10 минути.';
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

            $result = $this->authService->register($username, $email, $password);

            switch ($result) {
                case 'success':
                    $message = "<p style='color:green;'>Регистрацията е успешна! <a href='/'>Влезте оттук</a></p>";
                    break;
                case 'short_username':
                    $message = "<p style='color:red;'>Потребителското име трябва да е поне 3 символа.</p>";
                    break;
                case 'invalid_email':
                    $message = "<p style='color:red;'>Невалиден имейл адрес.</p>";
                    break;
                case 'short_password':
                    $message = "<p style='color:red;'>Паролата трябва да е поне 8 символа.</p>";
                    break;
                case 'duplicate':
                    $message = "<p style='color:red;'>Потребителското име или имейл вече съществува.</p>";
                    break;
                default:
                    $message = "<p style='color:red;'>Грешка при регистрация. Опитайте отново.</p>";
            }
        }

        $this->render('auth/register', ['message' => $message], 'Регистрация');
    }
}
