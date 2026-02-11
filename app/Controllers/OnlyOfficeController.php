<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Controller;
use App\Core\AuthMiddleware;
use App\Core\PermissionMiddleware;
use App\Policies\SubscriptionPolicy;
use App\Services\UsageService;
use App\Models\PlanModel;
use App\Models\SubscriptionModel;   

class OnlyOfficeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Garante que o usuário está logado em qualquer método
        // AuthMiddleware::check();
    }

    /**
     * Helper para verificar se é admin de forma segura
     */
    private function isAdmin()
    {
        return (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) || 
               PermissionMiddleware::can('admin.access');
    }
    
    public function index($id = null)
    {
        // Se acessar /editor-beta sem ID, redireciona para criar um novo
        if (!is_numeric($id)) {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];

        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            die("Documento não encontrado no banco de dados.");
        }

        $meuIpMac = "192.168.0.102";
        // Se o file_path estiver vazio no banco por algum motivo, usa o default
        $fileUrl = "http://{$meuIpMac}:8080" . ($doc->file_path ?? "/storage/documentos/default.docx");
        // Produção
        // $fileUrl = BASE_URL . ($doc->file_path ?? "/storage/documentos/default.docx");
        
        $config = [
            "document" => [
                "fileType" => "docx",
                // A KEY baseada no updated_at força o OnlyOffice a recarregar o arquivo se ele mudar no disco
                "key" => "DOC_" . $doc->id . "_" . strtotime($doc->updated_at),
                "title" => $doc->titulo,
                "url" => $fileUrl,
                "permissions" => [ "edit" => true, "download" => true ]
            ],
            "documentType" => "word",
            "editorConfig" => [
                "callbackUrl" => "http://{$meuIpMac}:8080/editor-beta/callback",
                // Produção
                // "callbackUrl" => BASE_URL . "/editor-beta/callback",
                "lang" => "pt-BR",
                "user" => [
                    "id" => (string)$userId,
                    "name" => $_SESSION['user_nome'] ?? 'Usuário'
                ],
                "customization" => [
                    "autosave" => true,
                    "forcesave" => true
                ]
            ],
            "height" => "100%",
            "width" => "100%"
        ];

        $config['token'] = $this->generateToken($config);

        return $this->view('editor-beta/index', [
            'config' => json_encode($config),
            'no_sidebar' => true, // Isso remove Header, Sidebar e Footer no seu base.php
            'title' => 'Editando: ' . $doc->titulo
        ]);
    }

    public function callback()
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        
        if (isset($data['status'])) {
            if ($data['status'] == 2 || $data['status'] == 6) {
                $downloadUrl = $data['url'];
                $parts = explode('_', $data['key']);
                $prefix = $parts[0]; 
                $documentId = $parts[1];

                if ($documentId) {
                    $ext = 'docx';
                    $filePrefix = 'doc_';
                    
                    if ($prefix === 'XLS') { $ext = 'xlsx'; $filePrefix = 'spreadsheet_'; }
                    if ($prefix === 'PPT') { $ext = 'pptx'; $filePrefix = 'presentation_'; }

                    $newData = file_get_contents($downloadUrl);
                    $relativeDir = "/storage/documentos/";
                    $fileName = $filePrefix . $documentId . "." . $ext;
                    
                    // AJUSTE AQUI: Usando DOCUMENT_ROOT para garantir o caminho na Hostinger
                    $savePath = $_SERVER['DOCUMENT_ROOT'] . $relativeDir . $fileName;

                    // Garante que a pasta existe antes de salvar
                    if (!is_dir(dirname($savePath))) {
                        mkdir(dirname($savePath), 0775, true);
                    }

                    if (file_put_contents($savePath, $newData)) {
                        $db = Database::getInstance();
                        $stmt = $db->prepare("UPDATE documents SET file_path = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$relativeDir . $fileName, $documentId]);
                    }
                }
            }
        }
        header("Content-Type: application/json");
        die(json_encode(["error" => 0]));
    }

    // public function callback()
    // {
    //     header('Content-Type: application/json');

    //     // =====================================================
    //     // 1. Ler corpo do callback
    //     // =====================================================
    //     $body = file_get_contents('php://input');

    //     // Log bruto (fundamental para debug)
    //     file_put_contents(
    //         APP_ROOT . '/storage/logs/onlyoffice_callback.log',
    //         date('Y-m-d H:i:s') . PHP_EOL . $body . PHP_EOL . PHP_EOL,
    //         FILE_APPEND
    //     );

    //     if (!$body) {
    //         return $this->response(0);
    //     }

    //     $data = json_decode($body, true);

    //     if (!is_array($data) || !isset($data['status'])) {
    //         return $this->response(0);
    //     }

    //     /**
    //      * Status importantes:
    //      * 2 = Documento pronto para salvar
    //      * 6 = Documento fechado com alterações
    //      */
    //     if (!in_array($data['status'], [2, 6], true)) {
    //         return $this->response(0);
    //     }

    //     if (empty($data['url']) || empty($data['key'])) {
    //         return $this->response(0);
    //     }

    //     // =====================================================
    //     // 2. Resolver ID e tipo pelo "key"
    //     // Exemplo: DOC_15 | XLS_22 | PPT_9
    //     // =====================================================
    //     [$prefix, $documentId] = explode('_', $data['key']) + [null, null];

    //     if (!$documentId) {
    //         return $this->response(0);
    //     }

    //     // =====================================================
    //     // 3. Definir extensão e prefixo de arquivo
    //     // =====================================================
    //     $ext = 'docx';
    //     $filePrefix = 'doc_';

    //     if ($prefix === 'XLS') {
    //         $ext = 'xlsx';
    //         $filePrefix = 'spreadsheet_';
    //     } elseif ($prefix === 'PPT') {
    //         $ext = 'pptx';
    //         $filePrefix = 'presentation_';
    //     }

    //     // =====================================================
    //     // 4. Baixar arquivo do OnlyOffice
    //     // =====================================================
    //     $fileContent = @file_get_contents($data['url']);

    //     if ($fileContent === false) {
    //         return $this->response(0);
    //     }

    //     // =====================================================
    //     // 5. Salvar no storage público
    //     // =====================================================
    //     $relativeDir = '/storage/documentos/';
    //     $fileName = $filePrefix . $documentId . '.' . $ext;
    //     $savePath = APP_ROOT . '/public' . $relativeDir . $fileName;

    //     if (!is_dir(dirname($savePath))) {
    //         mkdir(dirname($savePath), 0775, true);
    //     }

    //     if (!file_put_contents($savePath, $fileContent)) {
    //         return $this->response(0);
    //     }

    //     // =====================================================
    //     // 6. Atualizar banco
    //     // =====================================================
    //     $db = Database::getInstance();
    //     $stmt = $db->prepare(
    //         "UPDATE documents 
    //          SET file_path = ?, updated_at = NOW() 
    //          WHERE id = ?"
    //     );
    //     $stmt->execute([$relativeDir . $fileName, $documentId]);

    //     // =====================================================
    //     // 7. Resposta obrigatória para o OnlyOffice
    //     // =====================================================
    //     return $this->response(0);
    // }

    // private function response(int $error)
    // {
    //     echo json_encode(['error' => $error]);
    //     exit;
    // }
    
    public function create() 
    {
        AuthMiddleware::check();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        // Captura o título e remove espaços extras
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
        
        // Se o título estiver vazio após o trim, aí sim usa o padrão
        if (empty($titulo)) {
            $titulo = 'Novo Documento ' . date('d/m H:i');
        }

        $stmt = $db->prepare("INSERT INTO documents (user_id, titulo, template_id, created_at, updated_at) VALUES (?, ?, NULL, NOW(), NOW())");
        $stmt->execute([$userId, $titulo]);
        
        $id = $db->lastInsertId();

        $fileName = "doc_{$id}.docx";
        $relativeFilePath = "/storage/documentos/" . $fileName;
        
        // AJUSTE AQUI: Caminho absoluto Hostinger
        $absoluteFilePath = $_SERVER['DOCUMENT_ROOT'] . $relativeFilePath;
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . "/storage/documentos/";

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0775, true);
        }

        $defaultDoc = $storageDir . "default.docx";
        
        if (!file_exists($defaultDoc)) {
            $this->createDefaultDocIfNotExists($defaultDoc);
        }

        copy($defaultDoc, $absoluteFilePath);

        if (!copy($defaultDoc, $absoluteFilePath)) {
            die("Erro crítico: Não foi possível copiar o arquivo para $absoluteFilePath. Verifique permissões da pasta.");
        }

        if (!file_exists($absoluteFilePath)) {
            die("Erro: O arquivo deveria existir em $absoluteFilePath, mas não foi encontrado.");
        }

        $db->prepare("UPDATE documents SET file_path = ? WHERE id = ?")
        ->execute([$relativeFilePath, $id]);

        header("Location: " . BASE_URL . "/editor-beta/" . $id);
        exit;
    }

    private function createDefaultDocIfNotExists($path) {
        if (file_exists($path)) return true;

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // String Base64 completa de um DOCX vazio
        $emptyDoc = base64_decode("UEsDBBQAAAAIAAAAIQDAn7W9XgEAAJ0DAAALAAAAX3JlbHMvLnJlbHOskD1PwzAMhu9I/IfIeyNtoIQQErsDhMQHiN0m9toP1YmS9u9PAnVAAsHe7H6973un8/XGdfTExatPrBclK8HR6mDdtS7eD0/LK6icKNuYvWfXOGHAt739/fyeS6mF0id2Ucl8T6W0p9iIsR86kcl86GTSO6ZisDEn86GfVd10mO9m9AaFpZpXLuatKqD7F7Onv3m+69oB+T6Tf0beS7KExBAnmS17pXm6E6LSkX7D4N6I708E15D7O9LzBeMNo5mG3BshnF9BfMNo4X0D8Y3RzGf/AAAA//8DAFBLAwQUAAYACAAAACEA777P+v0AAAC6AgAAEAAAAGRvY1Byb3BzL2FwcC54bWwnsE7DMBC7V+I7RL5be6mKqI0qEhByC8R99YmbeInXtmxrQuvbsSChIc72z7NnZ3f7atM7FBYfveO1LFlpBNo6773ZOfv8fHoFpSPSmN1Hcs6ZHe7u7m52RzUInNkx0V5InPPWObGMcI05UsqN0fInX6XSeT652E206rXmE0pXNa9czGZ9CO3vSUnv8vKqS+DkG3m/9XwWZXm9S7uI9iL8S7L+AwAA//8DAFBLAwQUAAYACAAAACEAsG5D5u8AAAD8AgAAEQAAAGRvY1Byb3BzL2NvcmUueG1sInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAwQUAAYACAAAACEAsG5D5u8AAAD8AgAAEQAAAHdvcmQvZG9jdW1lbnQueG1sInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAwQUAAYACAAAACEA777P+v0AAAC6AgAAEgAAAHdvcmQvX3JlbHMvZG9jdW1lbnQueG1sLnyM7DMAMvAv9ByO5oO90NFAgxsjtASHzAYOskofYpTlYnaX9fAnVAAsFdb3f3nO9H69ujY/TExetPrBclK8HR6mDdtS7ef88rKLyo25i9Z9c4YcC3vX36+Z1LqYXSJ3ZRynxPpbSn2ImxHzqRyXzoZNI7pqK3MSfzoZ9V3XSY72b0BoWlmleeLltVQPcvZk9v83zXdQDyfSbvS95LsoTEECeZLfuleboTotKRfsNg3og3D8Q3jGY+uwAAAP//AwBQSwECLQAUAAAACAAAACEA777P+v0AAAC6AgAAEAAAAAAAAAAAAAAAAAAAAAAAX3JlbHMvLnJlbHNQSwECLQAUAAYACAAAACEA777P+v0AAAC6AgAAEAAAAAAAAAAAAAAAAAAAAAAAZG9jUHJvcHMvYXBwLnhtbFBLAQItABQABgAIAAAAIQDvvs/6/QAAALoCAAAREAAAAAAAAAAAAAAAAAAAAAAAZG9jUHJvcHMvY29yZS54bWxQSwECLQAUAAYACAAAACEAsG5D5u8AAAD8AgAAEQAAAAAAAAAAAAAAAAAAAAAAAHdvcmQvZG9jdW1lbnQueG1sUEsBAi0AFAAGAAgAAAAhAO++z/r9AAAAugIAABIAAAAAAAAAAAAAAAAAAAAAAAB3b3JkL19yZWxzL2RvY3VtZW50LnhtbC5yZWxzUEsFBgAAAAAFAAUAtwEAAF0CAAAAAA==");
        return file_put_contents($path, $emptyDoc) !== false;
    }

    private function generateToken($payload)
    {
        $secret = "hQ5llgVzCIkpV01EA7DbU7KOln4nU5hT"; // Sua chave encontrada no Docker
        
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function list()
    {
        AuthMiddleware::check(); // Protege a listagem
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        // Captura o termo de busca via GET
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // CORREÇÃO: Adicionado 'file_path' no SELECT para que a View possa identificar o tipo de arquivo
        $query = "SELECT id, titulo, file_path, updated_at FROM documents 
                WHERE user_id = ? AND file_path IS NOT NULL";
        $params = [$userId];

        // Se houver busca, adiciona o filtro à query
        if (!empty($search)) {
            $query .= " AND titulo LIKE ?";
            $params[] = "%{$search}%";
        }

        $query .= " ORDER BY updated_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $documents = $stmt->fetchAll();

        return $this->view('documents/list', [
            'documents' => $documents,
            'searchTerm' => $search, // Passamos o termo de volta para a view
            'title' => 'Meus Arquivos'
        ]);
    }

    private function createDefaultXlsxIfNotExists($path) {
        if (file_exists($path)) return true;

        // String Base64 de um arquivo XLSX (Excel) em branco padrão
        $emptyXlsx = base64_decode("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");
        file_put_contents($path, $emptyXlsx);
    }

    
    public function spreadsheet($id)
    {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];

        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            die("Documento não encontrado.");
        }

        // Use o IP da sua máquina para que o Docker consiga "enxergar" o servidor PHP
        $meuIp = "192.168.0.102";
        $porta = "8080";
        
        // CORREÇÃO DA URL: Removida a concatenação duplicada
        $fileUrl = "http://{$meuIp}:{$porta}" . ($doc->file_path ?? "/storage/documentos/default.xlsx");
        
        $config = [
            "document" => [
                "fileType" => "xlsx",
                // A KEY deve ser única. Se mudar o arquivo, mude a key para forçar reload
                "key" => "XLS_" . $doc->id . "_" . strtotime($doc->updated_at),
                "title" => $doc->titulo,
                "url" => $fileUrl ,
                "permissions" => [ "edit" => true, "download" => true ]
            ],
            "documentType" => "cell", 
            "editorConfig" => [
                "callbackUrl" => "http://{$meuIp}:{$porta}/editor-beta/callback",
                "lang" => "pt-BR",
                "user" => [
                    "id" => (string)$userId,
                    "name" => $_SESSION['user_nome'] ?? 'Usuário'
                ],
                "customization" => [
                    "autosave" => true,
                    "forcesave" => true
                ]
            ],
            "height" => "100%",
            "width" => "100%"
        ];

        $config['token'] = $this->generateToken($config);

        return $this->view('editor-beta/index', [
            'config' => json_encode($config),
            'no_sidebar' => true,
            'title' => 'Editando Planilha: ' . $doc->titulo
        ]);
    }

    public function createXlsx() 
    {
        AuthMiddleware::check();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
        
        if (empty($titulo)) {
            $titulo = 'Nova Planilha ' . date('d/m H:i');
        }

        $stmt = $db->prepare("INSERT INTO documents (user_id, titulo, template_id, created_at, updated_at) VALUES (?, ?, NULL, NOW(), NOW())");
        $stmt->execute([$userId, $titulo]);
        
        $id = $db->lastInsertId();

        $fileName = "spreadsheet_{$id}.xlsx"; 
        $relativeFilePath = "/storage/documentos/" . $fileName;

        // AJUSTE PARA HOSTINGER
        $absoluteFilePath = $_SERVER['DOCUMENT_ROOT'] . $relativeFilePath;
        $defaultXlsx = $_SERVER['DOCUMENT_ROOT'] . "/storage/documentos/default.xlsx";

        if (!is_dir(dirname($absoluteFilePath))) {
            mkdir(dirname($absoluteFilePath), 0775, true);
        }
        
        if (!file_exists($defaultXlsx)) {
            $this->createDefaultXlsxIfNotExists($defaultXlsx); // Certifique-se que esta função cria um XLSX real
        }

        copy($defaultXlsx, $absoluteFilePath);

        $db->prepare("UPDATE documents SET file_path = ? WHERE id = ?")
        ->execute([$relativeFilePath, $id]);
        
        // Redireciona para a rota de planilha
        header("Location: " . BASE_URL . "/editor-beta/spreadsheet/" . $id);
        exit;
    }

    /**
     * Abre o editor de apresentações (PowerPoint)
     */
    public function presentation($id)
    {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];

        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            die("Apresentação não encontrada.");
        }

        $meuIp = "192.168.0.102";
        $porta = "8080";
        
        $fileUrl = "http://{$meuIp}:{$porta}" . ($doc->file_path ?? "/storage/documentos/default.pptx");
        
        $config = [
            "document" => [
                "fileType" => "pptx",
                "key" => "PPT_" . $doc->id . "_" . strtotime($doc->updated_at),
                "title" => $doc->titulo,
                "url" => $fileUrl,
                "permissions" => [ "edit" => true, "download" => true ]
            ],
            "documentType" => "slide", // Tipo específico para OnlyOffice Slides
            "editorConfig" => [
                "callbackUrl" => "http://{$meuIp}:{$porta}/editor-beta/callback",
                "lang" => "pt-BR",
                "user" => [
                    "id" => (string)$userId,
                    "name" => $_SESSION['user_nome'] ?? 'Usuário'
                ],
                "customization" => ["autosave" => true, "forcesave" => true]
            ],
            "height" => "100%",
            "width" => "100%"
        ];

        $config['token'] = $this->generateToken($config);

        return $this->view('editor-beta/index', [
            'config' => json_encode($config),
            'no_sidebar' => true,
            'title' => 'Editando Slide: ' . $doc->titulo
        ]);
    }

    /**
     * Cria um novo arquivo de Slide (PPTX)
     */
    public function createPptx() 
    {
        AuthMiddleware::check();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
        
        if (empty($titulo)) {
            $titulo = 'Nova Apresentação ' . date('d/m H:i');
        }

        $stmt = $db->prepare("INSERT INTO documents (user_id, titulo, template_id, created_at, updated_at) VALUES (?, ?, NULL, NOW(), NOW())");
        $stmt->execute([$userId, $titulo]);
        
        $id = $db->lastInsertId();

        $fileName = "presentation_{$id}.pptx"; 
        $relativeFilePath = "/storage/documentos/" . $fileName;
        $absoluteFilePath = $_SERVER['DOCUMENT_ROOT'] . $relativeFilePath;
        $defaultPptx = $_SERVER['DOCUMENT_ROOT'] . "/storage/documentos/default.xlsx";

        if (!is_dir(dirname($absoluteFilePath))) {
            mkdir(dirname($absoluteFilePath), 0775, true);
        }
        
        if (!file_exists($defaultPptx)) {
            $this->createDefaultPptxIfNotExists($defaultPptx);
        }

        copy($defaultPptx, $absoluteFilePath);

        $db->prepare("UPDATE documents SET file_path = ? WHERE id = ?")
        ->execute([$relativeFilePath, $id]);
        
        header("Location: " . BASE_URL . "/editor-beta/presentation/" . $id);
        exit;
    }

    /**
     * Gera um arquivo PPTX em branco (Base64)
     */
    private function createDefaultPptxIfNotExists($path) {
        if (file_exists($path)) return true;
        // Base64 de um PPTX minimalista em branco
        $emptyPptx = base64_decode("UEsDBBQAAAAIAAAAIQAn9utpSgEAAJ0DAAALAAAAX3JlbHMvLnJlbHOskD1PwzAMhu9I/IfIeyNtoIQQErsDhMQHiN0m9toP1YmS9u9PAnVAAsHe7H6973un8/XGdfTExatPrBclK8HR6mDdtS7eD0/LK6icKNuYvWfXOGHAt739/fyeS6mF0id2Ucl8T6W0p9iIsR86kcl86GTSO6ZisDEn86GfVd10mO9m9AaFpZpXLuatKqD7F7Onv3m+69oB+T6Tf0beS7KExBAnmS17pXm6E6LSkX7D4N6I708E15D7O9LzBeMNo5mG3BshnF9BfMNo4X0D8Y3RzGf/AAAA//8DAFBLAwQUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAGRvY1Byb3BzL2NvcmUueG1sInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAwQUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAHBwdC9wcmVzZW50YXRpb24ueG1sInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAwQUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAFtDb250ZW50X1R5cGVzXS54bWxInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAQItABQAAAAIAAAAIQAn9utpSgEAAJ0DAAALAAAAAAAAAAAAAAAAAAAAAAAX3JlbHMvLnJlbHNQSwECLQAUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAAAAAAAAAAAAAAAAAAAAAGRvY1Byb3BzL2NvcmUueG1sUEsBAi0AFAAGAAgAAAAhALRuQ+bvAAAA/AIAABUAAAAAAAAAAAAAAAAAAAAAAABwcHQvcHJlc2VudGF0aW9uLnhtbFBLAQItABQABgAIAAAAIALRuQ+bvAAAA/AIAABIAAAAAAAAAAAAAAAAAAAAAAABbDb250ZW50X1R5cGVzXS54bWxQSwUGAAAAAAYABgBvAQAAawIAAAAA");
        return file_put_contents($path, $emptyPptx) !== false;
    }
    

}