<?php
require_once '../Includes/connection.php';

try {
    // Check if the payment_date column already exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM pre_orders LIKE 'payment_date'");
    if ($checkColumn->rowCount() == 0) {
        // Add payment_date column to pre_orders table
        $sql = "ALTER TABLE pre_orders ADD COLUMN payment_date DATETIME NULL AFTER status";
        $conn->exec($sql);
        echo "Payment date field added successfully";
    } else {
        echo "Payment date field already exists";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 