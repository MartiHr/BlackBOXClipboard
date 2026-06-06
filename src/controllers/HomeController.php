<?php
class HomeController
{
    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function index()
    {
        $view = __DIR__ . '/../views/home.php';
        if (file_exists($view)) {
            include $view;
            return;
        }

        // Fallback simple HTML if no view file exists
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Home</title></head><body>';
        echo '<h1>Welcome</h1>';
        echo '<p>This is the Home page.</p>';
        echo '</body></html>';
    }
}
