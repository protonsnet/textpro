<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;
use App\Policies\SubscriptionPolicy;
use App\Services\UsageService;
use App\Services\PdfService;
use App\Models\DocumentModel;
use App\Models\TemplateModel;
use App\Models\SystemUserModel; 
use App\Models\SubscriptionModel;
use App\Services\SubscriptionSyncService;
use App\Services\PermissionService;
use App\Services\FontService;
use App\Models\PlanModel;


class DocumentController extends Controller
{
    protected DocumentModel $documentModel;
    protected TemplateModel $templateModel;
    protected SystemUserModel $userModel;
    protected SubscriptionModel $subscriptionModel;

    public function __construct()
    {
        parent::__construct();

        AuthMiddleware::check();

        $this->documentModel = new DocumentModel();
        $this->templateModel = new TemplateModel();
        $this->userModel     = new SystemUserModel();
        $this->subscriptionModel = new SubscriptionModel();
    }

    /**
     * Abre o editor de documentos
     */
    public function openEditor($id = null): void
    {
        PermissionMiddleware::refresh();
        
        PermissionMiddleware::check('documents.edit');
        $userId  = (int) $_SESSION['user_id'];
        $isAdmin = PermissionMiddleware::can('admin.documents.full');

        if (!$isAdmin) {
            $subscription = $this->subscriptionModel->findActiveSubscription($userId);

            if ($subscription) {
                // FORÇA OS DADOS PARA A SESSÃO
                $_SESSION['plan_id'] = (int) $subscription->plan_id;
                $_SESSION['subscription_status'] = strtolower($subscription->status);
                
                $plan = (new PlanModel())->find((int)$subscription->plan_id);
                // Se for Plano 1 (Trial), garante limite alto independente do banco
                $_SESSION['plan_limit'] = ($_SESSION['plan_id'] === 1) ? 999 : ($plan->limite_documentos ?? 0);
            }

            // Se for MANUAL_FREE, ignora o Sync para não ser sobrescrito
            if (!$subscription || $subscription->stripe_subscription_id !== 'MANUAL_FREE') {
                SubscriptionSyncService::checkAndSync($userId);
                $subscription = $this->subscriptionModel->findActiveSubscription($userId);
            }

            // Se após tudo isso não houver assinatura, aí sim a Policy barra
            SubscriptionPolicy::ensureActive($userId);
            
            if (!$id) {
                if (!UsageService::canCreateDocument((int)($_SESSION['plan_limit'] ?? 0))) {
                    $this->view('client/limit_reached');
                    return;
                }
            }
        }

        // --- Carregamento dos dados para a View ---
        $document = null;
        $content  = '';

        if ($id) {
            $document = $this->documentModel->findByIdAndUser($id, $userId);
            if (!$document) {
                $_SESSION['error'] = "Documento não encontrado.";
                header("Location: " . BASE_URL . "/dashboard");
                exit;
            }
            $document->meta = !empty($document->meta) ? json_decode($document->meta, true) : [];
            $content = $document->conteudo_html ?? '';
        }

        $templateId = $document->template_id ?? 1;
        $currentTemplate = $this->templateModel->find($templateId);
        $fontService = new FontService();

        // $this->view('client/editor', [
        //     'title'              => 'Editor TextPro',
        //     'document'           => $document,
        //     'content'            => $content,
        //     'currentTemplate'    => $currentTemplate,
        //     'availableTemplates' => $this->templateModel->all(),
        //     'availableFonts'     => $fontService->getFontNames()
        // ]);
        $this->view('client/editor', [
            'title'              => 'Editor TextPro',
            'document'           => $document,
            'no_sidebar'         => true,
            'content'            => $content,
            'currentTemplate'    => $currentTemplate,
            'availableTemplates' => $this->templateModel->all(),
            'availableFonts'     => $fontService->getFontNames(),
        ], 'layout_vazio');
    }

    /**
     * Salva o documento
     */
    public function save(): void
    {
        PermissionMiddleware::check('documents.edit');
        $userId = (int) $_SESSION['user_id'];
        
        // 1. Validação básica de campos obrigatórios
        $templateId = (int) ($_POST['template_id'] ?? 0);
        $titulo     = trim($_POST['titulo'] ?? '');

        if ($templateId <= 0 || $titulo === '') {
            $_SESSION['error'] = "Título e Modelo são obrigatórios.";
            $redirect = isset($_POST['id']) ? "/editor/{$_POST['id']}" : "/editor";
            header("Location: " . BASE_URL . $redirect);
            exit;
        }

        // 2. Coleta de todos os dados do formulário
        $data = [
            'id'                => !empty($_POST['id']) ? (int)$_POST['id'] : null,
            'user_id'           => $userId,
            'template_id'       => $templateId,
            'titulo'            => $titulo,
            'subtitulo'         => trim($_POST['subtitulo'] ?? ''),
            'autor'             => trim($_POST['autor'] ?? ''),
            'instituicao'       => trim($_POST['instituicao'] ?? ''),
            'local_publicacao'  => trim($_POST['local_publicacao'] ?? ''),
            'ano_publicacao'    => trim($_POST['ano_publicacao'] ?? date('Y')),
            'conteudo_json'     => $_POST['conteudo_json'] ?? null,
            'conteudo_html'     => $_POST['conteudo_html'] ?? null, // Agora capturando o HTML para o PDF
            'meta'              => json_encode([
                'saved_at' => date('Y-m-d H:i:s'),
                'ip'       => $_SERVER['REMOTE_ADDR']
            ])
        ];

        // 3. Persistência no Model
        $id = $this->documentModel->save($data);

        if ($id) {
            $_SESSION['success'] = "Documento salvo com sucesso!";
            header("Location: " . BASE_URL . "/editor/{$id}");
        } else {
            $_SESSION['error'] = "Erro ao salvar o documento.";
            header("Location: " . BASE_URL . "/dashboard");
        }
        exit;
    }


    /**
     * Exporta o documento para PDF
     */
    public function exportPdf(int $id): void
    {
        // 1. Pegamos o ID do usuário da sessão
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $isAdmin = PermissionMiddleware::can('admin.documents.full') || 
                PermissionMiddleware::can('admin.access');

        // 2. SINCRONIZAÇÃO TOTAL (Mova para o topo)
        // Se o usuário pagou agora, o checkAndSync vai atualizar o status no banco
        // e precisamos que ele também recarregue as permissões na $_SESSION.
        if (!$isAdmin && $userId > 0) {
            SubscriptionSyncService::checkAndSync($userId);
            
            // Opcional: Recarregar permissões explicitamente se o seu SyncService não o fizer
            $_SESSION['permissions'] = PermissionService::loadForUser($userId);
        }

        // 3. Verificação de Permissão RBAC (Agora com a sessão atualizada)
        PermissionMiddleware::check('documents.export');

        if (!$isAdmin) {
            $user = $this->userModel->find($userId);
            
            // Verifica suspensão financeira
            if ($user && $user->suspenso) {
                $_SESSION['error'] = "A exportação está desativada para contas com faturas pendentes.";
                header("Location: " . BASE_URL . "/payments");
                exit;
            }
            
            // 4. Verifica se o Plano atual tem o recurso 'export_pdf'
            // Esta chamada deve ser a última barreira
            SubscriptionPolicy::ensureFeature('export_pdf');
        }

        // 5. Busca do documento (Se for Admin, ignoramos o filtro de findByIdAndUser se necessário, 
        // mas aqui mantemos a segurança por ID de usuário para clientes)
        $document = $isAdmin 
            ? $this->documentModel->find($id) 
            : $this->documentModel->findByIdAndUser($id, $userId);

        if (!$document) {
            $_SESSION['error'] = "Documento não encontrado ou sem permissão de acesso.";
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $template = $this->templateModel->find($document->template_id);

        // 6. Geração do PDF
        (new PdfService())->generateAbntPdf(
            $document->conteudo_html,
            $template,
            $document->titulo,
            $document
        );
    }

    /**
     * Normaliza HTML do editor para salvar e gerar PDF
     */
    private function sanitizeEditorHtml(string $html): string
    {
        // Remove html/head/body duplicados
        $html = preg_replace('/<\/*(html|head|body)[^>]*>/i', '', $html);

        // Remove scripts por segurança
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        // Remove estilos inline perigosos (opcional, mas recomendado)
        $html = preg_replace('/style="[^"]*position\s*:\s*fixed[^"]*"/i', '', $html);

        return trim($html);
    }

    /**
     * Exclui um documento e seu arquivo físico (se existir)
     */
    public function delete(int $id): void
    {
        PermissionMiddleware::check('documents.edit');
        $userId = (int) $_SESSION['user_id'];

        // 1. Busca o documento para verificar propriedade e obter o caminho do arquivo
        $document = $this->documentModel->findByIdAndUser($id, $userId);

        if (!$document) {
            $_SESSION['error'] = "Documento não encontrado ou permissão negada.";
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        // 2. Lógica para exclusão de arquivo físico (OnlyOffice)
        // Verifica se existe um caminho de arquivo gravado no banco
        if (!empty($document->file_path)) {
            // APP_ROOT aponta para a raiz do projeto. Concatenamos com /public + caminho do banco
            $absolutePath = APP_ROOT . "/public" . $document->file_path;

            if (file_exists($absolutePath)) {
                // Tenta deletar o arquivo do storage
                unlink($absolutePath);
            }
        }

        // 3. Exclui o registro do banco de dados
        if ($this->documentModel->delete($id)) {
            $_SESSION['success'] = "Documento excluído com sucesso.";
        } else {
            $_SESSION['error'] = "Erro ao tentar excluir o registro do banco.";
        }

        // 4. Redirecionamento inteligente: volta para onde o usuário estava
        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . "/dashboard");
        header("Location: " . $redirect);
        exit;
    }

    // public function uploadImage()
    // {
    //     header('Content-Type: application/json; charset=utf-8');

    //     if (empty($_FILES['image'])) {
    //         http_response_code(400);
    //         echo json_encode(['error' => 'Nenhuma imagem enviada']);
    //         return;
    //     }

    //     $file = $_FILES['image'];

    //     if ($file['error'] !== UPLOAD_ERR_OK) {
    //         http_response_code(400);
    //         echo json_encode([
    //             'error' => 'Erro no upload',
    //             'code'  => $file['error']
    //         ]);
    //         return;
    //     }

    //     if ($file['size'] > 3 * 1024 * 1024) {
    //         http_response_code(400);
    //         echo json_encode(['error' => 'Imagem maior que 3MB']);
    //         return;
    //     }

    //     $allowed = [
    //         'image/jpeg' => 'jpg',
    //         'image/png'  => 'png',
    //         'image/webp' => 'webp'
    //     ];

    //     if (!isset($allowed[$file['type']])) {
    //         http_response_code(400);
    //         echo json_encode(['error' => 'Formato não permitido']);
    //         return;
    //     }

    //     // ✅ Pasta correta
    //     $uploadDir = realpath(__DIR__ . '/../../public') . '/uploads/images/';

    //     if (!$uploadDir) {
    //         http_response_code(500);
    //         echo json_encode(['error' => 'Diretório base não encontrado']);
    //         return;
    //     }

    //     if (!is_dir($uploadDir)) {
    //         mkdir($uploadDir, 0775, true);
    //     }

    //     if (!is_writable($uploadDir)) {
    //         http_response_code(500);
    //         echo json_encode(['error' => 'Diretório sem permissão de escrita']);
    //         return;
    //     }

    //     $filename = uniqid('img_', true) . '.' . $allowed[$file['type']];
    //     $target   = $uploadDir . $filename;

    //     if (!move_uploaded_file($file['tmp_name'], $target)) {
    //         http_response_code(500);
    //         echo json_encode(['error' => 'Falha ao mover o arquivo']);
    //         return;
    //     }

    //     echo json_encode([
    //         'url' => BASE_URL . '/uploads/images/' . $filename
    //     ]);
    // }

}