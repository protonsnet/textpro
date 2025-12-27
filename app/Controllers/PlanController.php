<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\PlanModel;
use App\Models\SystemUserModel;
use App\Services\AsaasService;

class PlanController extends Controller
{
    protected PlanModel $planModel;

    public function __construct()
    {
        parent::__construct();
        $this->planModel = new PlanModel();
    }

    /**
     * Lista planos disponíveis (PÚBLICO)
     */
    public function listPlans(): void
    {
        $planos = $this->planModel->allActive();

        $this->view('plans/list', [
            'title'  => 'Nossos Planos TextPro',
            'planos' => $planos
        ]);
    }

    /**
     * Página de sucesso/aguardando pagamento
     */
    public function success(): void
    {
        $this->view('plans/success', [
            'title' => 'Pagamento em Processamento'
        ]);
    }

    /**
     * Inicia checkout ASAAS (LOGIN OBRIGATÓRIO)
     */
    public function checkout($planId): void
    {
        AuthMiddleware::check('/login');
        
        $plano = $this->planModel->find((int)$planId);
        $userModel = new \App\Models\SystemUserModel();
        $user = $userModel->find($_SESSION['user_id']);

        if (!$plano || !$user) {
            die("Erro: Plano ou Usuário não encontrado.");
        }

        $asaas = new \App\Services\AsaasService();

        try {
            // 1. Garante o Customer ID
            if (empty($user->asaas_customer_id)) {
                $asaasCustomer = $asaas->createCustomer($user);
                
                if (isset($asaasCustomer->id)) {
                    $userModel->update($user->id, ['asaas_customer_id' => $asaasCustomer->id]);
                    $user->asaas_customer_id = $asaasCustomer->id;
                } else {
                    // Trata erro de validação (ex: CPF inválido)
                    $msg = $asaasCustomer->errors[0]->description ?? 'Erro desconhecido';
                    die("Erro ao criar cliente no Asaas: " . $msg);
                }
            }

            // 2. Cria a cobrança
            $dueDate = date('Y-m-d', strtotime('+3 days'));
            $billing = $asaas->createBilling(
                $user->asaas_customer_id, 
                $plano->preco, 
                $dueDate, 
                "Assinatura " . $plano->nome
            );

            if (isset($billing->invoiceUrl)) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare("INSERT INTO payments (user_id, asaas_billing_id, valor, status, data_vencimento, invoice_url) VALUES (?, ?, ?, 'PENDING', ?, ?)");
                $stmt->execute([$user->id, $billing->id, $plano->preco, $dueDate, $billing->invoiceUrl]);

                header('Location: ' . $billing->invoiceUrl);
                exit;
            } else {
                $msg = $billing->errors[0]->description ?? 'Falha ao gerar fatura';
                die("Erro ao gerar fatura: " . $msg);
            }

        } catch (\Exception $e) {
            die("Erro Crítico: " . $e->getMessage());
        }
    }
}