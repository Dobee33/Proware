<?php
// Disable error display in output
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

try {
    // Connect to database
    $conn = mysqli_connect("localhost", "root", "", "proware");
    if (!$conn) {
        throw new Exception('Connection failed: ' . mysqli_connect_error());
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Get and validate form data
    $orderNumber = isset($_POST['orderNumber']) ? trim($_POST['orderNumber']) : '';
    if (empty($orderNumber)) {
        throw new Exception('Order number is required');
    }

    // Validate arrays
    if (!isset($_POST['itemId']) || !isset($_POST['quantityToAdd'])) {
        throw new Exception('Missing item data');
    }

    $itemIds = is_array($_POST['itemId']) ? $_POST['itemId'] : [$_POST['itemId']];
    $quantitiesToAdd = is_array($_POST['quantityToAdd']) ? $_POST['quantityToAdd'] : [$_POST['quantityToAdd']];

    if (count($itemIds) !== count($quantitiesToAdd)) {
        throw new Exception('Mismatched item and quantity data');
    }

    if (empty($itemIds)) {
        throw new Exception('No items provided');
    }

    // Validate all items before starting transaction
    $validatedItems = [];
    foreach ($itemIds as $i => $itemId) {
        $itemId = trim(mysqli_real_escape_string($conn, $itemId));
        $quantity = intval($quantitiesToAdd[$i]);

        if (empty($itemId)) {
            throw new Exception("Empty item ID at position " . ($i + 1));
        }

        if ($quantity <= 0) {
            throw new Exception("Invalid quantity for item $itemId: must be greater than 0");
        }

        // Check if item exists
        $stmt = $conn->prepare("SELECT item_code FROM inventory WHERE item_code = ?");
        if (!$stmt) {
            throw new Exception("Database error");
        }

        $stmt->bind_param("s", $itemId);
        if (!$stmt->execute()) {
            throw new Exception("Database error");
        }

        $result = $stmt->get_result();
        if (!$result->fetch_assoc()) {
            throw new Exception("Item not found: $itemId");
        }

        $validatedItems[] = [
            'itemId' => $itemId,
            'quantity' => $quantity
        ];
    }

    // Start transaction after all validation is complete
    mysqli_begin_transaction($conn);

    // Process each validated item
    foreach ($validatedItems as $item) {
        // Get current quantities with row lock
        $sql = "SELECT actual_quantity, new_delivery, beginning_quantity FROM inventory WHERE item_code = ? FOR UPDATE";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database error");
        }
        
        $stmt->bind_param("s", $item['itemId']);
        if (!$stmt->execute()) {
            throw new Exception("Database error");
        }
        
        $result = $stmt->get_result();
        $currentItem = $result->fetch_assoc();
        if (!$currentItem) {
            throw new Exception("Item not found: {$item['itemId']}");
        }

        // Calculate new quantities
        $new_delivery = $item['quantity'];
        $beginning_quantity = $currentItem['actual_quantity'];
        $actual_quantity = $beginning_quantity + $new_delivery;

        // Update inventory
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
            throw new Exception("Database error");
        }

        $updateStockStmt->bind_param("iiisi", 
            $actual_quantity,
            $actual_quantity,
            $actual_quantity,
            $item['itemId'],
            $beginning_quantity
        );
        
        if (!$updateStockStmt->execute()) {
            throw new Exception("Failed to update inventory");
        }
        $updateStockStmt->close();

        // Log the activity
        $activity_description = "New delivery added - Order #: $orderNumber, Item: {$item['itemId']}, Quantity: {$item['quantity']}, Previous stock: $beginning_quantity, New total: $actual_quantity";
        $log_activity_query = "INSERT INTO activities (action_type, description, item_code, user_id, timestamp) VALUES ('Restock Item', ?, ?, ?, NOW())";
        $stmt = $conn->prepare($log_activity_query);
        if (!$stmt) {
            throw new Exception("Database error");
        }

        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id === null) {
            throw new Exception("User not logged in");
        }
        $stmt->bind_param("ssi", $activity_description, $item['itemId'], $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to log activity");
        }
        $stmt->close();
    }

    // If we got here, everything succeeded
    mysqli_commit($conn);
    die(json_encode([
        'success' => true,
        'message' => 'All items in delivery recorded successfully'
    ]));

} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($conn) && $conn->ping()) {
        mysqli_rollback($conn);
    }
    die(json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
} finally {
    // Close connection if it exists
    if (isset($conn) && $conn->ping()) {
        mysqli_close($conn);
    }
}
?> 