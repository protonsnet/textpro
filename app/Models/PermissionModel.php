<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class PermissionModel extends Model
{
    protected string $table = 'permissions';

    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY chave ASC")
            ->fetchAll();
    }

    public function findByKey(string $key): object|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE chave = :chave
            LIMIT 1
        ");
        $stmt->bindValue(':chave', $key);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(string $chave, ?string $descricao = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (chave, descricao)
            VALUES (:chave, :descricao)
        ");

        return $stmt->execute([
            ':chave'     => $chave,
            ':descricao' => $descricao
        ]);
    }
}
