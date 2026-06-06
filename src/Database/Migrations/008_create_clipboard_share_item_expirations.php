<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboard_share_item_expirations (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          clipboard_share_item_id BIGINT NOT NULL,

          expires_at DATETIME NULL,

          max_views INT UNSIGNED NULL,

          one_time BOOLEAN DEFAULT FALSE,

          view_count INT UNSIGNED DEFAULT 0,

          is_expired BOOLEAN DEFAULT FALSE,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          UNIQUE KEY unique_item_expiration (clipboard_share_item_id),

          INDEX (expires_at),
          INDEX (is_expired),

          CONSTRAINT fk_expiration_item
            FOREIGN KEY (clipboard_share_item_id)
            REFERENCES clipboard_share_items(id)
            ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );
};