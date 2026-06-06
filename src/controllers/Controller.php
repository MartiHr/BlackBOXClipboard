<?php

abstract class Controller 
{
    protected PDO $db;
    protected string $viewsPath;

    public function __construct(PDO $db) 
    {
        $this->db = $db;
        $this->viewsPath = __DIR__ . '/../views';
    }

    protected function redirect(string $page): void 
    {
        header("Location: index.php?page=" . $page);
        exit;
    }

    protected function render(string $view, array $data = [], string $title = 'BlackBOX Clipboard'): void
    {
        $data['title'] = $title;

        require $this->viewsPath . '/layout/header.php';

        extract($data);
        require $this->viewsPath . '/' . $view . '.php';

        require $this->viewsPath . '/layout/footer.php';
    }
}