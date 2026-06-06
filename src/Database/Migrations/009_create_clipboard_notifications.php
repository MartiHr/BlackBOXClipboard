<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboard_notifications (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          user_id BIGINT NOT NULL,
          clipboard_id BIGINT NOT NULL,
          clipboard_share_item_id BIGINT NULL,

          type ENUM(
            'item_created',
            'item_expired'
          ) NOT NULL,

          message TEXT NULL,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          INDEX (user_id),
          INDEX (clipboard_id),
          INDEX (type),

          CONSTRAINT fk_notifications_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_notifications_clipboard
            FOREIGN KEY (clipboard_id)
            REFERENCES clipboards(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_notifications_item
            FOREIGN KEY (clipboard_share_item_id)
            REFERENCES clipboard_share_items(id)
            ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );
};