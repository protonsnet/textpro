<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\PermissionMiddleware;
use App\Models\SystemUserModel;
use App\Models\RoleModel;
use App\Models\UserRoleModel;

class AdminUserRoleController extends Controller
{
    protected SystemUserModel $userModel;
    protected RoleModel $roleModel;
    protected UserRoleModel $userRoleModel;

    public function __construct()
    {
        parent::__construct();

        $this->userModel     = new SystemUserModel();
        $this->roleModel     = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
    }

    /**
     * Alias exigido pelo Router (GET)
     */
    public function editForm($userId)
    {
        return $this->edit($userId);
    }

    /**
     * Alias exigido pelo Router (POST)
     */
    public function update($userId)
    {
        return $this->edit($userId);
    }

    /**
     * GET + POST
     */
    public function edit($userId)
    {
        PermissionMiddleware::check('users.manage');

        $user = $this->userModel->find((int)$userId);
        if (!$user) {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roles = $_POST['roles'] ?? [];

            // Segurança: não permitir remover todas as próprias roles
            if ((int)$userId === ($_SESSION['user_id'] ?? 0) && empty($roles)) {
                header(
                    'Location: ' . BASE_URL .
                    "/admin/users/roles/{$userId}?error=Você não pode remover todas as suas roles"
                );
                exit;
            }

            $this->userRoleModel->syncRoles(
                (int)$userId,
                array_map('intval', $roles)
            );

            // Recarrega permissões se for o próprio usuário
            if ((int)$userId === ($_SESSION['user_id'] ?? 0)) {
                PermissionMiddleware::refresh();
            }

            header('Location: ' . BASE_URL . '/admin/users?success=Roles atualizadas');
            exit;
        }

        $this->view('admin/users/roles', [
            'user'      => $user,
            'roles'     => $this->roleModel->all(),
            'userRoles' => $this->userRoleModel->getRoleIdsByUser((int)$userId),
            'title'     => 'Atribuir Roles'
        ]);
    }
}
