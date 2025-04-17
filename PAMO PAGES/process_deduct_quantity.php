<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . mysqli_connect_error()
    ]));
}

// Get form data
$transactionNumber = mysqli_real_escape_string($conn, $_POST['transactionNumber']);
$itemId = mysqli_real_escape_string($conn, $_POST['itemId']);
$size = mysqli_real_escape_string($conn, $_POST['size']);
$quantityToDeduct = intval($_POST['quantityToDeduct']);
$pricePerItem = floatval($_POST['pricePerItem']);
$totalAmount = floatval($_POST['totalAmount']);

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Get current quantities
    $sql = "SELECT actual_quantity, beginning_quantity FROM inventory WHERE item_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if (!$item) {
        throw new Exception("Item not found");
    }

    if ($item['actual_quantity'] < $quantityToDeduct) {
        throw new Exception("Insufficient stock. Current stock: " . $item['actual_quantity']);
    }

    // Calculate new quantities
    $new_actual_quantity = $item['actual_quantity'] - $quantityToDeduct;
    
    // Update inventory
    $sql = "UPDATE inventory SET 
            actual_quantity = ?,
            sold_quantity = sold_quantity + ?,
            status = CASE 
                WHEN ? <= 0 THEN 'Out of Stock'
                WHEN ? <= 20 THEN 'Low Stock'
                ELSE 'In Stock'
            END
            WHERE item_code = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", 
        $new_actual_quantity,
        $quantityToDeduct,
        $new_actual_quantity,
        $new_actual_quantity,
        $itemId
    );
    $stmt->execute();

    // Record the sale in a sales table
    $sql = "INSERT INTO sales (transaction_number, item_code, size, quantity, price_per_item, total_amount, sale_date) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssidd", 
        $transactionNumber,
        $itemId,
        $size,
        $quantityToDeduct,
        $pricePerItem,
        $totalAmount
    );
    $stmt->execute();

    // Log the activity
    $activity_description = "Sale recorded - Transaction #: $transactionNumber, Item: $itemId, Size: $size, Quantity: $quantityToDeduct, Total: $totalAmount, Previous stock: {$item['actual_quantity']}, New stock: $new_actual_quantity";
    $log_activity_query = "INSERT INTO activities (action_type, description, item_code, timestamp) VALUES ('sale', ?, ?, NOW())";
    $stmt = $conn->prepare($log_activity_query);
    $stmt->bind_param("ss", $activity_description, $itemId);
    $stmt->execute();

    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode([
        'success' => true,
        'new_quantity' => $new_actual_quantity,
        'message' => 'Sale recorded successfully'
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