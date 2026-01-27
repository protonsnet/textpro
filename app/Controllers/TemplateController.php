<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;
use App\Models\TemplateModel;

class TemplateController extends Controller
{
    protected TemplateModel $templateModel;

    public function __construct()
    {
        parent::__construct();

        // Usuário precisa estar autenticado
        AuthMiddleware::check();

        $this->templateModel = new TemplateModel();
    }

    /**
     * Retorna um template em JSON
     * Usado pelo editor para troca dinâmica de layout
     *
     * GET /template/{id}
     */
    public function show(int $id): void
    {
        // Mesmo nível de permissão do editor
        PermissionMiddleware::check('documents.edit');

        $template = $this->templateModel->find($id);

        if (!$template) {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'Template não encontrado'
            ]);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'id'               => (int) $template->id,
            'fonte_familia'    => $template->fonte_familia,
            'fonte_tamanho'    => (float) $template->fonte_tamanho,
            'entre_linhas'     => (float) $template->entre_linhas,
            'alinhamento'      => $template->alinhamento,
            'margem_superior'  => (float) $template->margem_superior,
            'margem_inferior'  => (float) $template->margem_inferior,
            'margem_esquerda'  => (float) $template->margem_esquerda,
            'margem_direita'   => (float) $template->margem_direita,
            'largura_papel'    => (float) $template->largura_papel,
            'altura_papel'     => (float) $template->altura_papel,
        ]);
    }

    /**
     * Retorna o CSS do template para uso no editor
     *
     * GET /template/{id}/editor-css
     */
    public function editorCss(int $id): void
    {
        PermissionMiddleware::check('documents.edit');

        $css = $this->templateModel->getEditorCssById($id);

        if ($css === '') {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo '/* Template não encontrado */';
            return;
        }

        header('Content-Type: text/css; charset=utf-8');
        echo $css;
    }

}
