<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;
use App\Core\Database;
use App\Models\SystemUserModel;
use App\Models\PlanModel;
use App\Models\SubscriptionModel;
use App\Models\TemplateModel;
use App\Models\DocumentModel;

class AdminController extends Controller
{
    protected $userModel;
    protected $planModel;
    protected $subscriptionModel;
    protected $templateModel;
    protected $documentModel;

    public function __construct()
    {
        parent::__construct();

        AuthMiddleware::check();

        $this->userModel         = new SystemUserModel();
        $this->planModel         = new PlanModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->templateModel     = new TemplateModel();
        $this->documentModel     = new DocumentModel();
    }

    /**
     * Dashboard Administrativo Profissional
     */
    public function dashboard()
    {
        PermissionMiddleware::check('admin.dashboard');

        $db = Database::getInstance();

        // Faturamento real: Soma de pagamentos CONFIRMED no mês atual (Dezembro 2025)
        $faturamentoMes = $db->query("
            SELECT SUM(valor) FROM payments 
            WHERE status = 'CONFIRMED' 
            AND MONTH(data_pagamento) = MONTH(CURRENT_DATE()) 
            AND YEAR(data_pagamento) = YEAR(CURRENT_DATE())
        ")->fetchColumn();

        // Busca documentos com o nome do usuário proprietário via JOIN
        $documentos = $db->query("
            SELECT d.id, d.titulo, d.created_at, u.nome as proprietario 
            FROM documents d
            JOIN system_users u ON d.user_id = u.id
            ORDER BY d.created_at DESC
            LIMIT 10
        ")->fetchAll(\PDO::FETCH_OBJ);

        $this->view('admin/dashboard', [
            'total_clientes'   => $this->userModel->countActive(),
            'templates_ativos' => $this->templateModel->countActive(),
            'faturamento_real' => $faturamentoMes ?? 0,
            'documentos'       => $documentos,
            'title'            => 'Dashboard Administrativo'
        ]);
    }

    /**
     * Assinaturas
     */
    public function manageSubscriptions()
    {
        PermissionMiddleware::check('subscriptions.manage');

        $this->view('admin/subscriptions/list', [
            'subscriptions' => $this->subscriptionModel->findAll(),
            'title'         => 'Assinaturas'
        ]);
    }
    
}
