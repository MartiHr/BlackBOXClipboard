<?php

return function (PDO $pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clipboard_subscriptions (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,

          clipboard_id BIGINT NOT NULL,
          user_id BIGINT NOT NULL,

          notify_email BOOLEAN DEFAULT TRUE,
          notify_inapp BOOLEAN DEFAULT TRUE,

          last_seen_item_id BIGINT NULL,

          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

          UNIQUE KEY unique_subscription (clipboard_id, user_id),

          INDEX (clipboard_id),
          INDEX (user_id),

          CONSTRAINT fk_sub_clipboard
            FOREIGN KEY (clipboard_id)
            REFERENCES clipboards(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_sub_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE,

          CONSTRAINT fk_sub_last_seen_item
            FOREIGN KEY (last_seen_item_id)
            REFERENCES clipboard_share_items(id)
            ON DELETE SET NULL
        ) ENGINE=InnoDB"
    );
};