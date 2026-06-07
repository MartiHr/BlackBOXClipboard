<?php

if (PHP_SAPI !== 'cli') {
    die("This script can only be run from the CLI.\n");
}

$action = $argv[1] ?? null;
$username = $argv[2] ?? null;

if (!$action || !$username || !in_array($action, ['promote', 'demote'], true)) {
    echo "Usage: php promote.php <promote|demote> <username>\n";
    echo "  promote <username>  - Promotes a user to admin role.\n";
    echo "  demote  <username>  - Demotes an admin to regular user.\n";
    exit(1);
}

$username = trim($username);
$pdo = require __DIR__ . '/config/db.php';

$stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE username = :username');
$stmt->execute([':username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User '$username' not found.\n";
    exit(1);
}

if ($action === 'promote') {
    if ($user['role'] === 'admin') {
        echo "User '$username' is already an admin.\n";
        exit(0);
    }
    $stmt = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
    $stmt->execute([':role' => 'admin', ':id' => $user['id']]);
    echo "User '$username' has been promoted to admin.\n";
} else {
    if ($user['role'] !== 'admin') {
        echo "User '$username' is not an admin.\n";
        exit(0);
    }
    $stmt = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
    $stmt->execute([':role' => 'user', ':id' => $user['id']]);
    echo "User '$username' has been demoted to regular user.\n";
}
