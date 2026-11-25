<?php

// Prepare serverless writable directories in /tmp
$dirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Initialize writable SQLite database in /tmp
$targetDb = '/tmp/database.sqlite';
if (!file_exists($targetDb) || filesize($targetDb) === 0) {
    $seedDbs = [
        __DIR__ . '/../database/seed_database.sqlite',
        __DIR__ . '/../database/database.sqlite',
    ];
    $copied = false;
    foreach ($seedDbs as $seed) {
        if (file_exists($seed) && filesize($seed) > 0) {
            @copy($seed, $targetDb);
            $copied = true;
            break;
        }
    }
    if (!$copied) {
        @touch($targetDb);
    }
}

// Forward to Laravel's native public index.php
require __DIR__ . '/../public/index.php';
