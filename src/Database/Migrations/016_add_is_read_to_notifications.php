<?php

return function (PDO $pdo) {
    $pdo->exec(
        "ALTER TABLE clipboard_notifications
         ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER message,
         ADD INDEX (is_read)"
    );
};
