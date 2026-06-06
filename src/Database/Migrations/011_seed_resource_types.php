<?php

return function (PDO $pdo) {

    $types = [

        // =====================
        // TEXT
        // =====================
        ['Plain Text', 'text/plain', 'text'],

        // =====================
        // CODE
        // =====================
        ['PHP', 'text/php', 'code'],
        ['JavaScript', 'text/javascript', 'code'],
        ['TypeScript', 'text/typescript', 'code'],
        ['HTML', 'text/html', 'code'],
        ['CSS', 'text/css', 'code'],
        ['SQL', 'text/sql', 'code'],
        ['JSON (Code)', 'application/json', 'code'],
        ['XML (Code)', 'application/xml', 'code'],

        // =====================
        // WEB / LINKS
        // =====================
        ['URL Link', 'text/uri-list', 'link'],
        ['HTTP Link', 'text/plain+url', 'link'],

        // =====================
        // IMAGES
        // =====================
        ['JPEG Image', 'image/jpeg', 'image'],
        ['PNG Image', 'image/png', 'image'],
        ['GIF Image', 'image/gif', 'image'],
        ['WEBP Image', 'image/webp', 'image'],
        ['SVG Vector', 'image/svg+xml', 'image'],

        // =====================
        // DATA
        // =====================
        ['JSON Data', 'application/json', 'file'],
        ['XML Data', 'application/xml', 'file'],
        ['YAML', 'application/x-yaml', 'file'],
        ['CSV', 'text/csv', 'file'],

        // =====================
        // DOCUMENTS
        // =====================
        ['PDF Document', 'application/pdf', 'file'],
        ['Markdown', 'text/markdown', 'file'],
        ['Word Document', 'application/msword', 'file'],

        // =====================
        // ARCHIVES
        // =====================
        ['ZIP Archive', 'application/zip', 'file'],
        ['RAR Archive', 'application/x-rar-compressed', 'file'],
        ['TAR Archive', 'application/x-tar', 'file'],
    ];

    $stmt = $pdo->prepare(
        "INSERT INTO resource_types (name, mime_type, category)
         VALUES (:name, :mime_type, :category)"
    );

    foreach ($types as $type) {

        $check = $pdo->prepare(
            "SELECT id FROM resource_types WHERE mime_type = :mime_type"
        );

        $check->execute([':mime_type' => $type[1]]);

        if ($check->fetch()) {
            continue;
        }

        $stmt->execute([
            ':name' => $type[0],
            ':mime_type' => $type[1],
            ':category' => $type[2]
        ]);
    }

    echo "Extended resource types seeded successfully.\n";
};