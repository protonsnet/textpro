<?php

namespace App\Controllers;

class UploadController
{
    public function image()
    {
        if (!isset($_FILES['upload'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nenhum arquivo enviado']);
            return;
        }

        $file = $_FILES['upload'];

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

        $name = uniqid('img_', true) . '.png';
        move_uploaded_file($file['tmp_name'], $dir . $name);

        echo json_encode([
            'url' => BASE_URL . '/uploads/images/' . $name
        ]);
    }
}
