<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\PermissionMiddleware;
use App\Models\PlanModel;

class AdminPlanController extends Controller
{
    protected PlanModel $planModel;

    public function __construct()
    {
        parent::__construct();
        $this->planModel = new PlanModel();
    }

    /**
     * Lista todos os planos
     */
    public function index(): void
    {
        PermissionMiddleware::check('plans.manage');

        $plans = $this->planModel->findAll();

        foreach ($plans as $plan) {
            $plan->recursos = $this->decodeRecursos($plan->recursos);
        }

        $this->view('admin/plans/list', [
            'plans'   => $plans,
            'title'   => 'Gerenciar Planos',
            'success' => $_GET['success'] ?? null,
            'error'   => $_GET['error'] ?? null
        ]);
    }

    /**
     * Formulário de criação
     */
    public function createForm(): void
    {
        PermissionMiddleware::check('plans.manage');

        $this->view('admin/plans/create', [
            'title' => 'Criar Plano'
        ]);
    }

    /**
     * Criação do plano
     */
    public function create(): void
    {
        PermissionMiddleware::check('plans.manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/plans/create');
            exit;
        }

        $data = $this->sanitizePlanData($_POST);

        if (!$this->validatePlanData($data)) {
            $this->view('admin/plans/create', [
                'title' => 'Criar Plano',
                'error' => 'Nome e preço são obrigatórios.'
            ]);
            return;
        }

        if ($this->planModel->insert($data)) {
            header('Location: ' . BASE_URL . '/admin/plans?success=Plano criado com sucesso');
            exit;
        }

        $this->view('admin/plans/create', [
            'title' => 'Criar Plano',
            'error' => 'Erro ao salvar o plano.'
        ]);
    }

    /**
     * Formulário de edição
     */
    public function editForm(int $id): void
    {
        PermissionMiddleware::check('plans.manage');

        $plan = $this->planModel->find($id);

        if (!$plan) {
            header('Location: ' . BASE_URL . '/admin/plans?error=Plano não encontrado');
            exit;
        }

        // NORMALIZA recursos → array
        $plan->recursos = $this->decodeRecursos($plan->recursos);

        $this->view('admin/plans/edit', [
            'plan'  => $plan,
            'title' => 'Editar Plano'
        ]);
    }

    /**
     * Atualização do plano
     */
    public function update(int $id): void
    {
        PermissionMiddleware::check('plans.manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . "/admin/plans/edit/{$id}");
            exit;
        }

        $data = $this->sanitizePlanData($_POST);

        if (!$this->validatePlanData($data)) {
            $plan = $this->planModel->find($id);
            $plan->recursos = $this->decodeRecursos($plan->recursos);

            $this->view('admin/plans/edit', [
                'plan'  => $plan,
                'title' => 'Editar Plano',
                'error' => 'Nome e preço são obrigatórios.'
            ]);
            return;
        }

        if ($this->planModel->update($id, $data)) {
            header('Location: ' . BASE_URL . '/admin/plans?success=Plano atualizado com sucesso');
            exit;
        }

        header('Location: ' . BASE_URL . '/admin/plans?error=Erro ao atualizar plano');
        exit;
    }

    /**
     * Desativação lógica
     */
    public function deactivate(int $id): void
    {
        PermissionMiddleware::check('plans.manage');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->planModel->deactivate($id);
        }

        header('Location: ' . BASE_URL . '/admin/plans?success=Plano desativado');
        exit;
    }

    /* ========================
       Helpers
    ======================== */

    private function sanitizePlanData(array $input): array
    {
        return [
            'nome'              => trim($input['nome'] ?? ''),
            'descricao'         => trim($input['descricao'] ?? ''),
            'preco'             => (float) ($input['preco'] ?? 0),
            'duracao_meses'     => (int) ($input['duracao_meses'] ?? 1),
            'stripe_price_id'   => trim($input['stripe_price_id'] ?? ''),
            'status'            => $input['status'] ?? 'ativo',
            'limite_documentos' => !empty($input['limite_documentos'])
                ? (int) $input['limite_documentos']
                : null,
            'recursos'          => json_encode(
                array_values($input['recursos'] ?? []),
                JSON_UNESCAPED_UNICODE
            )
        ];
    }

    private function validatePlanData(array $data): bool
    {
        return $data['nome'] !== '' && $data['preco'] > 0;
    }

    private function decodeRecursos($recursos): array
    {
        if (is_array($recursos)) {
            return $recursos;
        }

        if (is_string($recursos)) {
            $decoded = json_decode($recursos, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
