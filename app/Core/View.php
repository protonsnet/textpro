<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }

    public static function renderWithLayout(string $view, array $data = []): void
    {
        extract($data);

        ob_start();
        require __DIR__ . "/../Views/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . '/../Views/Layouts/base.php';
    }
}
