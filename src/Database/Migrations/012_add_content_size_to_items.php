<?php

return function (PDO $pdo) {
    $pdo->exec(
        "ALTER TABLE clipboard_share_items
         ADD COLUMN content_size INT UNSIGNED NOT NULL DEFAULT 0 AFTER content"
    );

    $pdo->exec(
        "UPDATE clipboard_share_items SET content_size = LENGTH(content) WHERE content IS NOT NULL"
    );
};
