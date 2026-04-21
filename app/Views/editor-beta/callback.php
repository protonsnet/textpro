<?php
// callback.php
$body = file_get_contents('php://input');
file_put_contents(APP_ROOT . "/debug_callback.txt", $body);
if ($body) {
    $data = json_decode($body, true);

    // Status 2 significa "Documento pronto para ser salvo"
    if ($data["status"] == 2) {
        $downloadUrl = $data["url"]; // Link do arquivo gerado pelo OnlyOffice
        $newData = file_get_contents($downloadUrl);
        
        $fileName = "Documento_Beta_V2.docx";
        // file_put_contents("./storage/" . $fileName, $newData);
        $path = APP_ROOT . '/public/storage/documentos/' . $fileName;

        file_put_contents($path, $newData);

        $documentId = $data["key"]; // ou você define isso no config

        $db->prepare("
            UPDATE documents 
            SET file_path = :path, updated_at = NOW()
            WHERE id = :id
        ")->execute([
            ':path' => $fileName,
            ':id'   => $documentId
        ]);
    }
}
echo json_encode(["error" => 0]);
?>