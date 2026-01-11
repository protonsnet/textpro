<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Models\SystemUserModel;

class ProfileController extends Controller
{
    protected SystemUserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::check();
        $this->userModel = new SystemUserModel();
    }

    public function index(): void
    {
        $user = $this->userModel->find($_SESSION['user_id']);
        $this->view('client/profile', [
            'title' => 'Meu Perfil',
            'user'  => $user
        ]);
    }

    public function update(): void
    {
        $userId = $_SESSION['user_id'];
        $data = [
            'nome'     => $_POST['nome'],
            'email'    => $_POST['email'],
            'cpf_cnpj' => $_POST['cpf_cnpj'],
            'telefone' => $_POST['telefone'],
        ];

        // Processamento da Foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $newName = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = 'uploads/profiles/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $newName)) {
                $data['foto'] = $newName;
            }
        }

        if ($this->userModel->update($userId, $data)) {
            $_SESSION['user_nome'] = $data['nome']; // Atualiza nome na sessão
            $_SESSION['success'] = "Perfil atualizado com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao atualizar perfil.";
        }

        header("Location: " . BASE_URL . "/profile");
        exit;
    }
}