// upload.php
<?php
$target_dir = "public/uploads/images/";
if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

$file = $_FILES['image'];
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = uniqid() . '.' . $extension;
$target_file = $target_dir . $fileName;

if (move_uploaded_file($file['tmp_name'], $target_file)) {
    echo json_encode(['url' => 'http://localhost:8000/textpro/' . $target_file]);
} else {
    http_response_code(500);
}
?>