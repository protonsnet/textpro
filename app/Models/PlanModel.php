<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class PlanModel extends Model
{
    protected string $table = 'plans';

    /**
     * Todos os planos ativos (cliente)
     */
    public function allActive(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} WHERE status = 'ativo' ORDER BY preco ASC")
            ->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Todos os planos (admin)
     */
    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY preco ASC")
            ->fetchAll(PDO::FETCH_OBJ);
    }

    public function find(int $id): object|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * CREATE (ADMIN)
     */
    public function create(array $data): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (nome, descricao, preco, duracao_meses, stripe_price_id, recursos, status, limite_documentos)
            VALUES
            (:nome, :descricao, :preco, :duracao_meses, :stripe_price_id, :recursos, :status, :limite_documentos)
        ";

        return $this->db->prepare($sql)->execute([
            ':nome'              => $data['nome'],
            ':descricao'         => $data['descricao'] ?? null,
            ':preco'             => $data['preco'],
            ':duracao_meses'     => $data['duracao_meses'] ?? 1,
            ':stripe_price_id'   => $data['stripe_price_id'] ?? null,
            ':recursos'          => json_encode($data['recursos'] ?? [], JSON_UNESCAPED_UNICODE),
            ':status'            => $data['status'] ?? 'ativo',
            ':limite_documentos' => $data['limite_documentos'] ?? null,
        ]);
    }

    /**
     * UPDATE (ADMIN)
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE {$this->table}
            SET
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                duracao_meses = :duracao_meses,
                stripe_price_id = :stripe_price_id,
                recursos = :recursos,
                status = :status,
                limite_documentos = :limite_documentos
            WHERE id = :id
        ";

        $data['id'] = $id;

        return $this->db->prepare($sql)->execute($data);
    }

    public function deactivate(int $id): bool
    {
        return $this->db
            ->prepare("UPDATE {$this->table} SET status = 'inativo' WHERE id = :id")
            ->execute(['id' => $id]);
    }

    public function countActive(): int
    {
        return (int)$this->db
            ->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'ativo'")
            ->fetchColumn();
    }

    public function findAll(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY preco ASC")
            ->fetchAll(PDO::FETCH_OBJ);
    }

    public function insert(array $data): bool
    {
        $sql = "
            INSERT INTO {$this->table}
            (nome, descricao, preco, duracao_meses, stripe_price_id, recursos, status, limite_documentos)
            VALUES
            (:nome, :descricao, :preco, :duracao_meses, :stripe_price_id, :recursos, :status, :limite_documentos)
        ";

        return $this->db->prepare($sql)->execute($data);
    }
}
