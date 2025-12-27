<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RoleModel extends Model
{
    protected string $table = 'roles';

    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY nome ASC")
            ->fetchAll();
    }

    public function find(int $id): object|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(string $nome, ?string $descricao = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (nome, descricao)
            VALUES (:nome, :descricao)
        ");

        return $stmt->execute([
            ':nome'      => $nome,
            ':descricao' => $descricao
        ]);
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET ativo = 0
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }
}
