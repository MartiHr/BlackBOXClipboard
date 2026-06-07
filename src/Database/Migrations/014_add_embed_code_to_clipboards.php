<?php

return function (PDO $pdo) {
    $columnCheck = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'clipboards'
           AND COLUMN_NAME = 'embed_code'"
    );
    $columnCheck->execute();

    if ((int) $columnCheck->fetchColumn() === 0) {
        $pdo->exec(
            "ALTER TABLE clipboards
              ADD COLUMN embed_code TEXT NULL AFTER mode"
        );
    }

    $stmt = $pdo->query('SELECT id FROM clipboards');
    $update = $pdo->prepare(
        'UPDATE clipboards SET embed_code = :embed_code WHERE id = :id'
    );

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $embedCode = '<iframe src="/clipboard/show?id=' . $row['id'] . '" width="800" height="550" style="border:0; width:100%; max-width:100%;" loading="lazy"></iframe>';
        $update->execute([
            ':embed_code' => $embedCode,
            ':id' => $row['id'],
        ]);
    }
};
