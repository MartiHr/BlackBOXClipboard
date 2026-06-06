<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboards (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          name VARCHAR(255) NOT NULL,
          description TEXT NULL,

          owner_id BIGINT NOT NULL,

          visibility ENUM('public','private') DEFAULT 'private',
          mode ENUM('single','multi') DEFAULT 'multi',

          allow_guest BOOLEAN DEFAULT FALSE,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          INDEX (owner_id),

          CONSTRAINT fk_clipboards_owner
            FOREIGN KEY (owner_id)
            REFERENCES users(id)
            ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );
};