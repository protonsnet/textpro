<?php

namespace App\Core;

class Controller
{
    public function __construct()
    {
        // Controller base — inicializações globais podem entrar aqui futuramente
    }

    /**
     * Renderiza uma view.
     *
     * @param string $view Caminho da view (ex: 'client/dashboard')
     * @param array  $data Variáveis disponíveis na view
     * @param bool   $useLayout Se deve usar o layout base
     */
    protected function view(string $view, array $data = [], bool $useLayout = true): void
    {
        // Disponibiliza variáveis para a view
        extract($data, EXTR_SKIP);

        $viewPath   = dirname(__DIR__) . "/Views/{$view}.php";
        $layoutPath = dirname(__DIR__) . "/Views/Layouts/base.php";

        if (!file_exists($viewPath)) {
            http_response_code(500);
            exit("View não encontrada: {$view}");
        }

        // Captura o conteúdo da view
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($useLayout) {
            if (!file_exists($layoutPath)) {
                http_response_code(500);
                exit('Layout base não encontrado.');
            }

            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Renderiza uma view pública (sem layout)
     */
    protected function viewPublic(string $view, array $data = []): void
    {
        $this->view($view, $data, false);
    }
}
