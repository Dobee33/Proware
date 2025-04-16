<?php
$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create sales table
$sql = "CREATE TABLE IF NOT EXISTS sales (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    transaction_number VARCHAR(50) NOT NULL,
    item_code VARCHAR(50) NOT NULL,
    size VARCHAR(20) NOT NULL,
    quantity INT(11) NOT NULL,
    price_per_item DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    sale_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Sales table created successfully";
} else {
    echo "Error creating sales table: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 