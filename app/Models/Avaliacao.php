<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Avaliacao
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function recentes(int $limite = 3): array
    {
        $sql = '
            SELECT
                a.id_avaliacao,
                a.id_produto,
                a.nome_exibicao,
                a.comentario,
                a.estrelas,
                a.criada_em,
                p.nome AS produto
            FROM avaliacao a
            LEFT JOIN produto p
                ON p.id_produto = a.id_produto
            ORDER BY a.criada_em DESC
            LIMIT :limite
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}