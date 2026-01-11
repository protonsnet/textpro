<?php

namespace App\Services;

use App\Models\DocumentUsageModel;
use App\Core\PermissionMiddleware;

class UsageService
{
    /**
     * Verifica se o usuário pode criar um novo documento
     */
    public static function canCreateDocument(?int $limit): bool
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) return false;

        if (PermissionMiddleware::can('admin.dashboard')) return true;

        // Busca no banco para não depender APENAS da sessão que pode estar cacheada
        $subModel = new \App\Models\SubscriptionModel();
        $sub = $subModel->findActiveSubscription((int)$userId);

        if ($sub && strtolower($sub->status) === 'trialing') {
            return true; // Trial sempre pode criar
        }

        if ($limit === null || $limit === -1) return true;

        return self::getCurrentUsage() < $limit;
    }

    /**
     * Incrementa uso mensal (somente para usuários comuns)
     */
    public static function increment(): void
    {
        // 👑 ADMIN não consome limite
        if (PermissionMiddleware::can('admin.dashboard')) {
            return;
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return;
        }

        $model = new DocumentUsageModel();
        $model->increment(
            (int) $userId,
            (int) date('Y'),
            (int) date('m')
        );
    }

    /**
     * Retorna uso atual do mês
     */
    public static function getCurrentUsage(): int
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return 0;
        }

        // 👑 ADMIN → sempre zero (ilimitado)
        if (PermissionMiddleware::can('admin.dashboard')) {
            return 0;
        }

        $model = new DocumentUsageModel();

        return (int) $model->getUsage(
            (int) $userId,
            (int) date('Y'),
            (int) date('m')
        );
    }
}
