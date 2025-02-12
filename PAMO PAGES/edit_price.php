<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");

$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId'];
$newPrice = $data['newPrice'];

$sql = "UPDATE inventory SET price = ? WHERE item_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ds", $newPrice, $itemId);
$success = $stmt->execute();

echo json_encode(['success' => $success]);

// After successfully updating the price
$activity_description = "Updated price for item: $itemId to ₱$newPrice";
$log_activity_query = "INSERT INTO activities (action_type, description, timestamp) VALUES ('price_update', ?, NOW())";
$stmt = $conn->prepare($log_activity_query);
$stmt->bind_param("s", $activity_description);
$stmt->execute();
?>