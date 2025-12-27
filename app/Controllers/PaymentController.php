<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;
use App\Models\PaymentModel;

class PaymentController extends Controller {
    
    // Lista faturas do cliente logado
    public function index() {
        AuthMiddleware::check();
        $model = new PaymentModel();
        $payments = $model->findByUser($_SESSION['user_id']);
        
        $this->view('client/payments', [
            'title' => 'Meus Pagamentos',
            'payments' => $payments
        ]);
    }

    // Lista todas as faturas (Admin)
    public function adminIndex() {
        PermissionMiddleware::check('admin.dashboard');
        $status = $_GET['status'] ?? null;
        $model = new PaymentModel();
        $payments = $model->allWithUsers($status);
        
        $this->view('admin/payments', [
            'title' => 'Gestão Financeira',
            'payments' => $payments
        ]);
    }

    // Confirmação manual (Via Admin) corrigida
    public function confirmManual($id) {
        PermissionMiddleware::check('admin.dashboard');
        
        $valorFinal = $_POST['valor_final'] ?? null;
        $paymentModel = new PaymentModel();
        $userModel = new \App\Models\SystemUserModel();
        $asaas = new \App\Services\AsaasService();
        $db = \App\Core\Database::getInstance();

        $p = $paymentModel->find($id);
        if (!$p || !$valorFinal) {
            $_SESSION['error'] = "Dados inválidos para baixa.";
            header("Location: " . BASE_URL . "/admin/payments");
            exit;
        }

        try {
            $p = (new \App\Models\PaymentModel())->find($id);
            
            // 1. Atualizar pagamento local
            $db->prepare("UPDATE payments SET status = 'CONFIRMED', valor = ?, data_pagamento = NOW() WHERE id = ?")
            ->execute([$valorFinal, $id]);

            // 2. BUSCA PLANO MAIS PRÓXIMO DO VALOR PAGO
            $stmtPlan = $db->prepare("SELECT id FROM plans ORDER BY ABS(preco - ?) ASC LIMIT 1");
            $stmtPlan->execute([$valorFinal]);
            $plan = $stmtPlan->fetch(\PDO::FETCH_OBJ);
            $planId = $plan ? $plan->id : 1;

            // 3. Reativar Usuário
            $db->prepare("UPDATE system_users SET suspenso = 0, ativo = 1 WHERE id = ?")
            ->execute([$p->user_id]);

            // 4. Upsert na Assinatura
            $vencimento = date('Y-m-d', strtotime('+30 days'));
            $stmtSub = $db->prepare("SELECT id FROM subscriptions WHERE user_id = ? LIMIT 1");
            $stmtSub->execute([$p->user_id]);
            $subId = $stmtSub->fetchColumn();

            if ($subId) {
                $db->prepare("UPDATE subscriptions SET status = 'active', plan_id = ?, proximo_vencimento = ? WHERE id = ?")
                ->execute([$planId, $vencimento, $subId]);
            } else {
                $db->prepare("INSERT INTO subscriptions (user_id, plan_id, status, proximo_vencimento, data_inicio) VALUES (?, ?, 'active', ?, NOW())")
                ->execute([$p->user_id, $planId, $vencimento]);
                $subId = $db->lastInsertId();
            }

            // 5. Atualiza vínculo
            $db->prepare("UPDATE payments SET subscription_id = ? WHERE id = ?")->execute([$subId, $id]);

            $_SESSION['success'] = "Acesso liberado e plano ID #$planId atribuído (por proximidade de valor).";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erro: " . $e->getMessage();
        }

        header("Location: " . BASE_URL . "/admin/payments");
        exit;
    }
}