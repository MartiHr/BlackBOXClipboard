<?php

class Migrator
{
    private PDO $pdo;
    private string $path;

    public function __construct(PDO $pdo, string $path)
    {
        $this->pdo = $pdo;
        $this->path = $path;

        $this->createMigrationsTable();
    }

    private function createMigrationsTable()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function run()
    {
        $files = scandir($this->path);

        sort($files);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $this->runFile($file);
        }
    }

    private function runFile(string $file)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM migrations WHERE name = ?");
        $stmt->execute([$file]);

        if ($stmt->fetch()) {
            return; // already executed
        }

        $migration = require $this->path . '/' . $file;

        if (is_callable($migration)) {
            $migration($this->pdo);
        }

        $stmt = $this->pdo->prepare("INSERT INTO migrations (name) VALUES (?)");
        $stmt->execute([$file]);

        echo "Migrated: $file\n";
    }
}