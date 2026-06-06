<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboard_allowed_resource_types (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          clipboard_id BIGINT NOT NULL,
          resource_type_id BIGINT NOT NULL,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

          UNIQUE KEY unique_clipboard_resource_type (clipboard_id, resource_type_id),

          INDEX (clipboard_id),
          INDEX (resource_type_id),

          CONSTRAINT fk_clipboard_allowed_clipboard
            FOREIGN KEY (clipboard_id)
            REFERENCES clipboards(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_clipboard_allowed_resource_type
            FOREIGN KEY (resource_type_id)
            REFERENCES resource_types(id)
            ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );
};