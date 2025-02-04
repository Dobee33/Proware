<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_code = $_POST['item_code'];
    $quantity_to_add = (int) $_POST['quantity'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Update new_delivery
        $sql_delivery = "UPDATE inventory SET new_delivery = new_delivery + ? WHERE item_code = ?";
        $stmt_delivery = mysqli_prepare($conn, $sql_delivery);
        mysqli_stmt_bind_param($stmt_delivery, "is", $quantity_to_add, $item_code);
        mysqli_stmt_execute($stmt_delivery);

        // Update actual_quantity
        $sql_actual = "UPDATE inventory SET actual_quantity = actual_quantity + ? WHERE item_code = ?";
        $stmt_actual = mysqli_prepare($conn, $sql_actual);
        mysqli_stmt_bind_param($stmt_actual, "is", $quantity_to_add, $item_code);
        mysqli_stmt_execute($stmt_actual);

        // Update status based on actual_quantity
        $sql_status = "UPDATE inventory SET 
            status = CASE 
                WHEN actual_quantity <= 0 THEN 'Out of Stock'
                WHEN actual_quantity <= 10 THEN 'Low Stock'
                ELSE 'In Stock'
            END 
            WHERE item_code = ?";
        $stmt_status = mysqli_prepare($conn, $sql_status);
        mysqli_stmt_bind_param($stmt_status, "s", $item_code);
        mysqli_stmt_execute($stmt_status);

        // Commit transaction
        mysqli_commit($conn);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
}

mysqli_close($conn);
?>