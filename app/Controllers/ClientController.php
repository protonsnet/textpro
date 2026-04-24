<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Services\PdfService;
use App\Models\DocumentModel;
use App\Models\TemplateModel;
use App\Models\PlanModel;
use App\Models\SubscriptionModel;
use App\Models\SystemUserModel;
use App\Services\SubscriptionSyncService;

class ClientController extends Controller
{
    protected DocumentModel $documentModel;
    protected TemplateModel $templateModel;
    protected PdfService $pdfService;
    protected SubscriptionModel $subscriptionModel;
    protected PlanModel $planModel;

    public function __construct()
    {
        parent::__construct();

        AuthMiddleware::check();

        $this->documentModel     = new DocumentModel();
        $this->templateModel     = new TemplateModel();
        $this->pdfService        = new PdfService();
        $this->subscriptionModel = new SubscriptionModel();
        $this->planModel         = new PlanModel();
    }

    public function dashboard(): void
    {
        AuthMiddleware::check();
        $userId = $_SESSION['user_id'];

        SubscriptionSyncService::checkAndSync($userId);

        // Buscamos todos os documentos
        $documents = $this->documentModel->findByUser($userId);
        
        $subscription = $this->subscriptionModel->findActiveSubscription($userId);
        $currentPlan  = $subscription ? $this->planModel->find($subscription->plan_id) : null;

        $this->view('client/dashboard', [
            'title'        => 'Meus Documentos',
            'documents'    => $documents, // Passamos a lista completa
            'subscription' => $subscription,
            'currentPlan'  => $currentPlan,
            'user_name'    => $_SESSION['user_nome'] ?? 'Cliente'
        ]);
    }
    
    /**
     * Exibe o formulário de alteração de senha
     */
    public function passwordForm(): void
    {
        $this->view('client/change_password', [
            'title' => 'Alterar Senha - NexoWriter'
        ]);
    }

    /**
     * Processa a alteração de senha
     */
    public function updatePassword(): void
    {
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $userModel = new SystemUserModel();
        $user = $userModel->find($userId);

        // 1. Validar senha atual
        if (!password_verify($currentPassword, $user->senha)) {
            $_SESSION['error'] = "A senha atual está incorreta.";
            header("Location: " . BASE_URL . "/profile/password");
            exit;
        }

        // 2. Validar se as senhas coincidem
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "A nova senha e a confirmação não coincidem.";
            header("Location: " . BASE_URL . "/profile/password");
            exit;
        }

        // 3. Validar força mínima (exemplo: 6 caracteres)
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = "A nova senha deve ter pelo menos 6 caracteres.";
            header("Location: " . BASE_URL . "/profile/password");
            exit;
        }

        // 4. Atualizar no banco
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $userModel->update($userId, ['senha' => $hashedPassword]);

        $_SESSION['success'] = "Senha alterada com sucesso!";
        header("Location: " . BASE_URL . "/profile/password");
        exit;
    }


    public function rename()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $novoTitulo = trim($_POST['titulo'] ?? '');
            $userId = $_SESSION['user_id'];

            if ($id && !empty($novoTitulo)) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare("UPDATE documents SET titulo = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->execute([$novoTitulo, $id, $userId]);
                
                // Retorno para o Fetch do JavaScript
                header('Content-Type: application/json');
                echo json_encode(['success' => 'Arquivo renomeado com sucesso!']);
                exit;
            }
        }
    }
}
