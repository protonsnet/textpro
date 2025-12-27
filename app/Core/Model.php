<?php

namespace App\Core;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function find(int $id): object|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
