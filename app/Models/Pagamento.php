<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Pagamento
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    //Registrar o resultado do pagamento (nao armazena dados do cartao)
    public function registrar(
        int $idPedido,
        string $metodo,
        string $status,
        string $referencia
    ): void {
        $sql = '
            INSERT INTO pagamento (
                id_pedido,
                metodo,
                status,
                referencia_transacao
            )
            VALUES (
                :id_pedido,
                :metodo,
                :status,
                :referencia
            )
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id_pedido' => $idPedido,
            'metodo' => $metodo,
            'status' => $status,
            'referencia' => $referencia,
        ]);
    }

    //Buscar um pagamento de um pedido
    public function porPedido(int $idPedido): ?array
    {
        $sql = '
            SELECT
                id_pagamento,
                id_pedido,
                metodo,
                status,
                referencia_transacao,
                criado_em
            FROM pagamento
            WHERE id_pedido = :id_pedido
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_pedido' => $idPedido]);

        return $stmt->fetch() ?: null;
    }
}
