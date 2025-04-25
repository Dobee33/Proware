<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Log the raw POST data
error_log("Raw POST data: " . print_r($_POST, true));

$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . mysqli_connect_error()
    ]));
}

// Get form data
$orderNumber = isset($_POST['orderNumber']) ? mysqli_real_escape_string($conn, $_POST['orderNumber']) : '';

// Debug: Log the received data
error_log("Order Number: " . $orderNumber);
error_log("ItemId Array: " . print_r($_POST['itemId'], true));
error_log("Quantity Array: " . print_r($_POST['quantityToAdd'], true));

// Validate order number
if (empty($orderNumber)) {
    die(json_encode([
        'success' => false,
        'message' => 'Order number is required'
    ]));
}

// Check if we have arrays of items and quantities
if (!isset($_POST['itemId']) || !isset($_POST['quantityToAdd'])) {
    die(json_encode([
        'success' => false,
        'message' => 'Missing item data'
    ]));
}

// Ensure we have arrays
$itemIds = is_array($_POST['itemId']) ? $_POST['itemId'] : [$_POST['itemId']];
$quantitiesToAdd = is_array($_POST['quantityToAdd']) ? $_POST['quantityToAdd'] : [$_POST['quantityToAdd']];

// Validate arrays have same length
if (count($itemIds) !== count($quantitiesToAdd)) {
    die(json_encode([
        'success' => false,
        'message' => 'Mismatched item and quantity data'
    ]));
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    $success = true;
    $errors = [];

    // Process each item
    for ($i = 0; $i < count($itemIds); $i++) {
        $itemId = mysqli_real_escape_string($conn, $itemIds[$i]);
        $quantityToAdd = intval($quantitiesToAdd[$i]);

        if (empty($itemId) || $quantityToAdd <= 0) {
            $errors[] = "Invalid data for item at position " . ($i + 1);
            continue;
        }

        // Get current quantities
        $sql = "SELECT actual_quantity, new_delivery, beginning_quantity FROM inventory WHERE item_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        if (!$item) {
            $errors[] = "Item not found: $itemId";
            continue;
        }

        // Update the quantities
        $new_delivery = $quantityToAdd;
        $beginning_quantity = $item['actual_quantity'];
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
        
        if (!$stmt->execute()) {
            $errors[] = "Error updating item $itemId: " . $stmt->error;
            $success = false;
            break;
        }

        // Log the activity
        $activity_description = "New delivery added - Order #: $orderNumber, Item: $itemId, Quantity: $quantityToAdd, Previous stock: $beginning_quantity, New total: $actual_quantity";
        $log_activity_query = "INSERT INTO activities (action_type, description, item_code, timestamp) VALUES ('new_delivery', ?, ?, NOW())";
        $stmt = $conn->prepare($log_activity_query);
        $stmt->bind_param("ss", $activity_description, $itemId);
        
        if (!$stmt->execute()) {
            $errors[] = "Error logging activity for item $itemId: " . $stmt->error;
            $success = false;
            break;
        }
    }

    if ($success && empty($errors)) {
        mysqli_commit($conn);
        echo json_encode([
            'success' => true,
            'message' => 'All items in delivery recorded successfully'
        ]);
    } else {
        mysqli_rollback($conn);
        echo json_encode([
            'success' => false,
            'message' => 'Errors occurred: ' . implode(', ', $errors)
        ]);
    }

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?> 