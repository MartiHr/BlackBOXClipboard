<?php

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
        'ANY' => []
    ];
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function add(string $page, string $controller, string $action = 'index', string $method = 'GET'): void
    {
        $method = strtoupper($method);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$page] = [$controller, $action];
    }

    public function get(string $page, string $controller, string $action = 'index'): void
    {
        $this->add($page, $controller, $action, 'GET');
    }

    public function post(string $page, string $controller, string $action = 'index'): void
    {
        $this->add($page, $controller, $action, 'POST');
    }

    public function put(string $page, string $controller, string $action = 'index'): void
    {
        $this->add($page, $controller, $action, 'PUT');
    }

    public function delete(string $page, string $controller, string $action = 'index'): void
    {
        $this->add($page, $controller, $action, 'DELETE');
    }

    public function any(string $page, string $controller, string $action = 'index'): void
    {
        $this->add($page, $controller, $action, 'ANY');
    }

    public function dispatch(string $page): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        $route = $this->routes[$method][$page] ?? $this->routes['ANY'][$page] ?? null;
        if ($route === null) {
            http_response_code(404);
            echo "<h1>404: Page not found</h1>";
            return;
        }

        [$controller, $action] = $route;
        $file = __DIR__ . "/../src/Controllers/{$controller}.php";

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
