<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class UserRoleModel extends Model
{
    protected string $table = 'user_roles';

    public function assign(int $userId, int $roleId): bool
    {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO {$this->table} (user_id, role_id)
            VALUES (:user_id, :role_id)
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':role_id' => $roleId
        ]);
    }

    public function remove(int $userId, int $roleId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE user_id = :user_id
              AND role_id = :role_id
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':role_id' => $roleId
        ]);
    }

    /**
     * Retorna objetos Role do usuário
     */
    public function rolesByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*
            FROM roles r
            JOIN {$this->table} ur ON ur.role_id = r.id
            WHERE ur.user_id = :user_id
              AND r.ativo = 1
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * ✅ NOVO MÉTODO
     * Retorna apenas os IDs das roles (para formulário)
     */
    public function getRoleIdsByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT role_id
            FROM {$this->table}
            WHERE user_id = :user_id
        ");

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return array_column(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            'role_id'
        );
    }

    /**
     * ✅ MÉTODO QUE ESTAVA FALTANDO
     * Sincroniza as roles do usuário
     */
    public function syncRoles(int $userId, array $newRoleIds): void
    {
        $this->db->beginTransaction();

        try {
            // Remove todas as roles atuais
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table}
                WHERE user_id = :user_id
            ");
            $stmt->execute([':user_id' => $userId]);

            // Insere as novas
            foreach ($newRoleIds as $roleId) {
                $this->assign($userId, (int)$roleId);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
