<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SystemUserModel;
use App\Services\PermissionService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    protected SystemUserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new SystemUserModel();
    }

    // =========================
    // CADASTRO
    // =========================

    public function registerForm(): void
    {
        $this->viewPublic('auth/register', [
            'title' => 'Cadastro TextPro'
        ]);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $nome           = trim($_POST['nome'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $resideBrasil = isset($_POST['reside_brasil']);
        $cpf_cnpj     = trim($_POST['cpf_cnpj'] ?? '');
        $telefone     = trim($_POST['telefone'] ?? '');
        $senha          = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if (!$nome || !$email || !$senha || !$telefone) {
            $this->viewPublic('auth/register', ['error' => 'Nome, Email, Telefone e Senha são obrigatórios.']);
            return;
        }

        if ($senha !== $confirmarSenha) {
            $this->viewPublic('auth/register', [
                'title' => 'Cadastro TextPro',
                'error' => 'As senhas não coincidem.'
            ]);
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $this->viewPublic('auth/register', [
                'title' => 'Cadastro TextPro',
                'error' => 'Este email já está cadastrado.'
            ]);
            return;
        }

        // CPF só é obrigatório se reside no Brasil
        if ($resideBrasil && !$cpf_cnpj) {
            $this->viewPublic('auth/register', ['error' => 'CPF/CNPJ é obrigatório para residentes no Brasil.']);
            return;
        }

        // Se estrangeiro, salvamos uma marcação no banco ou deixamos nulo
        $cpf_final = $resideBrasil ? $cpf_cnpj : 'ESTRANGEIRO';

        // Passando os novos campos para o método create
        if ($this->userModel->create($nome, $email, $senha, $cpf_cnpj, $telefone)) {
            $user = $this->userModel->findByEmail($email);

            $_SESSION['user_id']   = $user->id;
            $_SESSION['user_nome'] = $user->nome;
            $_SESSION['permissions'] = \App\Services\PermissionService::loadForUser($user->id);

            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $this->viewPublic('auth/register', [
            'title' => 'Cadastro TextPro',
            'error' => 'Erro ao cadastrar. Tente novamente.'
        ]);
    }

    // =========================
    // LOGIN
    // =========================

    public function loginForm(): void
    {
        $this->viewPublic('auth/login', [
            'title' => 'Login TextPro'
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($senha, $user->senha)) {
            $this->viewPublic('auth/login', [
                'title' => 'Login TextPro',
                'error' => 'Credenciais inválidas.'
            ]);
            return;
        }

        /**
         * =========================
         * INICIALIZA SESSÃO LIMPA
         * =========================
         */
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_nome'] = $user->nome;

        /**
         * =========================
         * CARREGA PERMISSÕES (RBAC)
         * =========================
         */
        $permissions = PermissionService::loadForUser($user->id);

        $_SESSION['permissions'] = $permissions;
        $_SESSION['permissions_loaded'] = true;

        $subModel = new \App\Models\SubscriptionModel();
        $sub = $subModel->findActiveSubscription($user->id);
        $_SESSION['plan_id'] = $sub ? $sub->plan_id : null;
        $_SESSION['user_suspenso'] = $user->suspenso;

        /**
         * =========================
         * REDIRECIONAMENTO CORRETO
         * =========================
         */
        if (in_array('admin.dashboard', $permissions, true)) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    // =========================
    // LOGOUT
    // =========================

    public function logout(): void
    {
        session_unset();
        session_destroy();

        header('Location: ' . BASE_URL . '/');
        exit;
    }

    // Exibe o formulário de "Esqueci minha senha"
    public function forgotPasswordForm(): void
    {
        $this->viewPublic('auth/forgot-password', [
            'title' => 'Recuperar Senha - TextPro'
        ]);
    }

    // Processa o pedido de recuperação de senha
    // 1. Recebe o e-mail e gera o token
    public function forgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $db = \App\Core\Database::getInstance();
            
            // Limpa tokens antigos e salva o novo
            $db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $db->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)")->execute([$email, $token]);

            $this->sendResetEmail($email, $token);
        }

        // Por segurança, mostramos a mesma mensagem mesmo se o e-mail não existir
        $this->viewPublic('auth/forgot-password', [
            'success' => 'Se o e-mail estiver cadastrado, você receberá um link de recuperação em instantes.'
        ]);
    }

    // 2. Exibe formulário de nova senha (GET /reset-password)
    public function resetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';
        $this->viewPublic('auth/reset-password', ['token' => $token]);
    }

    // 3. Processa a nova senha (POST /reset-password)
    public function resetPassword(): void
    {
        $token = $_POST['token'] ?? '';
        $novaSenha = $_POST['senha'] ?? '';
        $db = \App\Core\Database::getInstance();

        // Valida token (validade de 1 hora)
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([$token]);
        
        // O fetch aqui retorna um objeto stdClass
        $reset = $stmt->fetch(\PDO::FETCH_OBJ); 

        if ($reset) {
            // CORREÇÃO: Acessar como objeto ->email em vez de ['email']
            $user = $this->userModel->findByEmail($reset->email);
            
            if ($user) {
                $this->userModel->resetPassword($user->id, $novaSenha);
                
                // CORREÇÃO: Acessar como objeto ->email
                $db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$reset->email]);

                $this->viewPublic('auth/login', [
                    'success' => 'Senha atualizada! Faça login com a nova senha.'
                ]);
                return;
            }
        } 
        
        // Se chegou aqui, o token é inválido ou o usuário sumiu
        $this->viewPublic('auth/forgot-password', [
            'error' => 'Token inválido, expirado ou usuário não encontrado.'
        ]);
    }

    private function sendResetEmail($email, $token)
    {
        $mail = new PHPMailer(true);

        try {
            // Configurações do Servidor baseadas no seu .env atualizado
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];      // Estava SMTP_HOST
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USER'];      // Estava SMTP_USER
            $mail->Password   = $_ENV['MAIL_PASS'];      // Estava SMTP_PASS
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Para porta 465 (SSL)
            $mail->Port       = $_ENV['MAIL_PORT'];      // Estava 465 fixo
            $mail->CharSet    = 'UTF-8';

            // Destinatário
            $mail->setFrom($_ENV['MAIL_USER'], $_ENV['MAIL_FROM_NAME'] ?? 'TextPro Suporte');
            $mail->addAddress($email);

            // Pega o protocolo (http ou https)
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            // Pega o domínio (startupstogether.com)
            $domain = $_SERVER['HTTP_HOST'];

            // Conteúdo
            $url = $protocol . "://" . $domain . BASE_URL . "/reset-password?token=" . $token;
            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de Senha - TextPro';
            $mail->Body    = "Você solicitou a alteração de senha. Clique no link abaixo para cadastrar uma nova:<br><br>
                            <a href='{$url}'>{$url}</a><br><br>
                            Se não foi você, ignore este e-mail. Este link expira em 1 hora.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
            return false;
        }
    }
}
