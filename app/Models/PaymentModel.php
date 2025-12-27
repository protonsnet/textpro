<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class PaymentModel extends Model {
    protected string $table = 'payments';

    public function findByUser(int $userId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function allWithUsers(string $status = null) {
        $sql = "SELECT p.*, u.nome as user_nome, u.email as user_email 
                FROM {$this->table} p 
                JOIN system_users u ON p.user_id = u.id";
        
        if ($status) {
            $sql .= " WHERE p.status = :status";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($status) $stmt->bindValue(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}