<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class ItemCarrinho
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    //listar os produtos do carrinho
    public function todosPorCarrinho(int $idCarrinho): array
    {
        $sql = '
            SELECT
                ic.id_item_carrinho,
                ic.id_carrinho,
                ic.id_produto,
                p.nome,
                p.descricao,
                p.imagem_url,
                ic.quantidade,
                ic.preco_unitario,
                (ic.quantidade * ic.preco_unitario) AS subtotal
            FROM item_carrinho ic
            INNER JOIN produto p
                ON p.id_produto = ic.id_produto
            WHERE ic.id_carrinho = :id_carrinho
            ORDER BY ic.id_item_carrinho
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_carrinho' => $idCarrinho]);

        return $stmt->fetchAll();
    }

    //Buscar um item especifico no carrinho
    public function buscarProduto(
        int $idCarrinho,
        int $idProduto
    ): ?array {
        $sql = '
            SELECT
                id_item_carrinho,
                id_carrinho,
                id_produto,
                quantidade,
                preco_unitario
            FROM item_carrinho
            WHERE id_carrinho = :id_carrinho
              AND id_produto = :id_produto
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id_carrinho' => $idCarrinho,
            'id_produto' => $idProduto,
        ]);

        return $stmt->fetch() ?: null;
    }

    //Add profuto no carrinho
    public function adicionar(
        int $idCarrinho,
        int $idProduto,
        int $quantidade,
        float $precoUnitario
    ): int {
        $sql = '
            INSERT INTO item_carrinho (
                id_carrinho,
                id_produto,
                quantidade,
                preco_unitario
            )
            VALUES (
                :id_carrinho,
                :id_produto,
                :quantidade,
                :preco_unitario
            )
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id_carrinho' => $idCarrinho,
            'id_produto' => $idProduto,
            'quantidade' => $quantidade,
            'preco_unitario' => $precoUnitario,
        ]);

        return (int) $this->db->lastInsertId();
    }

    //Atualizar qtd no carrinho
    public function atualizarQuantidade(
        int $idItem,
        int $quantidade
    ): bool {
        $sql = '
            UPDATE item_carrinho
            SET quantidade = :quantidade
            WHERE id_item_carrinho = :id_item
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'quantidade' => $quantidade,
            'id_item' => $idItem,
        ]);

        return $stmt->rowCount() > 0;
    }

    //Remover item do carrinho
    public function remover(int $idItem): bool
    {
        $sql = '
            DELETE FROM item_carrinho
            WHERE id_item_carrinho = :id_item
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_item' => $idItem]);

        return $stmt->rowCount() > 0;
    }

    public function quantidadeTotalPorCarrinho(int $idCarrinho): int
    {
        $sql = '
        SELECT COALESCE(SUM(quantidade), 0)
        FROM item_carrinho
        WHERE id_carrinho = :id_carrinho
    ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_carrinho' => $idCarrinho]);

        return (int) $stmt->fetchColumn();
    }
}
