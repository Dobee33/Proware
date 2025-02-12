<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");

$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId'];
$newImage = $_FILES['newImage'];

$uploadDir = 'uploads/';
$uploadFile = $uploadDir . basename($newImage['name']);

if (move_uploaded_file($newImage['tmp_name'], $uploadFile)) {
    $sql = "UPDATE inventory SET image_path = ? WHERE item_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $uploadFile, $itemId);
    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
}
?>