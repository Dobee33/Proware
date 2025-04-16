<?php
// After accepting an order
$description = "Order #{$order_id} has been accepted";
$sql = "INSERT INTO activities (action_type, description, item_code) VALUES ('order_accepted', ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $description, $item_code);
$stmt->execute();