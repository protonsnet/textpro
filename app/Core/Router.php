<?php

namespace App\Core;

class Router {

    protected array $routes = [];

    /**
     * Adiciona uma rota genérica
     */
    public function add(string $method, string $uri, string $controllerAction): void
    {
        // Converte /rota/{id} ou /rota/{qualquer_coisa} em regex capturável
        // O padrão agora aceita letras, números, hifens e underscores
        $uri = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $uri);
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'controllerAction' => $controllerAction
        ];
    }

    /**
     * Resolve o erro "Call to undefined method match()"
     * Permite rotas que aceitam múltiplos métodos, ex: GET|POST
     */
    public function match(string $methods, string $uri, string $controllerAction): void
    {
        $methodsArray = explode('|', strtoupper($methods));
        foreach ($methodsArray as $method) {
            $this->add($method, $uri, $controllerAction);
        }
    }

    public function run(): void
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // 1. LIMPEZA DA URI PARA XAMPP
        if (defined('BASE_URL') && BASE_URL !== '/') {
            // Remove o BASE_URL (/textpro) do início da string
            if (strpos($requestUri, BASE_URL) === 0) {
                $requestUri = substr($requestUri, strlen(BASE_URL));
            }
        }

        // 2. NORMALIZAÇÃO (Remove / do final e garante que comece com /)
        $uri = ($requestUri !== '/') ? rtrim($requestUri, '/') : '/';
        if (empty($uri)) $uri = '/';

        foreach ($this->routes as $route) {
            // Prepara a regex para comparar a URI
            $pattern = "#^{$route['uri']}$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                
                array_shift($matches); // Remove o match completo da URI
                $params = $matches;

                [$controller, $action] = explode('@', $route['controllerAction']);
                $controllerClass = "App\\Controllers\\{$controller}";

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    echo "Erro 500: Controller {$controllerClass} não encontrado.";
                    return;
                }

                $instance = new $controllerClass();

                if (!method_exists($instance, $action)) {
                    http_response_code(500);
                    echo "Erro 500: Método {$action} não encontrado em {$controllerClass}.";
                    return;
                }

                // Executa o método do Controller passando os parâmetros capturados
                call_user_func_array([$instance, $action], $params);
                return;
            }
        }

        // Se nada coincidir
        http_response_code(404);
        echo "404 - Página não encontrada no TextPro.";
    }

    // =============================================================
    // MÉTODOS DE ATALHO
    // =============================================================

    public function get(string $uri, string $controllerAction): void
    {
        $this->add('GET', $uri, $controllerAction);
    }

    public function post(string $uri, string $controllerAction): void
    {
        $this->add('POST', $uri, $controllerAction);
    }
}