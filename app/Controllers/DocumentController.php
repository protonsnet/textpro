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
                
                $plan = (new \App\Models\PlanModel())->find((int)$subscription->plan_id);
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

        $this->view('client/editor', [
            'title'              => 'Editor TextPro',
            'document'           => $document,
            'content'            => $content,
            'currentTemplate'    => $currentTemplate,
            'availableTemplates' => $this->templateModel->all(),
            'availableFonts'     => $fontService->getFontNames()
        ]);
    }

    /**
     * Salva o documento
     */
    public function save(): void
    {
        PermissionMiddleware::check('documents.edit');

        $userId  = (int) $_SESSION['user_id'];
        $isAdmin = PermissionMiddleware::can('admin.documents.full');

        if (!$isAdmin) {
            $user = $this->userModel->find($userId);
            $subscription = $this->subscriptionModel->findActiveSubscription($userId);
            $hasAccess = $subscription && in_array(strtolower($subscription->status), ['active', 'trialing']);

            if ($user && $user->suspenso && !$hasAccess) {
                $_SESSION['error'] = "Sua conta possui pendências financeiras. Não é possível salvar.";
                header("Location: " . BASE_URL . "/payments");
                exit;
            }
        }

        $templateId = (int) ($_POST['template_id'] ?? 0);
        $titulo     = trim($_POST['titulo'] ?? '');
        $html       = $_POST['conteudo_html'] ?? '';

        if ($templateId <= 0 || $titulo === '') {
            $_SESSION['error'] = "Dados inválidos para salvar o documento.";
            header("Location: " . BASE_URL . "/editor");
            exit;
        }

        $html = $this->sanitizeEditorHtml($html);

        $data = [
            'id'              => $_POST['id'] ?? null,
            'user_id'         => $userId,
            'template_id'     => $templateId,
            'titulo'          => trim($_POST['titulo'] ?? ''),
            'subtitulo'        => trim($_POST['subtitulo'] ?? ''),
            'autor'            => trim($_POST['autor'] ?? ''),
            'instituicao'      => trim($_POST['instituicao'] ?? ''),
            'local_publicacao' => trim($_POST['local_publicacao'] ?? ''),
            'ano_publicacao'   => trim($_POST['ano_publicacao'] ?? ''),
            'conteudo_html'   => $html,
        ];

        $id = $this->documentModel->save($data);

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

}