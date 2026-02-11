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

    public function index(): void
    {
        PermissionMiddleware::check('permissions.view');

        $this->view('admin/permissions/list', [
            'permissions' => $this->permissionModel->all(),
            'title'       => 'Permissões do Sistema'
        ]);
    }

    /**
     * Salva ou atualiza uma permissão
     */
    public function store(): void
    {
        // Recomendado ter uma permissão específica para gerenciar a estrutura
        PermissionMiddleware::check('roles.manage');

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $data = [
            'chave'     => trim($_POST['chave']),
            'descricao' => trim($_POST['descricao'])
        ];

        if (empty($data['chave'])) {
            $_SESSION['error'] = "A chave da permissão é obrigatória.";
            header("Location: " . BASE_URL . "/admin/permissions");
            exit;
        }

        if ($id) {
            $this->permissionModel->update($id, $data);
            $_SESSION['success'] = "Permissão atualizada com sucesso!";
        } else {
            $this->permissionModel->insert($data);
            $_SESSION['success'] = "Nova permissão criada!";
        }

        header("Location: " . BASE_URL . "/admin/permissions");
        exit;
    }

    /**
     * Remove uma permissão
     */
    public function delete(int $id): void
    {
        PermissionMiddleware::check('roles.manage');
        
        // Cuidado: deletar uma permissão remove o acesso de todos os cargos vinculados
        if ($this->permissionModel->delete($id)) {
            $_SESSION['success'] = "Permissão removida.";
        }
        
        header("Location: " . BASE_URL . "/admin/permissions");
        exit;
    }
}