<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");

$data = json_decode(file_get_contents("php://input"), true);
$itemId = $data['itemId'];
$quantityToAdd = $data['quantityToAdd'];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Get current quantities
    $sql = "SELECT actual_quantity, new_delivery, beginning_quantity FROM inventory WHERE item_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if (!$item) {
        throw new Exception("Item not found");
    }

    // Update the quantities
    $new_delivery = $quantityToAdd;
    $beginning_quantity = $item['actual_quantity']; // Current actual becomes the new beginning
    $actual_quantity = $beginning_quantity + $new_delivery;

    // Update inventory
    $sql = "UPDATE inventory SET 
            actual_quantity = ?,
            new_delivery = ?,
            beginning_quantity = ?,
            status = CASE 
                WHEN ? <= 0 THEN 'Out of Stock'
                WHEN ? <= 20 THEN 'Low Stock'
                ELSE 'In Stock'
            END
            WHERE item_code = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiss", 
        $actual_quantity,
        $new_delivery,
        $beginning_quantity,
        $actual_quantity,
        $actual_quantity,
        $itemId
    );
    $stmt->execute();

    // Log the activity
    $activity_description = "New delivery added - Item: $itemId, Quantity: $quantityToAdd, Previous stock: $beginning_quantity, New total: $actual_quantity";
    $log_activity_query = "INSERT INTO activities (action_type, description, item_code, timestamp) VALUES ('new_delivery', ?, ?, NOW())";
    $stmt = $conn->prepare($log_activity_query);
    $stmt->bind_param("ss", $activity_description, $itemId);
    $stmt->execute();

    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode([
        'success' => true,
        'new_quantity' => $actual_quantity,
        'message' => 'Quantity updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>