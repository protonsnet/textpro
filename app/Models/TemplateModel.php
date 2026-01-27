<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class TemplateModel extends Model
{
    protected string $table = 'templates';

    public function createTemplate(array $data): bool
    {
        $fields = implode(',', array_keys($data));
        $params = ':' . implode(',:', array_keys($data));

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} ({$fields})
            VALUES ({$params})
        ");

        return $stmt->execute($data);
    }

    public function find(int $id): object|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_OBJ) ?: false;
    }


    public function findAll(): array
    {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY id DESC")
            ->fetchAll();
    }

    public function updateTemplate(int $id, array $data): bool
    {
        // Colunas válidas da tabela templates
        $allowedFields = [
            'nome',
            'tamanho_papel',
            'largura_papel',
            'altura_papel',
            'margem_superior',
            'margem_inferior',
            'margem_esquerda',
            'margem_direita',
            'fonte_familia',
            'fonte_tamanho',
            'alinhamento',
            'entre_linhas',
            'cabecalho_html',
            'rodape_html',
            'posicao_numeracao',
            'template_capa_html'
        ];

        // Remove qualquer campo inválido ou índice numérico
        $filtered = array_intersect_key(
            $data,
            array_flip($allowedFields)
        );

        if (empty($filtered)) {
            return false;
        }

        $set = [];
        foreach ($filtered as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }

        $sql = "
            UPDATE {$this->table}
            SET " . implode(', ', $set) . "
            WHERE id = :id
        ";

        $filtered['id'] = $id;

        return $this->db->prepare($sql)->execute($filtered);
    }


    public function deleteTemplate(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    public function countActive(): int
    {
        return (int)$this->db
            ->query("SELECT COUNT(*) FROM templates")
            ->fetchColumn();
    }

    public function buildEditorCss(object $template): string
    {
        return "
            body {
                font-family: '{$template->fonte_familia}', serif;
                font-size: {$template->fonte_tamanho}pt;
                line-height: {$template->entre_linhas};
                text-align: {$template->alinhamento};
                padding: {$template->margem_superior}cm
                        {$template->margem_direita}cm
                        {$template->margem_inferior}cm
                        {$template->margem_esquerda}cm;
            }

            p {
                text-indent: 1.25cm;
                margin: 0;
            }

            h1, h2, h3 {
                font-weight: bold;
                margin-top: 1cm;
                margin-bottom: 0.5cm;
            }

            img {
                max-width: 100%;
                height: auto;
            }
        ";
    }

    public function getEditorCssById(int $id): string
    {
        $template = $this->find($id);

        if (!$template) {
            return '';
        }

        return $this->buildEditorCss($template);
    }

}
