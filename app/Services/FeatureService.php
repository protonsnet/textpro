<?php
namespace App\Services;

use App\Models\SubscriptionModel;
use App\Models\PlanFeatureModel;
use App\Core\PermissionMiddleware;

class FeatureService
{
    public static function has(
        string $feature,
        ?SubscriptionModel $subscriptionModel = null,
        ?PlanFeatureModel $planFeatureModel = null
    ): bool {
        
        // 1. 🔑 VERIFICAÇÃO DE ADMIN AMPLIADA
        if (
            PermissionMiddleware::can('admin.access') || 
            PermissionMiddleware::can('admin.dashboard') ||
            PermissionMiddleware::can('admin.documents.full')
        ) {
            return true;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return false;
        }

        // 2. BUSCA ASSINATURA NO BANCO
        $subscriptionModel = $subscriptionModel ?? new SubscriptionModel();
        $subscription = $subscriptionModel->findActiveSubscription((int)$userId);

        // AJUSTE: Aceitar status 'active' OU 'trialing' (seu cadastro manual)
        $allowedStatuses = ['active', 'trialing'];
        if (!$subscription || !in_array(strtolower($subscription->status), $allowedStatuses)) {
            return false;
        }

        // 3. VERIFICAÇÃO DE TRIAL (Liberar todas as features se estiver no prazo)
        // Se o status for trialing, ele ganha acesso aos recursos básicos/totais
        if (
            strtolower($subscription->status) === 'trialing' || 
            (!empty($subscription->trial_ends_at) && $subscription->trial_ends_at >= date('Y-m-d H:i:s'))
        ) {
            return true;
        }

        // 4. VERIFICAÇÃO NA TABELA plan_features (Para assinaturas normais)
        $planFeatureModel  = $planFeatureModel ?? new PlanFeatureModel();
        
        return $planFeatureModel->hasFeature(
            (int)$subscription->plan_id,
            $feature
        );
    }
}