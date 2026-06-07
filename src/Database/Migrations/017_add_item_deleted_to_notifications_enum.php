<?php

return function (PDO $pdo) {
    $pdo->exec(
        "ALTER TABLE clipboard_notifications
         MODIFY COLUMN type ENUM(
           'item_created',
           'item_expired',
           'item_deleted'
         ) NOT NULL"
    );

    echo "Added 'item_deleted' to clipboard_notifications type ENUM.\n";
};
