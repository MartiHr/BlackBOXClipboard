<?php
// controllers/HomeController.php

class HomeController
{
    private PDO $db;

    // Конструкторът хваща базата, която Рутерът му подаде при "new HomeController($this->pdo)"
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(): void
    {
        // Базата вече е тук! Можеш да я ползваш за SQL заявки:
        // $stmt = $this->db->query("SELECT * FROM clipboards WHERE visibility = 'public'");
        // $publicClipboards = $stmt->fetchAll();

        // Показваш изгледа на потребителя
        echo "<h1>Добре дошли в BlackBOX Clipboard!</h1>";
        // echo "Публични канали в базата: " . count($publicClipboards);
    }
}