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

// After successfully updating the image
$activity_description = "Updated image for item: $itemId";
$log_activity_query = "INSERT INTO activities (action_type, description, timestamp) VALUES ('edit_image', ?, NOW())";
$stmt = $conn->prepare($log_activity_query);
$stmt->bind_param("s", $activity_description);
$stmt->execute();
?>