<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

return [
    'db' => [
        'host' => $_ENV['DB_TEST_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_TEST_PORT'] ?? '3306',
        'name' => $_ENV['DB_TEST_NAME'] ?? 'cupcake_gourmet_test',
        'user' => $_ENV['DB_TEST_USER'] ?? 'root',
        'pass' => $_ENV['DB_TEST_PASS'] ?? '',
        'charset' => 'utf8mb4',
    ],
];