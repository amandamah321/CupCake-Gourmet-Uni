<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$root = dirname(__DIR__);

// Carregar as dependências do Composer
require_once $root . '/vendor/autoload.php';

// Carregar as vars do arquivo .env
if (is_file($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

// Retornar as config da app
return [
    'root' => $root,

    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'CupCake Gourmet',
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => filter_var(
            $_ENV['APP_DEBUG'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        ),
    ],
];
