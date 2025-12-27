<?php

use App\Core\Database;
use PDO;

class RbacSeeder
{
    public static function run(Database $db): void
    {
        /* =====================
           ROLES
        ===================== */
        $db->exec("
            INSERT IGNORE INTO roles (id, nome, descricao) VALUES
            (1, 'admin', 'Administrador do sistema'),
            (2, 'cliente', 'Usuário cliente')
        ");

        /* =====================
           PERMISSIONS
        ===================== */
        $permissions = [
            'users.manage'      => 'Gerenciar usuários',
            'users.edit'        => 'Editar usuários',
            'users.deactivate'  => 'Desativar usuários',
            'roles.manage'      => 'Gerenciar roles',
            'permissions.view'  => 'Visualizar permissões',
            'plans.manage'      => 'Gerenciar planos',
            'templates.manage'  => 'Gerenciar templates',
            'dashboard.view'    => 'Visualizar dashboard'
        ];

        $stmt = $db->prepare("
            INSERT IGNORE INTO permissions (chave, descricao)
            VALUES (:chave, :descricao)
        ");

        foreach ($permissions as $key => $desc) {
            $stmt->execute([
                ':chave'     => $key,
                ':descricao' => $desc
            ]);
        }

        /* =====================
           ROLE → PERMISSIONS
        ===================== */
        $adminPermissions = array_keys($permissions);

        $stmt = $db->prepare("
            INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT :role_id, id FROM permissions WHERE chave = :chave
        ");

        foreach ($adminPermissions as $perm) {
            $stmt->execute([
                ':role_id' => 1,
                ':chave'   => $perm
            ]);
        }

        // Cliente: apenas dashboard
        $stmt->execute([
            ':role_id' => 2,
            ':chave'   => 'dashboard.view'
        ]);
    }
}
