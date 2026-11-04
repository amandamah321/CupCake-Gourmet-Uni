<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Produto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os produtos ativos.
     */
    public function todos(): array
    {
        $sql = '
            SELECT
                p.id_produto,
                p.id_categoria,
                c.nome AS categoria,
                p.nome,
                p.descricao,
                p.preco,
                p.imagem_url
            FROM produto p
            INNER JOIN categoria c
                ON c.id_categoria = p.id_categoria
            WHERE p.ativo = 1
            ORDER BY p.id_produto
        ';

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca um produto pelo seu ID.
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = '
            SELECT
                p.id_produto,
                p.id_categoria,
                c.nome AS categoria,
                p.nome,
                p.descricao,
                p.preco,
                p.imagem_url
            FROM produto p
            INNER JOIN categoria c
                ON c.id_categoria = p.id_categoria
            WHERE p.id_produto = :id
              AND p.ativo = 1
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Retorna produtos de uma categoria.
     */
    public function porCategoria(int $idCategoria): array
    {
        $sql = '
            SELECT
                id_produto,
                id_categoria,
                nome,
                descricao,
                preco,
                imagem_url
            FROM produto
            WHERE id_categoria = :id_categoria
              AND ativo = 1
            ORDER BY id_produto
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_categoria' => $idCategoria]);

        return $stmt->fetchAll();
    }

    /**
     * Pesquisa produtos pelo nome.
     */
    public function buscarPorNome(string $nome): array
    {
        $sql = '
            SELECT
                id_produto,
                id_categoria,
                nome,
                descricao,
                preco,
                imagem_url
            FROM produto
            WHERE nome LIKE :nome
              AND ativo = 1
            ORDER BY nome
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nome' => '%' . $nome . '%']);

        return $stmt->fetchAll();
    }
}
