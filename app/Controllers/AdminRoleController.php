<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\PermissionMiddleware;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\RolePermissionModel;

class AdminRoleController extends Controller
{
    protected RoleModel $roleModel;
    protected PermissionModel $permissionModel;
    protected RolePermissionModel $rolePermissionModel;

    public function __construct()
    {
        parent::__construct();

        $this->roleModel           = new RoleModel();
        $this->permissionModel     = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
    }

    /**
     * Lista todas as roles
     */
    public function index()
    {
        PermissionMiddleware::check('roles.manage');

        $this->view('admin/roles/list', [
            'roles' => $this->roleModel->all(),
            'title' => 'Gerenciar Roles'
        ]);
    }

    /**
     * Editar permissões da role
     */
    public function edit($id)
    {
        PermissionMiddleware::check('roles.manage');

        $role = $this->roleModel->find((int)$id);
        if (!$role) {
            header('Location: ' . BASE_URL . '/admin/roles');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $permissions = $_POST['permissions'] ?? [];

            $this->rolePermissionModel->syncPermissions(
                (int)$id,
                array_map('intval', $permissions)
            );

            header('Location: ' . BASE_URL . '/admin/roles');
            exit;
        }

        $this->view('admin/roles/edit', [
            'role'                => $role,
            'permissions'         => $this->permissionModel->all(),
            'assignedPermissions' => $this->rolePermissionModel
                                            ->getPermissionIdsByRole((int)$id),
            'title'               => 'Editar Role'
        ]);
    }
}
