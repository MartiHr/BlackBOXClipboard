<?php

class Router
{
    private array $routes = [];
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function add(string $page, string $controller, string $action = 'index'): void
    {
        $this->routes[$page] = [$controller, $action];
    }

    public function dispatch(string $page): void
    {
        if (!isset($this->routes[$page])) {
            http_response_code(404);
            echo "<h1>404: Page not found</h1>";
            return;
        }

        [$controller, $action] = $this->routes[$page];
        $file = __DIR__ . "/Controllers/{$controller}.php";

        if (!file_exists($file)) {
            http_response_code(500);
            echo "<h1>500: Controller {$controller} does not exist</h1>";
            return;
        }

        require_once $file;
        $instance = new $controller($this->pdo);
        $instance->$action();
    }
}
