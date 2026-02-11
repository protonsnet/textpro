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
        file_put_contents("./storage/" . $fileName, $newData);

        // AQUI você insere ou atualiza no seu MySQL
        // $db->query("UPDATE documentos SET path = '$fileName' WHERE id = 1");
    }
}
echo json_encode(["error" => 0]);
?>