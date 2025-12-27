<?php
namespace App\Services;

use App\Models\SubscriptionModel;
use App\Models\SystemUserModel;
use App\Models\PlanModel;
use App\Core\Database;
use App\Services\PermissionService;

class SubscriptionSyncService {
    
    public static function checkAndSync($userId) {
        $db = Database::getInstance();
        $subModel = new SubscriptionModel();
        $planModel = new PlanModel();
        $userModel = new SystemUserModel();
        
        $user = $userModel->find($userId);
        // Busca a assinatura ignorando rigor de data para o Sync decidir o que fazer
        $subscription = $subModel->findActiveSubscription($userId);

        if (!$subscription) {
            return; // Se não tem nada, sai.
        }

        // Atualiza Sessão com dados do Banco
        $_SESSION['plan_id'] = $subscription->plan_id;
        $_SESSION['user_suspenso'] = $user->suspenso ?? 0;
        
        // Recarrega permissões para garantir que a permissão de PDF entre na sessão
        $_SESSION['permissions'] = PermissionService::loadForUser($userId);

        $plan = $planModel->find($subscription->plan_id);
        $_SESSION['plan_limit'] = $plan->limite_documentos ?? 9999;

        $hoje = date('Y-m-d H:i:s');
        $vencimento = $subscription->proximo_vencimento;

        // 1. Bloqueio por expiração
        if ($vencimento && $hoje > $vencimento && !$user->suspenso) {
            $db->prepare("UPDATE system_users SET suspenso = 1 WHERE id = ?")->execute([$userId]);
            $db->prepare("UPDATE subscriptions SET status = 'expired' WHERE id = ?")->execute([$subscription->id]);
            $_SESSION['user_suspenso'] = 1;
            return;
        }

        // 2. Liberação por pagamento
        if ($user->suspenso) {
            $stmt = $db->prepare("SELECT id FROM payments WHERE user_id = ? AND status = 'CONFIRMED' AND data_pagamento >= ? LIMIT 1");
            $stmt->execute([$userId, $subscription->data_inicio]);
            
            if ($stmt->fetch()) {
                $db->prepare("UPDATE system_users SET suspenso = 0 WHERE id = ?")->execute([$userId]);
                $db->prepare("UPDATE subscriptions SET status = 'active' WHERE id = ?")->execute([$subscription->id]);
                $_SESSION['user_suspenso'] = 0;
            }
        }
    }
}