<?php

return function (PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            username VARCHAR(255) NOT NULL,
            attempts INT DEFAULT 1,
            last_attempt INT UNSIGNED NOT NULL,
            UNIQUE KEY idx_ip_username (ip, username)
        )
    ");
};
