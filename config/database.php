<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'CupCake Gourmet',

    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'name' => $_ENV['DB_NAME'] ?? 'cupcake_gourmet',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => 'utf8mb4',
    ],
];
