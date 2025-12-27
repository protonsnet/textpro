<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SystemUserModel extends Model
{
    protected string $table = 'system_users';

    /**
     * Busca usuário pelo e-mail
     */
    public function findByEmail(string $email): object|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * NOVO: Busca usuário pelo ID do Asaas (Essencial para Webhooks)
     */
    public function findByAsaasId(string $asaasId): object|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE asaas_customer_id = :asaas_id
            LIMIT 1
        ");
        $stmt->bindValue(':asaas_id', $asaasId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Cria um novo usuário
     */
    public function create(string $nome, string $email, string $senha, ?string $cpf_cnpj, string $telefone, string $pais = 'BR'): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            (nome, email, senha, cpf_cnpj, telefone, pais, ativo, suspenso, created_at)
            VALUES (:nome, :email, :senha, :cpf, :tel, :pais, 1, 0, NOW())
        ");

        return $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':cpf'   => $cpf_cnpj, // Aceita null agora
            ':tel'   => $telefone,
            ':pais'  => $pais
        ]);
    }

    /**
     * Método Genérico de Update (Aprimorado)
     */
    public function update(int $id, array $data): bool
    {
        $fields = "";
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields .= "{$key} = :{$key}, ";
                $params[":{$key}"] = $value;
            }
        }
        $fields = rtrim($fields, ", ");

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET {$fields}, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute($params);
    }

    /* ============================
       MÉTODOS DE STATUS E FINANCEIRO
    ============================ */

    /**
     * Bloqueia acesso por inadimplência
     */
    public function suspend(int $id): bool
    {
        return $this->update($id, ['suspenso' => 1]);
    }

    /**
     * Libera acesso após pagamento
     */
    public function reactivate(int $id): bool
    {
        return $this->update($id, ['suspenso' => 0, 'ativo' => 1]);
    }

    public function find(int $id): object|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY created_at DESC")
            ->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Atalho para atualizar dados do ASAAS e Perfil
     */
    public function updateBillingData(int $id, array $billingData): bool
    {
        return $this->update($id, $billingData);
    }

    public function resetPassword(int $id, string $newPassword): bool
    {
        return $this->update($id, [
            'senha' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Conta o total de usuários ativos no sistema
     */
    public function countActive(): int
    {
        return (int) $this->db
            ->query("SELECT COUNT(*) FROM {$this->table} WHERE ativo = 1")
            ->fetchColumn();
    }

    public function updateProfile(int $id, array $data): bool
    {
        // Remove senha do array se estiver vazia para não sobrescrever com hash vazio
        if (isset($data['senha']) && empty($data['senha'])) {
            unset($data['senha']);
        } elseif (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }

        return $this->update($id, $data);
    }

    /**
     * Busca usuário pelo ID do Stripe (Essencial para Webhooks Internacionais)
     */
    public function findByStripeId(string $stripeId): object|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE stripe_customer_id = :stripe_id
            LIMIT 1
        ");
        $stmt->bindValue(':stripe_id', $stripeId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}