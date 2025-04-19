<?php
// After completing a sale
$description = "Sold {$quantity} units of {$item_name}";
$sql = "INSERT INTO activities (action_type, description, item_code) VALUES ('sale', ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $description, $item_code);
$stmt->execute();