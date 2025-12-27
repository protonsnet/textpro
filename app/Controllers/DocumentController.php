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
use App\Services\SubscriptionSyncService;
use App\Services\PermissionService;

class DocumentController extends Controller
{
    protected DocumentModel $documentModel;
    protected TemplateModel $templateModel;
    protected SystemUserModel $userModel;

    public function __construct()
    {
        parent::__construct();

        AuthMiddleware::check();

        $this->documentModel = new DocumentModel();
        $this->templateModel = new TemplateModel();
        $this->userModel     = new SystemUserModel();
    }

    /**
     * Abre o editor de documentos
     */
    public function openEditor($id = null): void
    {
        PermissionMiddleware::check('documents.edit');

        $userId = (int) $_SESSION['user_id'];
        $isAdmin = PermissionMiddleware::can('admin.documents.full');

        // 1. Primeiro sincronizamos e verificamos suspensão
        if (!$isAdmin) {
            SubscriptionSyncService::checkAndSync($userId);
            
            $user = $this->userModel->find($userId);
            if ($user && $user->suspenso) {
                $_SESSION['error'] = "Acesso bloqueado. Regularize seus pagamentos.";
                header("Location: " . BASE_URL . "/payments");
                exit;
            }
            
            SubscriptionPolicy::ensureActive($userId);
        }

        $document = null;
        $content  = '';

        if ($id) {
            $document = $this->documentModel->findByIdAndUser($id, $userId);

            if (!$document) {
                http_response_code(403);
                exit('Documento não encontrado');
            }

            $content = $document->conteudo_html ?? '';
        } else {
            // 🚫 Limite de criação apenas para não-admin
            if (!$isAdmin) {
                $limit = (int) ($_SESSION['plan_limit'] ?? 0);
                if (!UsageService::canCreateDocument($limit)) {
                    $this->view('client/limit_reached'); // Certifique-se de criar este arquivo em app/Views/client/
                    return;
                }
            }
        }

        $templateId = $document->template_id ?? 1;

        $this->view('client/editor', [
            'title'              => 'Editor TextPro',
            'document'           => $document,
            'content'            => $content,
            'currentTemplate'    => $this->templateModel->find($templateId),
            'availableTemplates' => $this->templateModel->all()
        ]);
    }

    /**
     * Salva o documento
     */
    public function save(): void
    {
        PermissionMiddleware::check('documents.edit');

        $userId = (int) $_SESSION['user_id'];
        $isAdmin = PermissionMiddleware::can('admin.documents.full');

        // Bloqueia o salvamento se estiver suspenso e não for admin
        if (!$isAdmin) {
            $user = $this->userModel->find($userId);
            if ($user && $user->suspenso) {
                $_SESSION['error'] = "Sua conta possui pendências financeiras. Não é possível salvar novos documentos.";
                header("Location: " . BASE_URL . "/payments");
                exit;
            }
        }

        $data = [
            'id'            => $_POST['id'] ?? null,
            'user_id'       => $userId,
            'template_id'   => $_POST['template_id'],
            'titulo'        => $_POST['titulo'],
            'conteudo_html' => $_POST['conteudo_html']
        ];

        $id = $this->documentModel->save($data);

        // 📊 Incrementa uso apenas se NÃO for admin e for um NOVO documento
        if (!$data['id'] && !$isAdmin) {
            UsageService::increment();
        }

        header("Location: " . BASE_URL . "/editor/{$id}");
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
}