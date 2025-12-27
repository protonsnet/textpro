<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class DocumentUsageModel extends Model
{
    protected string $table = 'document_usage';

    /**
     * Retorna uso mensal do usuário
     */
    public function getUsage(int $userId, int $year, int $month): int
    {
        $stmt = $this->db->prepare("
            SELECT total
            FROM {$this->table}
            WHERE user_id = :user_id
              AND year = :year
              AND month = :month
            LIMIT 1
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':year'    => $year,
            ':month'   => $month
        ]);

        return (int) ($stmt->fetchColumn() ?? 0);
    }

    /**
     * Incrementa uso mensal (1 documento)
     */
    public function increment(int $userId, int $year, int $month): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, year, month, total)
            VALUES (:user_id, :year, :month, 1)
            ON DUPLICATE KEY UPDATE total = total + 1
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':year'    => $year,
            ':month'   => $month
        ]);
    }
}
