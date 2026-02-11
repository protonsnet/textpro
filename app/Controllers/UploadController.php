<?php

namespace App\Controllers;

class UploadController
{
    public function image()
    {
        header('Content-Type: application/json');

        if (!isset($_FILES['image'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nenhum arquivo enviado']);
            return;
        }

        $file = $_FILES['image'];

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            http_response_code(415);
            echo json_encode(['error' => 'Formato não permitido']);
            return;
        }

        $dir = APP_ROOT . '/public/uploads/images/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('img_', true) . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao salvar imagem']);
            return;
        }

        // --- AJUSTE AQUI ---
        // Certifique-se que BASE_URL está definido como "http://localhost:8080" no seu config
        // Removemos a barra extra para evitar "http://localhost:8080//uploads"
        $baseUrl = rtrim(BASE_URL, '/');
        $imageUrl = $baseUrl . '/uploads/images/' . $name;

        echo json_encode([
            'url' => $imageUrl
        ]);
    }
}