<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PlanFeatureModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function hasFeature(int $planId, string $feature): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM plan_features
            WHERE plan_id = :plan_id
              AND feature_key = :feature
              AND enabled = 1
            LIMIT 1
        ");

        $stmt->execute([
            ':plan_id' => $planId,
            ':feature' => $feature
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function featuresByPlan(int $planId): array
    {
        $stmt = $this->db->prepare("
            SELECT feature_key
            FROM plan_features
            WHERE plan_id = :plan_id
              AND enabled = 1
        ");

        $stmt->execute([':plan_id' => $planId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
