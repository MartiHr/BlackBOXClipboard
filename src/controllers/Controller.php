<?php
// src/Controllers/Controller.php

abstract class Controller 
{
    protected PDO $db;

    public function __construct(PDO $db) 
    {
        $this->db = $db;
    }

    // Спомагателен метод за лесно пренасочване (Redirect)
    protected function redirect(string $page): void 
    {
        header("Location: index.php?page=" . $page);
        exit;
    }
}