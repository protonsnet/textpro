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
        // Se o usuário tiver qualquer uma das permissões administrativas, ele tem acesso total
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

        // 2. BUSCA ASSINATURA NO BANCO (Evita cache de sessão antigo)
        $subscriptionModel = $subscriptionModel ?? new SubscriptionModel();
        $subscription = $subscriptionModel->findActiveSubscription((int)$userId);

        // Se não houver assinatura ou o status não for ativo, nega o recurso
        if (!$subscription || $subscription->status !== 'active') {
            return false;
        }

        // 3. VERIFICAÇÃO DE TRIAL
        if (
            !empty($subscription->trial_ends_at) &&
            $subscription->trial_ends_at > date('Y-m-d H:i:s')
        ) {
            return true;
        }

        // 4. VERIFICAÇÃO NA TABELA plan_features
        // Aqui o sistema olha se o plan_id (Ex: 2 para Premium) tem a feature (Ex: export_pdf)
        $planFeatureModel  = $planFeatureModel ?? new PlanFeatureModel();
        
        return $planFeatureModel->hasFeature(
            (int)$subscription->plan_id,
            $feature
        );
    }
}