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

        $stmt = $db->prepare("
            SELECT d.*, ds.can_edit as shared_edit 
            FROM documents d
            LEFT JOIN document_shares ds ON d.id = ds.document_id AND ds.user_id = ?
            WHERE d.id = ? AND (d.user_id = ? OR ds.user_id = ?)
        ");
        // $stmt->execute([$id, $userId]);
        $stmt->execute([$userId, $id, $userId, $userId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            die("Documento não encontrado no banco de dados.");
        }
        
        $canEdit = ($doc->user_id == $userId) || ($doc->shared_edit == 1);

        // $meuIpMac = "192.168.0.102";
        // Se o file_path estiver vazio no banco por algum motivo, usa o default
        // $fileUrl = "http://{$meuIpMac}:8080" . ($doc->file_path ?? "/storage/documentos/default.docx");
        // Produção
        $baseUrl = "https://nexowriter.com"; 
    
        $fileUrl = $baseUrl . ($doc->file_path ?? "/public/storage/documentos/default.docx");
        $callbackUrl = $baseUrl . "/editor-beta/callback";
        
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
                "callbackUrl" => $callbackUrl,
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

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");

        $config['token'] = $this->generateToken($config);

        return $this->view('editor-beta/index', [
            'config' => json_encode($config),
            'no_sidebar' => true, // Isso remove Header, Sidebar e Footer no seu base.php
            'title' => 'Editando: ' . $doc->titulo
        ], false);
    }

    public function callback()
    {
        if (ob_get_length()) ob_clean();
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $logPath = $_SERVER['DOCUMENT_ROOT'] . "/public/storage/documentos/debug_log.txt";
        
        // Log inicial de entrada
        $status = $data['status'] ?? 'N/A';
        // file_put_contents($logPath, "\n--- NOVA REQUISIÇÃO " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);
        // file_put_contents($logPath, "Status: $status\n", FILE_APPEND);

        if (isset($data['status']) && ($data['status'] == 2 || $data['status'] == 6)) {
            $parts = explode('_', $data['key']);
            $documentId = $parts[1] ?? null;

            if ($documentId) {
                $db = Database::getInstance();
                $stmt = $db->prepare("SELECT file_path FROM documents WHERE id = ?");
                $stmt->execute([$documentId]);
                $doc = $stmt->fetch();

                if ($doc) {
                    $downloadUrl = $data['url'];
                    $savePath = $_SERVER['DOCUMENT_ROOT'] . $doc->file_path;

                    // file_put_contents($logPath, "Download URL: $downloadUrl\n", FILE_APPEND);
                    // file_put_contents($logPath, "Destino Local: $savePath\n", FILE_APPEND);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $downloadUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
                    
                    // Ativa a captura dos headers para ver o que o servidor do OnlyOffice responde
                    curl_setopt($ch, CURLOPT_HEADER, true); 

                    $response = curl_exec($ch);
                    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                    $header = substr($response, 0, $headerSize);
                    $newData = substr($response, $headerSize);
                    
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    curl_close($ch);

                    // file_put_contents($logPath, "HTTP Code: $httpCode\n", FILE_APPEND);
                    // file_put_contents($logPath, "Headers da VPS:\n$header\n", FILE_APPEND);

                    if ($newData !== false && $httpCode == 200 && strlen($newData) > 100) {
                        if (file_exists($savePath)) @unlink($savePath);
                        if (file_put_contents($savePath, $newData)) {
                            chmod($savePath, 0666);
                            if ($data['status'] == 2) {
                                $db->prepare("UPDATE documents SET updated_at = NOW() WHERE id = ?")->execute([$documentId]);
                                // file_put_contents($logPath, "RESULTADO: Sucesso ao gravar doc_$documentId\n", FILE_APPEND);
                            }
                        }
                    } else {
                        file_put_contents($logPath, "RESULTADO: Falha. Tamanho dados: " . strlen($newData) . " bytes. Erro cURL: $curlError\n", FILE_APPEND);
                    }
                }
            }
        }

        header("Content-Type: application/json");
        echo json_encode(["error" => 0]);
        exit;
    }

    private function response(int $error)
    {
        echo json_encode(['error' => $error]);
        exit;
    }
    

    private function createDefaultDocIfNotExists($path) {
        if (file_exists($path)) return true;

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // String Base64 completa de um DOCX vazio
        $emptyDoc = base64_decode("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");
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
        // $meuIp = "192.168.0.102";
        // $porta = "8080";
        
        // // CORREÇÃO DA URL: Removida a concatenação duplicada
        // $fileUrl = "http://{$meuIp}:{$porta}" . ($doc->file_path ?? "/storage/documentos/default.xlsx");
        $baseUrl = "https://nexowriter.com"; 
    
        $fileUrl = $baseUrl . ($doc->file_path ?? "/public/storage/documentos/default.xlsx");
        $callbackUrl = $baseUrl . "/editor-beta/callback";
        
        $config = [
            "document" => [
                "fileType" => "xlsx",
                // A KEY deve ser única. Se mudar o arquivo, mude a key para forçar reload
                "key" => "XLS_" . $doc->id . "_" . strtotime($doc->updated_at),
                "title" => $doc->titulo,
                "url" => $fileUrl,
                "permissions" => [ "edit" => true, "download" => true ]
            ],
            "documentType" => "cell", 
            "editorConfig" => [
                "callbackUrl" => $callbackUrl,
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

        // $meuIp = "192.168.0.102";
        // $porta = "8080";
        
        // $fileUrl = "http://{$meuIp}:{$porta}" . ($doc->file_path ?? "/storage/documentos/default.pptx");
        $baseUrl = "https://nexowriter.com"; 
    
        $fileUrl = $baseUrl . ($doc->file_path ?? "/public/storage/documentos/default.pptx");
        $callbackUrl = $baseUrl . "/editor-beta/callback";
        
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
                "callbackUrl" => $callbackUrl,
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
     * Gera um arquivo PPTX em branco (Base64)
     */
    private function createDefaultPptxIfNotExists($path) {
        if (file_exists($path)) return true;
        // Base64 de um PPTX minimalista em branco
        $emptyPptx = base64_decode("UEsDBBQAAAAIAAAAIQAn9utpSgEAAJ0DAAALAAAAX3JlbHMvLnJlbHOskD1PwzAMhu9I/IfIeyNtoIQQErsDhMQHiN0m9toP1YmS9u9PAnVAAsHe7H6973un8/XGdfTExatPrBclK8HR6mDdtS7eD0/LK6icKNuYvWfXOGHAt739/fyeS6mF0id2Ucl8T6W0p9iIsR86kcl86GTSO6ZisDEn86GfVd10mO9m9AaFpZpXLuatKqD7F7Onv3m+69oB+T6Tf0beS7KExBAnmS17pXm6E6LSkX7D4N6I708E15D7O9LzBeMNo5mG3BshnF9BfMNo4X0D8Y3RzGf/AAAA//8DAFBLAwQUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAGRvY1Byb3BzL2NvcmUueG1sInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAwQUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAHBwdC9wcmVzZW50YXRpb24ueG1sInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAwQUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAFtDb250ZW50X1R5cGVzXS54bWxInxQUUvDMBDF3wf/Q8m7beo6N9pNhI7BeRRE8W3Iba9lsclN0m799yZdtX0beLnfvXvH5XpXN9EjmFA7XfCcZShAtU6ZatOzvzre8YIltpauG60Fz16B0fXm+qpaClVrZ6fWGrBWIAtS0pLnrN9mSlmX2L9gD84OfAClW9Z6p8AnuR9M9YvNHzYAx0v6B9Y3UfIChX5rR/O6A+4H0p8G30n4e499eH8YV1F+qI/X+L6S6E/jXGgO4mB9A3uN79N/Wz/p/H4L8ZzRXO/8AQAA//8DAFBLAQItABQAAAAIAAAAIQAn9utpSgEAAJ0DAAALAAAAAAAAAAAAAAAAAAAAAAAX3JlbHMvLnJlbHNQSwECLQAUAAYACAAAACEAtG5D5u8AAAD8AgAAEQAAAAAAAAAAAAAAAAAAAAAAAGRvY1Byb3BzL2NvcmUueG1sUEsBAi0AFAAGAAgAAAAhALRuQ+bvAAAA/AIAABUAAAAAAAAAAAAAAAAAAAAAAABwcHQvcHJlc2VudGF0aW9uLnhtbFBLAQItABQABgAIAAAAIALRuQ+bvAAAA/AIAABIAAAAAAAAAAAAAAAAAAAAAAABbDb250ZW50X1R5cGVzXS54bWxQSwUGAAAAAAYABgBvAQAAawIAAAAA");
        return file_put_contents($path, $emptyPptx) !== false;
    }
    
    // --- MÉTODO CREATE (DOCX) ---
    public function create() 
    {
        AuthMiddleware::check();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }
    
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $titulo = !empty(trim($_POST['titulo'])) ? trim($_POST['titulo']) : 'Novo Documento ' . date('d/m H:i');
    
        $db->prepare("INSERT INTO documents (user_id, titulo, created_at, updated_at) VALUES (?, ?, NOW(), NOW())")->execute([$userId, $titulo]);
        $id = $db->lastInsertId();
    
        $fileName = "doc_{$id}.docx";
        $this->setupFile($id, $fileName, 'docx');
    
        header("Location: " . BASE_URL . "/editor-beta/" . $id);
        exit;
    }
    
    // --- MÉTODO CREATE XLSX ---
    public function createXlsx() 
    {
        AuthMiddleware::check();
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $titulo = !empty(trim($_POST['titulo'])) ? trim($_POST['titulo']) : 'Nova Planilha ' . date('d/m H:i');
    
        $db->prepare("INSERT INTO documents (user_id, titulo, created_at, updated_at) VALUES (?, ?, NOW(), NOW())")->execute([$userId, $titulo]);
        $id = $db->lastInsertId();
    
        $fileName = "spreadsheet_{$id}.xlsx";
        $this->setupFile($id, $fileName, 'xlsx');
    
        header("Location: " . BASE_URL . "/editor-beta/spreadsheet/" . $id);
        exit;
    }
    
    // --- MÉTODO CREATE PPTX ---
    public function createPptx() 
    {
        AuthMiddleware::check();
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $titulo = !empty(trim($_POST['titulo'])) ? trim($_POST['titulo']) : 'Nova Apresentação ' . date('d/m H:i');
    
        $db->prepare("INSERT INTO documents (user_id, titulo, created_at, updated_at) VALUES (?, ?, NOW(), NOW())")->execute([$userId, $titulo]);
        $id = $db->lastInsertId();
    
        $fileName = "presentation_{$id}.pptx";
        $this->setupFile($id, $fileName, 'pptx');
    
        header("Location: " . BASE_URL . "/editor-beta/presentation/" . $id);
        exit;
    }
    
    /**
     * Função Auxiliar Unificada para configurar o arquivo
     */
    // private function setupFile($id, $fileName, $type) {
    //     $relativeDir = "/public/storage/documentos/";
    //     $storageDir = $_SERVER['DOCUMENT_ROOT'] . $relativeDir;
    //     $absoluteFilePath = $storageDir . $fileName;
    //     $defaultFile = $storageDir . "default." . $type;
    
    //     // 1. Garante que a pasta existe com permissão total para o PHP
    //     if (!is_dir($storageDir)) {
    //         mkdir($storageDir, 0755, true); 
    //     }
    
    //     // 2. Garante que o arquivo default existe (se não existir, cria agora)
    //     if (!file_exists($defaultFile)) {
    //         if ($type == 'docx') $this->createDefaultDocIfNotExists($defaultFile);
    //         if ($type == 'xlsx') $this->createDefaultXlsxIfNotExists($defaultFile);
    //         if ($type == 'pptx') $this->createDefaultPptxIfNotExists($defaultFile);
    //     }
    
    //     // 3. Copia o arquivo
    //     if (!copy($defaultFile, $absoluteFilePath)) {
    //         // Se falhar aqui, tente usar file_put_contents para "forçar" a criação
    //         $content = file_get_contents($defaultFile);
    //         file_put_contents($absoluteFilePath, $content);
    //     }
    
    //     // 4. Dá permissão de escrita para o OnlyOffice conseguir salvar depois
    //     chmod($absoluteFilePath, 0666);
    
    //     $db = Database::getInstance();
    //     $db->prepare("UPDATE documents SET file_path = ? WHERE id = ?")
    //       ->execute([$relativeDir . $fileName, $id]);
    // }
    private function setupFile($id, $fileName, $type) {
        $relativeDir = "/public/storage/documentos/";
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . $relativeDir;
        
        // GERAR UUID PARA O NOME FÍSICO
        $uid = bin2hex(random_bytes(16)); // Gera uma string única de 32 caracteres
        $newPhysicalName = "{$uid}.{$type}";
        $absoluteFilePath = $storageDir . $newPhysicalName;
        
        $defaultFile = $storageDir . "default." . $type;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true); 
        }

        if (!file_exists($defaultFile)) {
            if ($type == 'docx') $this->createDefaultDocIfNotExists($defaultFile);
            if ($type == 'xlsx') $this->createDefaultXlsxIfNotExists($defaultFile);
            if ($type == 'pptx') $this->createDefaultPptxIfNotExists($defaultFile);
        }

        // Copia o template para o arquivo com nome UUID
        if (copy($defaultFile, $absoluteFilePath)) {
            chmod($absoluteFilePath, 0666);
        }

        $db = Database::getInstance();
        // SALVAMOS O CAMINHO COM UUID NO BANCO
        $db->prepare("UPDATE documents SET file_path = ?, updated_at = NOW() WHERE id = ?")
        ->execute([$relativeDir . $newPhysicalName, $id]);
    }
    
    // Metodo para compartilhar um documento com outro usuário
    public function share()
    {
        $documentId = $_POST['document_id'];
        $targetEmail = $_POST['email'];
        $canEdit = $_POST['can_edit'] ?? 0;
        $ownerId = $_SESSION['user_id'];

        $db = Database::getInstance();
        
        // 1. Verifica se quem está compartilhando é o dono
        $stmt = $db->prepare("SELECT id FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$documentId, $ownerId]);
        if (!$stmt->fetch()) {
            die(json_encode(['error' => 'Apenas o dono pode compartilhar.']));
        }

        // 2. Busca o ID do usuário pelo e-mail
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?"); // ajuste para sua tabela de users
        $stmt->execute([$targetEmail]);
        $targetUser = $stmt->fetch();

        if (!$targetUser) {
            die(json_encode(['error' => 'Usuário não encontrado.']));
        }

        // 3. Insere a permissão
        $stmt = $db->prepare("INSERT INTO document_shares (document_id, user_id, can_edit) VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE can_edit = ?");
        $stmt->execute([$documentId, $targetUser->id, $canEdit, $canEdit]);

        echo json_encode(['success' => 'Compartilhado com sucesso!']);
    }
}