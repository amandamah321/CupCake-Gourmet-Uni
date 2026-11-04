<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class ItemPedido
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    //Listar todos os produtos de um carrinho
    public function todosPorPedido(int $idPedido): array
    {
        $sql = '
            SELECT
                ip.id_item_pedido,
                ip.id_pedido,
                ip.id_produto,
                ip.nome_produto_snapshot,
                ip.quantidade,
                ip.preco_unitario,
                (ip.quantidade * ip.preco_unitario) AS subtotal
            FROM item_pedido ip
            WHERE ip.id_pedido = :id_pedido
            ORDER BY ip.id_item_pedido
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_pedido' => $idPedido]);

        return $stmt->fetchAll();
    }

    //buscar um produto especifico
    public function buscarPorId(int $id): ?array
    {
        $sql = '
            SELECT
                id_item_pedido,
                id_pedido,
                id_produto,
                nome_produto_snapshot,
                quantidade,
                preco_unitario
            FROM item_pedido
            WHERE id_item_pedido = :id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    //Registrar um profuto a um pedido
    public function adicionar(
        int $idPedido,
        int $idProduto,
        string $nomeProduto,
        int $quantidade,
        float $precoUnitario
    ): int {
        $sql = '
            INSERT INTO item_pedido (
                id_pedido,
                id_produto,
                nome_produto_snapshot,
                quantidade,
                preco_unitario
            )
            VALUES (
                :id_pedido,
                :id_produto,
                :nome_produto_snapshot,
                :quantidade,
                :preco_unitario
            )
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id_pedido' => $idPedido,
            'id_produto' => $idProduto,
            'nome_produto_snapshot' => $nomeProduto,
            'quantidade' => $quantidade,
            'preco_unitario' => $precoUnitario,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
