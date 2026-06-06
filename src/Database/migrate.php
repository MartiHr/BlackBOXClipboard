<?php

$pdo = require __DIR__ . '/../../config/db.php';

require __DIR__ . '/Core/Migrator.php';

$migrator = new Migrator(
    $pdo,
    __DIR__ . '/Migrations'
);

$migrator->run();