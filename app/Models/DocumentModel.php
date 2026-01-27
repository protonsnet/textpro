<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class DocumentModel extends Model
{
    protected string $table = 'documents';

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE user_id = :user_id
            ORDER BY updated_at DESC
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByIdAndUser(int $id, int $userId): object|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([
            ':id'      => $id,
            ':user_id'=> $userId
        ]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Cria ou atualiza um documento
     */
    // No DocumentModel.php, altere o método save:
    public function save(array $data): int|false
    {
        if (!empty($data['id'])) {
            // =========================
            // UPDATE
            // =========================
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET
                    titulo = :titulo,
                    subtitulo = :subtitulo,
                    autor = :autor,
                    instituicao = :instituicao,
                    local_publicacao = :local_publicacao,
                    ano_publicacao = :ano_publicacao,
                    conteudo_json = :conteudo_json,
                    conteudo_html = :conteudo_html,
                    template_id = :template_id,
                    meta = :meta,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");

            $stmt->execute([
                ':titulo'           => $data['titulo'],
                ':subtitulo'        => $data['subtitulo'] ?? null,
                ':autor'            => $data['autor'] ?? null,
                ':instituicao'      => $data['instituicao'] ?? null,
                ':local_publicacao' => $data['local_publicacao'] ?? null,
                ':ano_publicacao'   => $data['ano_publicacao'] ?? null,
                ':conteudo_json'    => $data['conteudo_json'],
                ':conteudo_html'    => $data['conteudo_html'],
                ':template_id'      => $data['template_id'],
                ':meta'             => $data['meta'] ?? null,
                ':id'               => $data['id'],
                ':user_id'          => $data['user_id']
            ]);
            
            return (int) $data['id'];
        }

        // =========================
        // INSERT
        // =========================
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            (
                user_id, template_id, titulo, subtitulo, autor, 
                instituicao, local_publicacao, ano_publicacao, 
                meta, conteudo_html, conteudo_json, 
                created_at, updated_at
            )
            VALUES
            (
                :user_id, :template_id, :titulo, :subtitulo, :autor, 
                :instituicao, :local_publicacao, :ano_publicacao, 
                :meta, :conteudo_html, :conteudo_json, 
                NOW(), NOW()
            )
        ");

        $ok = $stmt->execute([
            ':user_id'          => $data['user_id'],
            ':template_id'      => $data['template_id'],
            ':titulo'           => $data['titulo'],
            ':subtitulo'        => $data['subtitulo'] ?? null,
            ':autor'            => $data['autor'] ?? null,
            ':instituicao'      => $data['instituicao'] ?? null,
            ':local_publicacao' => $data['local_publicacao'] ?? null,
            ':ano_publicacao'   => $data['ano_publicacao'] ?? null,
            ':meta'             => $data['meta'] ?? null,
            ':conteudo_html' => is_array($data['conteudo_html']) || is_object($data['conteudo_html']) 
                    ? json_encode($data['conteudo_html']) 
                    : (string)$data['conteudo_html'],
            ':conteudo_json'    => $data['conteudo_json']
        ]);

        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Exclui um registro pelo ID
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
