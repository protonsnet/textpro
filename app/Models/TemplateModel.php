<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class TemplateModel extends Model
{
    protected string $table = 'templates';

    public function createTemplate(array $data): bool
    {
        $fields = implode(',', array_keys($data));
        $params = ':' . implode(',:', array_keys($data));

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} ({$fields})
            VALUES ({$params})
        ");

        return $stmt->execute($data);
    }

    public function findAll(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY id DESC")
            ->fetchAll();
    }

    public function updateTemplate(int $id, array $data): bool
    {
        unset($data['id']);

        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(',', $set) . " WHERE id = :id";
        $data['id'] = $id;

        return $this->db->prepare($sql)->execute($data);
    }

    public function deleteTemplate(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    public function countActive(): int
    {
        return (int)$this->db
            ->query("SELECT COUNT(*) FROM templates")
            ->fetchColumn();
    }

}
