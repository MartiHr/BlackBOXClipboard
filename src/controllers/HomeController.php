<?php

require_once __DIR__ . '/Controller.php';

class HomeController extends Controller 
{
    public function index(): void 
    {
        // Вземаме публичните клипбордове за началната страница
        // $stmt = $this->db->query("SELECT * FROM clipboards WHERE visibility = 'public'");
        // $clipboards = $stmt->fetchAll();

        // Тук просто ще "плюем" HTML директно за теста (после може да си направиш гледки/views)
        echo "<h1>Добре дошли в BlackBOX Clipboard</h1>";
        echo "<h3>Налични публични канали:</h3><ul>";
        // foreach ($clipboards as $c) {
        //     echo "<li><a href='index.php?page=clipboard&slug={$c['slug']}'>" . htmlspecialchars($c['name']) . "</a></li>";
        // }
        echo "</ul>";
    }
}