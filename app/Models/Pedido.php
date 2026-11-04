<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Pedido
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lista todos os pedidos.
     */
    public function todos(): array
    {
        $sql = '
            SELECT
                p.id_pedido,
                p.id_cliente,
                c.nome AS nome_cliente,
                p.criado_em,
                p.status,
                p.total,
                p.cep,
                p.comentario
            FROM pedido p
            LEFT JOIN cliente c
                ON c.id_cliente = p.id_cliente
            ORDER BY p.criado_em DESC
        ';

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca um pedido pelo ID.
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = '
            SELECT
                p.id_pedido,
                p.id_cliente,
                c.nome AS nome_cliente,
                p.criado_em,
                p.status,
                p.total,
                p.cep,
                p.comentario
            FROM pedido p
            LEFT JOIN cliente c
                ON c.id_cliente = p.id_cliente
            WHERE p.id_pedido = :id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Busca os pedidos de um cliente.
     */
    public function porCliente(int $idCliente): array
    {
        $sql = '
            SELECT
                id_pedido,
                id_cliente,
                criado_em,
                status,
                total,
                cep,
                comentario
            FROM pedido
            WHERE id_cliente = :id_cliente
            ORDER BY criado_em DESC
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_cliente' => $idCliente]);

        return $stmt->fetchAll();
    }

    /**
     * Registra um novo pedido.
     */
    public function criar(
        ?int $idCliente,
        float $total,
        ?string $cep = null,
        ?string $comentario = null
    ): int {
        $sql = '
            INSERT INTO pedido (
                id_cliente,
                total,
                cep,
                comentario
            )
            VALUES (
                :id_cliente,
                :total,
                :cep,
                :comentario
            )
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id_cliente' => $idCliente,
            'total' => $total,
            'cep' => $cep,
            'comentario' => $comentario,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza o status de um pedido.
     */
    public function atualizarStatus(
        int $idPedido,
        string $status
    ): bool {
        $sql = '
            UPDATE pedido
            SET status = :status
            WHERE id_pedido = :id_pedido
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'status' => $status,
            'id_pedido' => $idPedido,
        ]);

        return $stmt->rowCount() > 0;
    }
}
