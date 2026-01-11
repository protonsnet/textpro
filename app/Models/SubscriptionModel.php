<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SubscriptionModel extends Model
{
    protected string $table = 'subscriptions';

    public function findActiveSubscription(int $userId): object|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE user_id = :user_id
            AND status IN ('active', 'trialing')
            AND (
                status = 'trialing' OR 
                proximo_vencimento >= CURDATE() OR 
                data_fim >= NOW() OR 
                data_fim IS NULL
            )
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function createSubscription(
        int $userId,
        int $planId,
        string $stripeSubId,
        string $status
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            (user_id, plan_id, stripe_subscription_id, status, data_inicio, data_fim)
            VALUES (:user_id, :plan_id, :stripe_id, :status, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))
            ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                plan_id = VALUES(plan_id),
                updated_at = NOW()
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':plan_id' => $planId,
            ':stripe_id' => $stripeSubId,
            ':status' => $status
        ]);
    }

    public function updateStatusByStripeId(string $stripeId, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET status = :status, updated_at = NOW()
            WHERE stripe_subscription_id = :stripe_id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':stripe_id' => $stripeId
        ]);
    }

    public function updatePlanByStripeId(string $stripeId, string $priceId): bool
    {
        $plan = (new PlanModel())->findByStripePriceId($priceId);

        if (!$plan) {
            error_log("Stripe Webhook: plano não encontrado ({$priceId})");
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET plan_id = :plan_id, updated_at = NOW()
            WHERE stripe_subscription_id = :stripe_id
        ");

        return $stmt->execute([
            ':plan_id' => $plan->id,
            ':stripe_id' => $stripeId
        ]);
    }

    public function cancelSubscription(string $stripeId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET status = 'canceled', data_fim = NOW()
            WHERE stripe_subscription_id = :stripe_id
        ");

        return $stmt->execute([':stripe_id' => $stripeId]);
    }

    public function calculateMRR(): float
    {
        return (float)$this->db
            ->query("
                SELECT COALESCE(SUM(p.preco),0)
                FROM subscriptions s
                JOIN plans p ON p.id = s.plan_id
                WHERE s.status IN ('active','trialing')
            ")
            ->fetchColumn();
    }

    public function giveManualTrial(int $userId, int $days): bool
    {
        $newEndDate = date('Y-m-d H:i:s', strtotime("+$days days"));
        $vencimento = date('Y-m-d', strtotime($newEndDate));

        $sql = "INSERT INTO {$this->table} 
                (user_id, plan_id, stripe_subscription_id, status, data_inicio, data_fim, trial_ends_at, proximo_vencimento) 
                VALUES (:user_id, 1, 'MANUAL_FREE', 'trialing', NOW(), :data_fim, :trial_ends, :vencimento)
                ON DUPLICATE KEY UPDATE
                    status = 'trialing',
                    data_fim = VALUES(data_fim),
                    trial_ends_at = VALUES(trial_ends_at),
                    proximo_vencimento = VALUES(proximo_vencimento)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id'    => $userId,
            ':data_fim'   => $newEndDate,
            ':trial_ends' => $newEndDate,
            ':vencimento' => $vencimento
        ]);
    }

}
