<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Carrinho
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    //Buscar um carrinho pelo id
    public function buscarPorId(int $id): ?array
    {
        $sql = '
            SELECT id_carrinho, id_cliente, criado_em, atualizado_em
            FROM carrinho
            WHERE id_carrinho = :id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    //Buscar um carrinho associado a um cliete
    public function buscarPorCliente(int $idCliente): ?array
    {
        $sql = '
            SELECT id_carrinho, id_cliente, criado_em, atualizado_em
            FROM carrinho
            WHERE id_cliente = :id_cliente
            ORDER BY atualizado_em DESC
            LIMIT 1
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_cliente' => $idCliente]);

        return $stmt->fetch() ?: null;
    }

    //Criar  um carrinho para um cliente ou visitante
    public function criar(?int $idCliente = null): int
    {
        $sql = '
            INSERT INTO carrinho (id_cliente)
            VALUES (:id_cliente)
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_cliente' => $idCliente]);

        return (int) $this->db->lastInsertId();
    }

    //atualizar a data de modificação do carrinho
    public function atualizar(int $id): bool
    {
        $sql = '
            UPDATE carrinho
            SET atualizado_em = CURRENT_TIMESTAMP
            WHERE id_carrinho = :id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
