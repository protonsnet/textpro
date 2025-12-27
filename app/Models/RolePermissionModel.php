<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RolePermissionModel extends Model
{
    protected string $table = 'role_permissions';

    public function assign(int $roleId, int $permissionId): bool
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO {$this->table} (role_id, permission_id)
            VALUES (:role_id, :permission_id)
        ");

        return $stmt->execute([
            ':role_id'       => $roleId,
            ':permission_id' => $permissionId
        ]);
    }

    public function remove(int $roleId, int $permissionId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE role_id = :role_id
              AND permission_id = :permission_id
        ");

        return $stmt->execute([
            ':role_id'       => $roleId,
            ':permission_id' => $permissionId
        ]);
    }

    public function permissionsByRole(int $roleId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*
            FROM permissions p
            JOIN {$this->table} rp ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
        ");
        $stmt->bindValue(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
