<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboard_share_items (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          clipboard_id BIGINT NOT NULL,
          user_id BIGINT NOT NULL,

          resource_type_id BIGINT NOT NULL,

          title VARCHAR(255) NULL,

          content LONGTEXT NULL,

          file_path VARCHAR(1000) NULL,

          view_count INT UNSIGNED DEFAULT 0,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          INDEX (clipboard_id),
          INDEX (user_id),
          INDEX (resource_type_id),
          INDEX (created_at),

          CONSTRAINT fk_share_items_clipboard
            FOREIGN KEY (clipboard_id)
            REFERENCES clipboards(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_share_items_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_share_items_resource_type
            FOREIGN KEY (resource_type_id)
            REFERENCES resource_types(id)
            ON DELETE RESTRICT
        ) ENGINE=InnoDB"
    );
};