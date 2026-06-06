<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS resource_types (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          name VARCHAR(100) NOT NULL,

          mime_type VARCHAR(255) NOT NULL,

          category ENUM(
            'text',
            'link',
            'code',
            'image',
            'file'
          ) NOT NULL,

          description VARCHAR(255) NULL,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          UNIQUE KEY unique_mime_type (mime_type),

          INDEX (category)
        ) ENGINE=InnoDB"
    );
};