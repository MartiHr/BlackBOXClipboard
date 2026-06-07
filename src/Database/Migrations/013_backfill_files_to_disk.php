<?php

return function (PDO $pdo) {
    require_once __DIR__ . '/../../../config/storage.php';

    $stmt = $pdo->query(
        "SELECT id, content FROM clipboard_share_items
         WHERE content LIKE 'data:%' AND (file_path IS NULL OR file_path = '')"
    );

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;

    foreach ($items as $item) {
        $content = $item['content'];

        if (!preg_match('/^data:([^;]+);base64,(.+)$/', $content, $m)) {
            continue;
        }

        $data = base64_decode($m[2], true);
        if ($data === false) {
            continue;
        }

        $mime = $m[1];
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
            'text/plain' => 'txt',
            'text/html' => 'html',
            'application/json' => 'json',
            'application/xml' => 'xml',
            'application/pdf' => 'pdf',
            'application/zip' => 'zip',
            default => 'bin',
        };

        $id = (int) $item['id'];
        $filename = $id . '.' . $ext;
        $path = UPLOADS_DIR . '/' . $filename;

        if (!is_dir(UPLOADS_DIR)) {
            mkdir(UPLOADS_DIR, 0755, true);
        }

        if (file_put_contents($path, $data) === false) {
            continue;
        }

        $updateStmt = $pdo->prepare(
            "UPDATE clipboard_share_items SET file_path = :file_path WHERE id = :id"
        );
        $updateStmt->execute([
            ':file_path' => 'uploads/' . $filename,
            ':id' => $id,
        ]);

        $count++;
    }

    echo "Backfilled $count files to disk.\n";
};
