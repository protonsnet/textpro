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
    public function save(array $data): int|false
    {
        // =========================
        // UPDATE
        // =========================
        if (!empty($data['id'])) {

            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET
                    titulo = :titulo,
                    subtitulo = :subtitulo,
                    autor = :autor,
                    instituicao = :instituicao,
                    local_publicacao = :local_publicacao,
                    ano_publicacao = :ano_publicacao,
                    conteudo_html = :conteudo_html,
                    template_id = :template_id,
                    updated_at = NOW()
                WHERE id = :id
                AND user_id = :user_id
            ");

            $stmt->execute([
                ':titulo'           => $data['titulo'],
                ':subtitulo'        => $data['subtitulo'],
                ':autor'            => $data['autor'],
                ':instituicao'      => $data['instituicao'],
                ':local_publicacao' => $data['local_publicacao'],
                ':ano_publicacao'   => $data['ano_publicacao'],
                ':conteudo_html'    => $data['conteudo_html'],
                ':template_id'      => $data['template_id'],
                ':id'               => $data['id'],
                ':user_id'          => $data['user_id'],
            ]);



            return (int) $data['id'];
        }

        // =========================
        // INSERT
        // =========================
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
            (
                user_id,
                template_id,
                titulo,
                meta,
                conteudo_html,
                created_at,
                updated_at
            )
            VALUES
            (
                :user_id,
                :template_id,
                :titulo,
                :meta,
                :conteudo_html,
                NOW(),
                NOW()
            )

        ");

        $ok = $stmt->execute([
            ':user_id'          => $data['user_id'],
            ':template_id'      => $data['template_id'],
            ':titulo'           => $data['titulo'],
            ':meta'             => $data['meta'],
            ':conteudo_html'    => $data['conteudo_html'],
        ]);


        return $ok ? (int) $this->db->lastInsertId() : false;
    }
}
