<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Database;
use PDO;

final class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os clientes cadastrados.
     */
    public function todos(): array
    {
        $sql = '
            SELECT id_cliente, nome, email, telefone
            FROM cliente
            ORDER BY nome
        ';

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Busca um cliente pelo ID.
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = '
            SELECT id_cliente, nome, email, telefone
            FROM cliente
            WHERE id_cliente = :id
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Busca um cliente pelo endereço de e-mail.
     */
    public function buscarPorEmail(string $email): ?array
    {
        $sql = '
            SELECT id_cliente, nome, email, telefone
            FROM cliente
            WHERE email = :email
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Cadastra um novo cliente.
     */
    public function criar(
        string $nome,
        string $email,
        ?string $telefone = null
    ): int {
        $sql = '
            INSERT INTO cliente (nome, email, telefone)
            VALUES (:nome, :email, :telefone)
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
        ]);

        return (int) $this->db->lastInsertId();
    }
}
