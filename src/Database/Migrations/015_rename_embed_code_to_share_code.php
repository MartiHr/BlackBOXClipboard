<?php

return function (PDO $pdo) {
    $checkColumn = $pdo->prepare(
        "SELECT COUNT(*)
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'clipboards'
           AND COLUMN_NAME = :column"
    );

    $checkColumn->execute([':column' => 'share_code']);
    $hasShareCode = (int) $checkColumn->fetchColumn() > 0;

    $checkColumn->execute([':column' => 'embed_code']);
    $hasEmbedCode = (int) $checkColumn->fetchColumn() > 0;

    if (!$hasShareCode) {
        if ($hasEmbedCode) {
            $pdo->exec("ALTER TABLE clipboards CHANGE embed_code share_code TEXT NULL");
        } else {
            $pdo->exec("ALTER TABLE clipboards ADD COLUMN share_code TEXT NULL AFTER mode");
        }
    }

    $select = $pdo->query('SELECT id, share_code FROM clipboards');
    $checkUnique = $pdo->prepare('SELECT COUNT(*) FROM clipboards WHERE share_code = :share_code');
    $update = $pdo->prepare('UPDATE clipboards SET share_code = :share_code WHERE id = :id');

    while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
        $code = trim((string)$row['share_code']);
        if ($code === '' || preg_match('/^\s*<iframe/i', $code)) {
            do {
                $code = '';
                $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                for ($i = 0; $i < 5; $i++) {
                    $code .= $characters[random_int(0, strlen($characters) - 1)];
                }
                $checkUnique->execute([':share_code' => $code]);
                $exists = (int)$checkUnique->fetchColumn() > 0;
            } while ($exists);

            $update->execute([
                ':share_code' => $code,
                ':id' => $row['id'],
            ]);
        }
    }
};
