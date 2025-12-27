<?php
namespace App\Services;

use App\Core\Database;
use PDO;

class PermissionService
{
    /**
     * Carrega todas as chaves de permissão únicas para um usuário
     */
    public static function loadForUser(int $userId): array
    {
        $pdo = Database::getInstance();

        // Usamos DISTINCT para evitar duplicidade caso um usuário tenha múltiplas roles 
        // com a mesma permissão
        $sql = "
            SELECT DISTINCT p.chave
            FROM user_roles ur
            INNER JOIN role_permissions rp ON rp.role_id = ur.role_id
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE ur.user_id = :user_id
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // FETCH_COLUMN é mais eficiente que array_column(fetchAll) 
            // pois já retorna um array simples de strings diretamente do PDO
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
            
        } catch (\PDOException $e) {
            // Log de erro básico para debug se a query falhar
            error_log("Erro ao carregar permissões do usuário {$userId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Atalho para verificar se um usuário possui uma permissão específica
     * Útil para checagens rápidas fora da sessão
     */
    public static function hasPermission(int $userId, string $permissionKey): bool
    {
        $permissions = self::loadForUser($userId);
        return in_array($permissionKey, $permissions, true);
    }
}