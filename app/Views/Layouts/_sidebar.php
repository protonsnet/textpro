<?php

use App\Services\FeatureService;
use App\Models\SubscriptionModel;
use App\Models\PlanModel;
use App\Models\DocumentUsageModel;
use App\Models\SystemUserModel;
use App\Core\PermissionMiddleware;
use App\Services\SubscriptionSyncService; 

/**
 * =========================
 * HELPERS
 * =========================
 */
if (!function_exists('active')) {
    function active(string $path): string
    {
        return str_contains($_SERVER['REQUEST_URI'], $path)
            ? 'bg-blue-100 text-blue-800'
            : 'text-gray-600 hover:bg-blue-50 hover:text-blue-700';
    }
}

if (!function_exists('activeAdmin')) {
    function activeAdmin(string $path): string
    {
        return str_contains($_SERVER['REQUEST_URI'], $path)
            ? 'bg-red-100 text-red-700'
            : 'text-red-600 hover:bg-red-50';
    }
}

/**
 * =========================
 * USUÁRIO / ADMIN
 * =========================
 */
$userId = $_SESSION['user_id'] ?? null;

$isAdmin =
    PermissionMiddleware::can('admin.dashboard') ||
    PermissionMiddleware::can('users.manage') ||
    PermissionMiddleware::can('plans.manage') ||
    PermissionMiddleware::can('templates.manage');

// Verificação de Sincronização
if ($userId && !$isAdmin) {
    SubscriptionSyncService::checkAndSync($userId);
}

/**
 * =========================
 * PLANO (SÓ CLIENTE)
 * =========================
 */
$subscription = null;
$plan         = null;
$monthlyLimit = null;
$currentUsage = 0;
$limitReached = false;
$hasActiveOrTrial = false;

// Buscamos o status atualizado após o Sync
$userStatus = (new SystemUserModel())->find($userId);
$isSuspended = $userStatus->suspenso ?? false;

if (!$isAdmin && $userId) {
    $subscriptionModel = new SubscriptionModel();
    $subscription = $subscriptionModel->findActiveSubscription($userId);
    
    // Verifica se o status é active ou trialing para ignorar a suspensão
    if ($subscription) {
        $status = strtolower($subscription->status);
        if (in_array($status, ['active', 'trialing'])) {
            $hasActiveOrTrial = true;
        }
        
        if (!$isAdmin) {
            $planModel = new PlanModel();
            $plan = $planModel->find($subscription->plan_id);
            $monthlyLimit = $plan->limite_documentos ?? null;

            if ($monthlyLimit !== null) {
                $usageModel   = new \App\Models\DocumentUsageModel();
                $currentUsage = $usageModel->getUsage($userId, date('Y'), date('m'));
                $limitReached = $currentUsage >= $monthlyLimit;
            }
        }
    }
}
$canCreate = $isAdmin || (
    FeatureService::has('editor_access') && 
    !$limitReached && 
    (!$isSuspended || $hasActiveOrTrial)
);
?>

<aside class="sidebar bg-white shadow-md p-6 flex flex-col space-y-2 border-r">

    <h3 class="text-lg font-semibold mb-4 text-gray-700">
        Navegação
    </h3>

    <a href="<?= BASE_URL ?>/profile" class="p-3 rounded-lg transition <?= active('/profile') ?>">
        Meu Perfil
    </a>    

    <a href="<?= BASE_URL ?>/dashboard"
       class="p-3 rounded-lg transition <?= active('/dashboard') ?>">
        Meus Documentos
    </a>

    <a href="<?= BASE_URL ?>/payments"
       class="p-3 rounded-lg transition <?= active('/payments') ?>">
        Minhas Faturas / Boletos
    </a>

    <a href="<?= BASE_URL ?>/profile/password"
    class="p-3 rounded-lg transition <?= active('/profile/password') ?>">
        Alterar Senha
    </a>

    <?php if ($canCreate): ?>
        <a href="<?= BASE_URL ?>/editor"
           class="p-3 rounded-lg transition <?= active('/editor') ?>">
            Novo Documento
        </a>
    <?php else: ?>
        <div class="p-3 rounded-lg text-gray-400 cursor-not-allowed flex justify-between items-center">
            <span>Novo Documento</span>
            <span class="text-xs">🔒</span>
        </div>
        <?php if($isSuspended && !$hasActiveOrTrial): ?>
            <p class="text-[10px] text-red-500 px-3 font-bold">Assinatura Suspensa</p>
        <?php elseif($limitReached): ?>
            <p class="text-[10px] text-orange-500 px-3 font-bold">Limite de Uso Atingido</p>
        <?php endif; ?>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/plans"
       class="p-3 rounded-lg transition <?= active('/plans') ?>">
        Meus Planos
    </a>

    <?php if (!$isAdmin && $monthlyLimit !== null): ?>
        <div class="mt-4 p-3 rounded-lg border
            <?= $limitReached ? 'bg-red-50 border-red-300' : 'bg-gray-50 border-gray-200' ?>">

            <p class="text-sm font-semibold text-gray-700">
                Uso mensal
            </p>

            <p class="text-sm mt-1
                <?= $limitReached ? 'text-red-600' : 'text-gray-600' ?>">
                <?= $currentUsage ?> / <?= $monthlyLimit ?> documentos
            </p>

            <?php if ($limitReached): ?>
                <p class="text-xs mt-1 text-red-600 font-semibold">
                    Limite atingido
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (
        !$isAdmin &&
        ($limitReached || !FeatureService::has('export_pdf'))
    ): ?>
        <a href="<?= BASE_URL ?>/plans"
           class="mt-3 p-3 rounded-lg text-center font-semibold
                  bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition">
            ⭐ Fazer upgrade de plano
        </a>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
        <hr class="my-4">

        <h3 class="text-sm font-semibold uppercase text-red-600 tracking-wide mb-2">
            Administração
        </h3>

        <?php if (PermissionMiddleware::can('admin.dashboard')): ?>
            <a href="<?= BASE_URL ?>/admin"
               class="p-3 rounded-lg transition <?= activeAdmin('/admin') ?>">
                Dashboard Admin
            </a>
        <?php endif; ?>

        <?php if (PermissionMiddleware::can('admin.dashboard')): ?>
            <a href="<?= BASE_URL ?>/admin/payments"
               class="p-3 rounded-lg transition <?= activeAdmin('/admin/payments') ?>">
                Financeiro (Asaas)
            </a>
        <?php endif; ?>

        <?php if (PermissionMiddleware::can('users.manage')): ?>
            <a href="<?= BASE_URL ?>/admin/users"
               class="p-3 rounded-lg transition <?= activeAdmin('/admin/users') ?>">
                Usuários
            </a>
        <?php endif; ?>

        <?php if (PermissionMiddleware::can('plans.manage')): ?>
            <a href="<?= BASE_URL ?>/admin/plans"
               class="p-3 rounded-lg transition <?= activeAdmin('/admin/plans') ?>">
                Planos
            </a>
        <?php endif; ?>

        <?php if (PermissionMiddleware::can('templates.manage')): ?>
            <a href="<?= BASE_URL ?>/admin/templates"
               class="p-3 rounded-lg transition <?= activeAdmin('/admin/templates') ?>">
                Templates
            </a>
        <?php endif; ?>

    <?php endif; ?>

</aside>