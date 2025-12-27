<?php

namespace App\Core;

use App\Models\SubscriptionModel;

class AuthMiddleware
{
    /**
     * Verifica apenas se o usuário está logado
     */
    public static function check(string $redirect = '/login'): bool
    {
        $base = defined('BASE_URL') ? BASE_URL : '';

        if (empty($_SESSION['user_id'])) {
            $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . $base . $redirect);
            exit;
        }

        return true;
    }

    /**
     * Verifica login + assinatura ativa (ou trial)
     */
    public static function checkSubscription(string $redirect = '/plans'): bool
    {
        self::check();

        $base = defined('BASE_URL') ? BASE_URL : '';

        $subscriptionModel = new SubscriptionModel();
        $subscription = $subscriptionModel->findActiveSubscription($_SESSION['user_id']);

        if (!$subscription) {
            header('Location: ' . $base . $redirect);
            exit;
        }

        if (!in_array($subscription->status, ['active', 'trialing'], true)) {
            header('Location: ' . $base . $redirect);
            exit;
        }

        return true;
    }

    public static function reloadPermissions(): void
    {
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        $userId = (int) $_SESSION['user_id'];

        $permissionModel = new \App\Models\PermissionModel();
        $permissions = $permissionModel->getPermissionsByUser($userId);

        $_SESSION['permissions'] = $permissions;
    }

}
