<?php

declare(strict_types=1);

use App\Services\Database;
use PHPUnit\Framework\TestCase;

final class DatabaseConnectionTest extends TestCase
{
    public function testUsesTheTestDatabase(): void
    {
        $db = Database::getConnection();

        $databaseName = $db->query('SELECT DATABASE()')->fetchColumn();

        $this->assertSame('cupcake_gourmet_test', $databaseName);
    }
}