<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use RuntimeException;

final class DatabaseConnection
{
    public static function getConnection(): PDO
    {
        $config = require dirname(__DIR__) . '/config/database.test.php';
        $db = $config['db'];

        if ($db['name'] !== 'cupcake_gourmet_test') {
            throw new RuntimeException(
                'Os testes devem utilizar exclusivamente o banco de testes.'
            );
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['name'],
            $db['charset']
        );

        return new PDO(
            $dsn,
            $db['user'],
            $db['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
