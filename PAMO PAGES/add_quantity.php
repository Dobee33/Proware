<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");

$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId'];
$quantityToAdd = $data['quantityToAdd'];

$sql = "UPDATE inventory SET actual_quantity = actual_quantity + ? WHERE item_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $quantityToAdd, $itemId);
$success = $stmt->execute();

echo json_encode(['success' => $success]);

$activity_description = "Added $quantityToAdd to item: $itemId";
$log_activity_query = "INSERT INTO activities (action_type, description, timestamp) VALUES ('quantity_update', ?, NOW())";
$stmt = $conn->prepare($log_activity_query);
$stmt->bind_param("s", $activity_description);
$stmt->execute();
?>