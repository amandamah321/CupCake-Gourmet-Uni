<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Categoria
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function todas(): array
    {
        return $this->db
            ->query(
                'SELECT id_categoria, nome
                 FROM categoria
                 ORDER BY id_categoria'
            )
            ->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id_categoria, nome
             FROM categoria
             WHERE id_categoria = :id'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
}

