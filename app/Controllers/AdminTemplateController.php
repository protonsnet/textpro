<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;
use App\Models\TemplateModel;
use App\Services\FontService;


class AdminTemplateController extends Controller
{
    protected TemplateModel $templateModel;
    protected FontService $fontService;

    public function __construct()
    {
        parent::__construct();

        AuthMiddleware::check();
        $this->templateModel = new TemplateModel();
        $this->fontService   = new FontService();
    }

    /**
     * =============================
     * LISTAGEM
     * =============================
     */
    public function index()
    {
        PermissionMiddleware::check('templates.manage');

        $this->view('admin/templates/list', [
            'templates' => $this->templateModel->findAll(),
            'title'     => 'Gerenciar Templates ABNT',
            'success'   => $_GET['success'] ?? null,
            'error'     => $_GET['error'] ?? null
        ]);
    }

    /**
     * =============================
     * FORMULÁRIO DE CRIAÇÃO
     * =============================
     */
    public function createForm()
    {
        PermissionMiddleware::check('templates.create');

        $this->view('admin/templates/create', [
            'title'          => 'Criar Novo Template',
            'availableFonts' => $this->fontService->getFontNames()
        ]);
    }

    /**
     * =============================
     * CRIAÇÃO
     * =============================
     */
    public function create()
    {
        PermissionMiddleware::check('templates.create');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/templates/create');
            exit;
        }

        if (empty($_POST['nome'])) {
            header('Location: ' . BASE_URL . '/admin/templates/create?error=Nome obrigatório');
            exit;
        }

        $data = $this->sanitizeTemplateData($_POST);

        $this->templateModel->createTemplate($data);

        header('Location: ' . BASE_URL . '/admin/templates?success=Template criado com sucesso');
        exit;
    }

    /**
     * =============================
     * FORMULÁRIO DE EDIÇÃO
     * =============================
     */
    public function editForm($id)
    {
        PermissionMiddleware::check('templates.edit');

        $template = $this->templateModel->find($id);

        if (!$template) {
            header('Location: ' . BASE_URL . '/admin/templates?error=Template não encontrado');
            exit;
        }

        $this->view('admin/templates/edit', [
            'template'       => $template,
            'title'          => 'Editar Template',
            'availableFonts' => $this->fontService->getFontNames()
        ]);
    }

    /**
     * =============================
     * ATUALIZAÇÃO
     * =============================
     */
    public function update($id)
    {
        PermissionMiddleware::check('templates.edit');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . "/admin/templates/edit/{$id}");
            exit;
        }

        $data = $this->sanitizeTemplateData($_POST);

        $this->templateModel->updateTemplate($id, $data);

        header('Location: ' . BASE_URL . '/admin/templates?success=Template atualizado');
        exit;
    }

    /**
     * =============================
     * EXCLUSÃO
     * =============================
     */
    public function delete($id)
    {
        PermissionMiddleware::check('templates.delete');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->templateModel->deleteTemplate($id);
        }

        header('Location: ' . BASE_URL . '/admin/templates?success=Template excluído');
        exit;
    }

    /**
     * =============================
     * SANITIZAÇÃO E PADRONIZAÇÃO
     * =============================
     */
    private function sanitizeTemplateData(array $input): array
    {
        $fonts = $this->getAvailableFonts();

        return [
            'nome'              => trim($input['nome']),
            'tamanho_papel'     => $input['tamanho_papel'] ?? 'custom',

            // Dimensões
            'largura_papel'     => isset($input['largura_papel']) ? (float)$input['largura_papel'] : null,
            'altura_papel'      => isset($input['altura_papel']) ? (float)$input['altura_papel'] : null,

            // Margens
            'margem_superior'   => (float)($input['margem_superior'] ?? 3.0),
            'margem_inferior'   => (float)($input['margem_inferior'] ?? 2.0),
            'margem_esquerda'   => (float)($input['margem_esquerda'] ?? 3.0),
            'margem_direita'    => (float)($input['margem_direita'] ?? 2.0),

            // Tipografia
            'fonte_familia' => in_array($input['fonte_familia'] ?? '', $fonts, true)
                            ? $input['fonte_familia']
                            : 'Times New Roman',

            'fonte_tamanho'     => (int)($input['fonte_tamanho'] ?? 12),
            'alinhamento'       => $input['alinhamento'] ?? 'justify',
            'entre_linhas'      => (float)($input['entre_linhas'] ?? 1.5),

            // Layout
            'cabecalho_html'    => $input['cabecalho_html'] ?? null,
            'rodape_html'       => $input['rodape_html'] ?? null,
            'posicao_numeracao' => $input['posicao_numeracao'] ?? 'superior_direita',

            // Capa
            'template_capa_html'=> $input['template_capa_html'] ?? null,
        ];
    }

    /**
     * =============================
     * FONTES DISPONÍVEIS NO SISTEMA
     * =============================
     */
    private function getAvailableFonts(): array
    {
        return [
            'Times New Roman',
            'Arial',
            'Calibri',
            'Georgia'
        ];
    }
}
