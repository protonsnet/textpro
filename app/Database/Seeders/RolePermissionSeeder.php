<?php

namespace App\Database\Seeders;

use App\Core\Database;

class RolePermissionSeeder
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->seedRolePermissions();
    }

    protected function seedRoles(): void
    {
        $roles = [
            'admin',
            'cliente',
        ];

        foreach ($roles as $role) {
            $this->db->execute(
                "INSERT IGNORE INTO roles (nome) VALUES (?)",
                [$role]
            );
        }
    }

    protected function seedPermissions(): void
    {
        $permissions = [
            'admin.access',
            'users.manage',
            'plans.manage',
            'templates.manage',
            'subscriptions.manage',

            'editor_access',
            'export_pdf',
            'documents.create',
            'documents.view',
        ];

        foreach ($permissions as $permission) {
            $this->db->execute(
                "INSERT IGNORE INTO permissions (chave) VALUES (?)",
                [$permission]
            );
        }
    }

    protected function seedRolePermissions(): void
    {
        // Busca IDs
        $roles = $this->db->query("SELECT id, nome FROM roles")->fetchAll();
        $permissions = $this->db->query("SELECT id, chave FROM permissions")->fetchAll();

        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role->nome] = $role->id;
        }

        $permissionMap = [];
        foreach ($permissions as $permission) {
            $permissionMap[$permission->chave] = $permission->id;
        }

        // ADMIN → tudo
        foreach ($permissionMap as $permissionId) {
            $this->db->execute(
                "INSERT IGNORE INTO role_permissions (role_id, permission_id)
                 VALUES (?, ?)",
                [$roleMap['admin'], $permissionId]
            );
        }

        // CLIENTE → permissões básicas
        $clientPermissions = [
            'editor_access',
            'documents.create',
            'documents.view',
        ];

        foreach ($clientPermissions as $key) {
            $this->db->execute(
                "INSERT IGNORE INTO role_permissions (role_id, permission_id)
                 VALUES (?, ?)",
                [$roleMap['cliente'], $permissionMap[$key]]
            );
        }
    }
}
