<?php

namespace App\Core;

use App\Models\UserRoleModel;
use App\Models\RolePermissionModel;

class PermissionMiddleware
{
    /**
     * Verifica se o usuário possui uma permissão
     */
    public static function check(string $permission, string $redirect = '/plans'): bool
    {
        AuthMiddleware::check();

        if (!self::can($permission)) {
            $base = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $base . $redirect);
            exit;
        }

        return true;
    }

    /**
     * Verifica sem redirecionar (uso em views)
     */
    public static function can(string $permission): bool
    {
        if (!isset($_SESSION['permissions_loaded'])) {
            self::loadPermissions();
        }

        return in_array($permission, $_SESSION['permissions'], true);
    }

    /**
     * Carrega permissões e cacheia na sessão
     */
    protected static function loadPermissions(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['permissions'] = [];
            return;
        }

        $userId = $_SESSION['user_id'];
        $userRoleModel       = new UserRoleModel();
        $rolePermissionModel = new RolePermissionModel();

        $roles = $userRoleModel->rolesByUser($userId);
        $permissions = [];

        foreach ($roles as $role) {
            $rolePermissions = $rolePermissionModel->permissionsByRole($role->id);
            foreach ($rolePermissions as $p) {
                $permissions[] = $p->chave;
            }
        }

        // 🔑 GARANTE QUE O ADMIN SEMPRE TENHA AS PERMISSÕES CRÍTICAS
        // Se quiser automatizar, mas o ideal é que venha do banco.
        
        $_SESSION['permissions'] = array_values(array_unique($permissions));
        $_SESSION['permissions_loaded'] = true;
    }

    /**
     * Invalida cache de permissões
     * Usar após alterar roles/permissões do usuário
     */
    public static function refresh(): void
    {
        unset(
            $_SESSION['permissions'],
            $_SESSION['permissions_loaded']
        );
    }
}
