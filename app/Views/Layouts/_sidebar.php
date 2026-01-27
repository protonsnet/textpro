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

<aside
    id="sidebar"
    class="sidebar bg-white shadow-md border-r flex flex-col transition-all duration-300 w-64 overflow-hidden"
>
    <div class="flex items-center justify-between p-4 mb-2">
        <h3 class="sidebar-text text-lg font-semibold text-gray-700 whitespace-nowrap">
            Navegação
        </h3>
        <button id="toggleSidebar" type="button" class="p-2 rounded-md hover:bg-gray-100 transition" title="Minimizar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-2 space-y-1">

        <a href="<?= BASE_URL ?>/profile" class="sidebar-link flex items-center p-3 rounded-lg transition <?= active('/profile') ?>" title="Meu Perfil">
            <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="sidebar-text ml-3 whitespace-nowrap">Meu Perfil</span>
        </a>    

        <a href="<?= BASE_URL ?>/dashboard" class="sidebar-link flex items-center p-3 rounded-lg transition <?= active('/dashboard') ?>" title="Meus Documentos">
            <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="sidebar-text ml-3 whitespace-nowrap">Meus Documentos</span>
        </a>

        <a href="<?= BASE_URL ?>/payments" class="sidebar-link flex items-center p-3 rounded-lg transition <?= active('/payments') ?>" title="Faturas">
            <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span class="sidebar-text ml-3 whitespace-nowrap">Minhas Faturas</span>
        </a>

        <a href="<?= BASE_URL ?>/profile/password" class="sidebar-link flex items-center p-3 rounded-lg transition <?= active('/profile/password') ?>" title="Alterar Senha">
            <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            <span class="sidebar-text ml-3 whitespace-nowrap">Alterar Senha</span>
        </a>

        <?php if ($canCreate): ?>
            <a href="<?= BASE_URL ?>/editor" class="sidebar-link flex items-center p-3 rounded-lg transition <?= active('/editor') ?>" title="Novo Documento">
                <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="sidebar-text ml-3 whitespace-nowrap">Novo Documento</span>
            </a>
        <?php else: ?>
            <div class="sidebar-link p-3 rounded-lg text-gray-400 cursor-not-allowed flex items-center" title="Acesso Bloqueado">
                <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <div class="sidebar-text flex flex-col ml-3">
                    <span class="whitespace-nowrap">Novo Documento</span>
                    <?php if($isSuspended && !$hasActiveOrTrial): ?>
                        <span class="text-[9px] text-red-500 font-bold uppercase">Suspensa</span>
                    <?php elseif($limitReached): ?>
                        <span class="text-[9px] text-orange-500 font-bold uppercase">Limite Atingido</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/plans" class="sidebar-link flex items-center p-3 rounded-lg transition <?= active('/plans') ?>" title="Planos">
            <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"/></svg>
            <span class="sidebar-text ml-3 whitespace-nowrap">Meus Planos</span>
        </a>

        <?php if (!$isAdmin && $monthlyLimit !== null): ?>
            <div class="sidebar-extra-info mt-4 p-3 rounded-lg border <?= $limitReached ? 'bg-red-50 border-red-300' : 'bg-gray-50 border-gray-200' ?>">
                <p class="sidebar-text text-xs font-semibold text-gray-700 uppercase">Uso mensal</p>
                <div class="flex items-center mt-1">
                    <svg class="h-4 w-4 text-gray-500 min-w-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m0 10v2m0-2h-6m6 0h2m-2 0H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2h-1z"/></svg>
                    <p class="sidebar-text text-sm ml-2 <?= $limitReached ? 'text-red-600' : 'text-gray-600' ?>">
                        <?= $currentUsage ?> / <?= $monthlyLimit ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$isAdmin && ($limitReached || !FeatureService::has('export_pdf'))): ?>
            <a href="<?= BASE_URL ?>/plans" class="sidebar-link mt-3 p-3 rounded-lg flex items-center bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" title="Upgrade">
                <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span class="sidebar-text ml-3 font-semibold whitespace-nowrap">Fazer upgrade</span>
            </a>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <div class="sidebar-text pt-4 pb-2 px-3">
                <hr class="mb-4">
                <h3 class="text-xs font-semibold uppercase text-red-600 tracking-wide">Administração</h3>
            </div>

            <?php if (PermissionMiddleware::can('admin.dashboard')): ?>
                <a href="<?= BASE_URL ?>/admin" class="sidebar-link flex items-center p-3 rounded-lg transition <?= activeAdmin('/admin') ?>" title="Admin">
                    <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span class="sidebar-text ml-3 whitespace-nowrap">Dashboard Admin</span>
                </a>
            <?php endif; ?>

            <?php if (PermissionMiddleware::can('admin.dashboard')): ?>
                <a href="<?= BASE_URL ?>/admin/payments" class="sidebar-link flex items-center p-3 rounded-lg transition <?= activeAdmin('/admin/payments') ?>" title="Financeiro">
                    <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="sidebar-text ml-3 whitespace-nowrap">Financeiro (Asaas)</span>
                </a>
            <?php endif; ?>

            <?php if (PermissionMiddleware::can('users.manage')): ?>
                <a href="<?= BASE_URL ?>/admin/users" class="sidebar-link flex items-center p-3 rounded-lg transition <?= activeAdmin('/admin/users') ?>" title="Usuários">
                    <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="sidebar-text ml-3 whitespace-nowrap">Usuários</span>
                </a>
            <?php endif; ?>

            <?php if (PermissionMiddleware::can('plans.manage')): ?>
                <a href="<?= BASE_URL ?>/admin/plans" class="sidebar-link flex items-center p-3 rounded-lg transition <?= activeAdmin('/admin/plans') ?>" title="Planos Admin">
                    <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="sidebar-text ml-3 whitespace-nowrap">Planos</span>
                </a>
            <?php endif; ?>

            <?php if (PermissionMiddleware::can('templates.manage')): ?>
                <a href="<?= BASE_URL ?>/admin/templates" class="sidebar-link flex items-center p-3 rounded-lg transition <?= activeAdmin('/admin/templates') ?>" title="Templates Admin">
                    <svg class="h-5 w-5 min-w-[20px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    <span class="sidebar-text ml-3 whitespace-nowrap">Templates</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
</aside>

<style>
    /* Estilos para o estado colapsado */
    .sidebar-collapsed {
        width: 4.5rem !important;
    }

    .sidebar-collapsed .sidebar-text,
    .sidebar-collapsed .sidebar-extra-info {
        display: none !important;
    }

    .sidebar-collapsed .sidebar-link {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }

    /* Remove margens do ícone quando centralizado */
    .sidebar-collapsed .sidebar-link svg {
        margin-left: 0;
        margin-right: 0;
    }

    /* Ajuste no cabeçalho */
    .sidebar-collapsed .flex.items-center.justify-between {
        justify-content: center;
        padding: 1rem 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('toggleSidebar');
    const collapsedClass = 'sidebar-collapsed';

    // Aplica estado inicial sem animação para evitar "salto" visual
    if (localStorage.getItem('sidebar') === 'collapsed') {
        sidebar.classList.add(collapsedClass);
    }

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle(collapsedClass);
        localStorage.setItem('sidebar', 
            sidebar.classList.contains(collapsedClass) ? 'collapsed' : 'expanded'
        );
    });
});
</script>