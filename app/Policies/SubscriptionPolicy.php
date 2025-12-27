<?php
namespace App\Policies;

use App\Core\PermissionMiddleware;
use App\Models\SubscriptionModel;
use App\Services\FeatureService;

class SubscriptionPolicy
{
    /**
     * Assinatura ativa (ADMIN ignora)
     */
    public static function ensureActive(int $userId): void
    {
        if (PermissionMiddleware::can('admin.access')) {
            return;
        }

        $subscriptionModel = new SubscriptionModel();
        $subscription = $subscriptionModel->findActiveSubscription($userId);

        // Se não achar assinatura ou se ela estiver expirada/suspensa no banco
        if (!$subscription || $subscription->status !== 'active') {
            header('Location: ' . BASE_URL . '/plans');
            exit;
        }
    }

    /**
     * Feature específica (ADMIN ignora)
     */
    public static function ensureFeature(string $feature): void
    {
        // 1. Admin sempre passa
        // if (PermissionMiddleware::can('admin.access') || PermissionMiddleware::can('admin.documents.full')) {
        //     return;
        // }
        if (\App\Core\PermissionMiddleware::can('admin.access')) {
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2. Tenta verificar pelo FeatureService (Sessão)
        // 3. Se falhar na sessão, forçamos uma verificação no Banco de Dados
        if (!FeatureService::has($feature)) {
            
            // Verificação de segurança: O usuário pode ter acabado de pagar
            $subModel = new SubscriptionModel();
            $subscription = $subModel->findActiveSubscription((int)$userId);
            
            if ($subscription) {
                // Se achou assinatura ativa no banco, atualizamos a SESSÃO agora mesmo
                $_SESSION['plan_id'] = $subscription->plan_id;
                
                // Tenta checar novamente após atualizar a sessão
                if (FeatureService::has($feature)) {
                    return; // Agora ele tem acesso!
                }
            }

            // Se mesmo olhando no banco ele não tem a feature, aí sim redireciona
            $_SESSION['error'] = "Seu plano atual não permite este recurso (exportação PDF).";
            header('Location: ' . BASE_URL . '/plans');
            exit;
        }
    }
}