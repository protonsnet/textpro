<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\PermissionMiddleware;
use App\Models\PermissionModel;

class AdminPermissionController extends Controller
{
    protected PermissionModel $permissionModel;

    public function __construct()
    {
        parent::__construct();

        $this->permissionModel = new PermissionModel();
    }

    /**
     * Lista todas as permissões do sistema
     */
    public function index(): void
    {
        // RBAC: somente usuários com permissão explícita
        PermissionMiddleware::check('permissions.view');

        $this->view('admin/permissions/list', [
            'permissions' => $this->permissionModel->all(),
            'title'       => 'Permissões do Sistema'
        ]);
    }
}
