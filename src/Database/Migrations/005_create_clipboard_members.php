<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboard_members (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          clipboard_id BIGINT NOT NULL,
          user_id BIGINT NOT NULL,

          role ENUM('owner','editor','viewer') DEFAULT 'viewer',

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          UNIQUE KEY unique_membership (clipboard_id, user_id),

          INDEX (clipboard_id),
          INDEX (user_id),

          CONSTRAINT fk_members_clipboard
            FOREIGN KEY (clipboard_id)
            REFERENCES clipboards(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_members_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );
};