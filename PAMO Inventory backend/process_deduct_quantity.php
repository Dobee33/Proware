<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . mysqli_connect_error()
    ]));
}

// Get form data
$transactionNumber = mysqli_real_escape_string($conn, $_POST['transactionNumber']);
$itemIds = is_array($_POST['itemId']) ? $_POST['itemId'] : [$_POST['itemId']];
$sizes = is_array($_POST['size']) ? $_POST['size'] : [$_POST['size']];
$quantitiesToDeduct = is_array($_POST['quantityToDeduct']) ? $_POST['quantityToDeduct'] : [$_POST['quantityToDeduct']];
$pricesPerItem = is_array($_POST['pricePerItem']) ? $_POST['pricePerItem'] : [$_POST['pricePerItem']];
$itemTotals = is_array($_POST['itemTotal']) ? $_POST['itemTotal'] : [$_POST['itemTotal']];
$totalAmount = floatval($_POST['totalAmount']);

// Validate arrays have same length
if (count($itemIds) !== count($sizes) || 
    count($itemIds) !== count($quantitiesToDeduct) || 
    count($itemIds) !== count($pricesPerItem) || 
    count($itemIds) !== count($itemTotals)) {
    die(json_encode([
        'success' => false,
        'message' => 'Mismatched item data'
    ]));
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    $success = true;
    $errors = [];

    // Validate transaction number
    if (empty($transactionNumber)) {
        throw new Exception("Transaction number is required");
    }

    // Process each item
    for ($i = 0; $i < count($itemIds); $i++) {
        $itemId = mysqli_real_escape_string($conn, $itemIds[$i]);
        $size = mysqli_real_escape_string($conn, $sizes[$i]);
        $quantityToDeduct = intval($quantitiesToDeduct[$i]);
        $pricePerItem = floatval($pricesPerItem[$i]);
        $itemTotal = floatval($itemTotals[$i]);

        // Verify item exists and get current quantities with row lock
        $sql = "SELECT actual_quantity, beginning_quantity FROM inventory WHERE item_code = ? FOR UPDATE";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        if (!$item) {
            throw new Exception("Item not found or no longer exists in inventory: $itemId");
        }

        if ($item['actual_quantity'] < $quantityToDeduct) {
            throw new Exception("Insufficient stock for item $itemId. Current stock: " . $item['actual_quantity']);
        }

        // Calculate new quantities
        $new_actual_quantity = $item['actual_quantity'] - $quantityToDeduct;
        
        // Update inventory with optimistic locking
        $updateStockStmt = $conn->prepare(
            "UPDATE inventory 
            SET actual_quantity = ?,
                status = CASE 
                    WHEN ? <= 0 THEN 'Out of Stock'
                    WHEN ? <= 10 THEN 'Low Stock'
                    ELSE 'In Stock'
                END
            WHERE item_code = ? AND actual_quantity = ?"
        );
        if (!$updateStockStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $updateStockStmt->bind_param("iiiiss", 
            $new_actual_quantity,
            $new_actual_quantity,
            $new_actual_quantity,
            $itemId,
            $item['actual_quantity']
        );
        
        if (!$updateStockStmt->execute()) {
            throw new Exception("Error updating item $itemId: " . $updateStockStmt->error);
        }

        if ($updateStockStmt->affected_rows === 0) {
            throw new Exception("Item $itemId was modified by another transaction. Please try again.");
        }

        // Record the sale in sales table
        $sql = "INSERT INTO sales (transaction_number, item_code, size, quantity, price_per_item, total_amount, sale_date) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssidd", 
            $transactionNumber,
            $itemId,
            $size,
            $quantityToDeduct,
            $pricePerItem,
            $itemTotal
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error recording sale for item $itemId: " . $stmt->error);
        }

        // Log the activity
        $activity_description = "Sale recorded - Transaction #: $transactionNumber, Item: $itemId, Size: $size, Quantity: $quantityToDeduct, Total: $itemTotal, Previous stock: {$item['actual_quantity']}, New stock: $new_actual_quantity";
        $log_activity_query = "INSERT INTO activities (action_type, description, item_code, user_id, timestamp) VALUES ('Sales', ?, ?, ?, NOW())";
        $stmt = $conn->prepare($log_activity_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $user_id = $_SESSION['user_id'] ?? null;
        $stmt->bind_param("ssi", $activity_description, $itemId, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error logging activity for item $itemId: " . $stmt->error);
        }
    }

    // If we got here, everything succeeded
    mysqli_commit($conn);
    echo json_encode([
        'success' => true,
        'message' => 'All sales recorded successfully'
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?> 