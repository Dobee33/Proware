<?php
require_once '../Includes/connection.php';

try {
    // Check if the notifications table already exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'notifications'");
    if ($checkTable->rowCount() == 0) {
        // Create notifications table
        $sql = "CREATE TABLE notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            order_number VARCHAR(50) NOT NULL,
            type VARCHAR(20) NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES account(id)
        )";
        $conn->exec($sql);
        echo "Notifications table created successfully";
    } else {
        echo "Notifications table already exists";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 