<?php
// deduct_quantity.php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");

$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId'];
$quantityToDeduct = $data['quantityToDeduct'];

// Update the database
$sql = "UPDATE inventory SET actual_quantity = actual_quantity - ? WHERE item_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $quantityToDeduct, $itemId);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
// After successfully deducting quantity
$activity_description = "Deducted $quantityToDeduct from item: $itemId";
$log_activity_query = "INSERT INTO activities (action_type, description, timestamp) VALUES ('quantity_update', ?, NOW())";
$stmt = $conn->prepare($log_activity_query);
$stmt->bind_param("s", $activity_description);
$stmt->execute();
?>