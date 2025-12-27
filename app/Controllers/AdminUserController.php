<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\PermissionMiddleware;
use App\Models\SystemUserModel;

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
        if (!$user) {
            $_SESSION['error'] = "Usuário não encontrado";
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $this->view('admin/users/edit', [
            'user'  => $user,
            'title' => 'Editar Usuário: ' . $user->nome
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
            $uploadDir = APP_ROOT . '/public/uploads/profiles/';
            
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

        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (!$nome || !$email || !$senha) {
            header('Location: ' . BASE_URL . '/admin/users/create?error=Preencha todos os campos');
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            header('Location: ' . BASE_URL . '/admin/users/create?error=Email já cadastrado');
            exit;
        }

        // O método create agora suporta telefone e CPF
        $this->userModel->create($nome, $email, $senha, null, '');

        header('Location: ' . BASE_URL . '/admin/users?success=Usuário criado com sucesso');
        exit;
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
}