<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\PermissionMiddleware;
use App\Models\SystemUserModel;
use App\Core\Database;
use App\Models\UserRoleModel;
use App\Models\RoleModel;
use App\Models\SubscriptionModel;

class AdminUserController extends Controller
{
    protected SystemUserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new SystemUserModel();
    }

    public function index(): void
    {
        PermissionMiddleware::check('users.manage');

        $this->view('admin/users/list', [
            'users'   => $this->userModel->all(),
            'title'   => 'Gerenciar Usuários',
            'success' => $_SESSION['success'] ?? null,
            'error'   => $_SESSION['error'] ?? null,
        ]);
        
        // Limpa mensagens da sessão após exibir
        unset($_SESSION['success'], $_SESSION['error']);
    }

    /**
     * Formulário de edição de usuário com todos os campos
     */
    public function editForm($id)
    {
        PermissionMiddleware::check('users.manage');
        $user = $this->userModel->find((int) $id);
        
        // Busca todas as roles e as roles atuais do usuário
        $roleModel = new RoleModel(); // Crie este model se não existir
        $userRoleModel = new UserRoleModel();
        
        $this->view('admin/users/edit', [
            'user'       => $user,
            'allRoles'   => $roleModel->all(),
            'userRoles'  => $userRoleModel->getRoleIdsByUser((int)$id),
            'title'      => 'Editar Usuário: ' . $user->nome
        ]);
    }

    /**
     * Atualiza dados do usuário incluindo Foto, País, Telefone e Status
     */
    public function update($id)
    {
        PermissionMiddleware::check('users.manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . "/admin/users/edit/{$id}");
            exit;
        }

        $id = (int)$id;
        $data = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'cpf_cnpj' => trim($_POST['cpf_cnpj'] ?? null),
            'pais'     => trim($_POST['pais'] ?? 'BR'),
            'ativo'    => isset($_POST['ativo']) ? 1 : 0,
            'suspenso' => isset($_POST['suspenso']) ? 1 : 0
        ];

        if (empty($data['nome']) || empty($data['email'])) {
            $_SESSION['error'] = "Nome e email são obrigatórios";
            header("Location: " . BASE_URL . "/admin/users/edit/{$id}");
            exit;
        }

        // Lógica de Upload de Foto
        if (!empty($_FILES['foto']['name'])) {
            $uploadDir = APP_ROOT . '/uploads/profiles/';
            
            // Cria diretório se não existir
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fileName = "user_" . $id . "_" . time() . "." . $ext;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fileName)) {
                $data['foto'] = $fileName;
            }
        }

        $roleIds = $_POST['roles'] ?? []; // Array vindo de checkboxes no HTML
        $userRoleModel = new UserRoleModel();
        $userRoleModel->syncRoles($id, $roleIds);

        if ($this->userModel->update($id, $data)) {
            $_SESSION['success'] = "Usuário atualizado com sucesso";
        } else {
            $_SESSION['error'] = "Erro ao atualizar banco de dados";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function activate(int $id): void
    {
        PermissionMiddleware::check('users.manage');
        $this->userModel->update($id, ['ativo' => 1]);
        
        $_SESSION['success'] = "Usuário ativado";
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function deactivate(int $id): void
    {
        PermissionMiddleware::check('users.manage');

        if ($this->isSelf($id)) {
            $_SESSION['error'] = "Você não pode desativar sua própria conta";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->userModel->update($id, ['ativo' => 0]);
        
        $_SESSION['success'] = "Usuário desativado";
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function store()
    {
        PermissionMiddleware::check('users.manage');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/users/create');
            exit;
        }

        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $senha    = $_POST['senha'] ?? '';
        $telefone = trim($_POST['telefone'] ?? '');
        $cpf_cnpj = trim($_POST['cpf_cnpj'] ?? null);
        $pais     = trim($_POST['pais'] ?? 'BR');
        $ativo    = isset($_POST['ativo']) ? 1 : 0;
        $suspenso = isset($_POST['suspenso']) ? 1 : 0;

        // Validação básica
        if (!$nome || !$email || !$senha) {
            header('Location: ' . BASE_URL . '/admin/users/create?error=Nome, Email e Senha são obrigatórios');
            exit;
        }

        // Verifica duplicidade
        if ($this->userModel->findByEmail($email)) {
            header('Location: ' . BASE_URL . '/admin/users/create?error=Este email já está cadastrado');
            exit;
        }

        // Tenta criar o usuário primeiro para obter o ID (necessário para nomear a foto)
        // Se o seu model create retornar o ID, use-o. Caso contrário, adaptamos:
        $userId = $this->userModel->create($nome, $email, $senha, $cpf_cnpj, $telefone);

        if ($userId) {
            $updateData = [
                'pais'     => $pais,
                'ativo'    => $ativo,
                'suspenso' => $suspenso
            ];

            // Lógica de Upload de Foto (semelhante ao seu método update)
            if (!empty($_FILES['foto']['name'])) {
                $uploadDir = APP_ROOT . '/uploads/profiles/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $fileName = "user_" . $userId . "_" . time() . "." . $ext;
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fileName)) {
                    $updateData['foto'] = $fileName;
                }
            }

            // Atualiza os campos adicionais que o método create padrão pode não contemplar
            $this->userModel->update($userId, $updateData);

            header('Location: ' . BASE_URL . '/admin/users?success=Usuário criado com sucesso');
            exit;
        }

        header('Location: ' . BASE_URL . '/admin/users/create?error=Erro ao criar usuário no banco');
        exit;
    }

    public function create()
    {
        PermissionMiddleware::check('users.manage');
        $this->view('admin/users/create', ['title' => 'Novo Usuário']);
    }

    public function resetPassword(int $id): void
    {
        PermissionMiddleware::check('users.manage');

        if ($this->isSelf($id)) {
            $_SESSION['error'] = "Use seu perfil para alterar a própria senha";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $novaSenha = bin2hex(random_bytes(4));
        $this->userModel->resetPassword($id, $novaSenha);

        $_SESSION['success'] = "Senha resetada! Nova senha: " . $novaSenha;
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    private function isSelf(int $id): bool
    {
        return (int)($_SESSION['user_id'] ?? 0) === $id;
    }

    /**
     * Ativa ou renova manualmente o período de acesso/trial do usuário
     */
    public function giveTrial($id)
    {
        PermissionMiddleware::check('users.manage');

        $id = (int)$id;
        $days = (int)($_POST['trial_days'] ?? 7);

        // 1. Cria a assinatura no banco (Model de assinaturas)
        $subscriptionModel = new SubscriptionModel();
        $subscriptionModel->giveManualTrial($id, $days);

        // 2. ATRIBUI A ROLE DE CLIENTE (IMPORTANTE)
        // Supondo que o ID da role 'Cliente' seja 2. 
        // Isso garante que ele passe pelo PermissionMiddleware::can('documents.edit')
        $userRoleModel = new UserRoleModel();
        $userRoleModel->assign($id, 2); // Ajuste o '2' para o ID correto da sua role de usuário comum

        // 3. Remove suspensão e ativa o usuário
        $this->userModel->update($id, ['suspenso' => 0, 'ativo' => 1]);

        // 4. Invalida o cache de permissões do usuário caso ele esteja logado
        // Se o usuário 4 estiver logado agora, a sessão dele precisa ser renovada
        // Se você tiver uma lógica de refresh global, chame aqui.

        $_SESSION['success'] = "Assinatura Trial criada e permissões atribuídas com sucesso!";
        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }
}