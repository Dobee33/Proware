<?php
session_start();
file_put_contents(__DIR__ . '/debug_add_item_size.txt', print_r($_POST, true));
header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]));
}

// Get form data
$existingItem = isset($_POST['existingItem']) ? $_POST['existingItem'] : '';
$newItemCode = isset($_POST['newItemCode']) ? $_POST['newItemCode'] : '';
$newSize = isset($_POST['newSize']) ? $_POST['newSize'] : '';
$newQuantity = isset($_POST['newQuantity']) ? $_POST['newQuantity'] : '';
$newDamage = isset($_POST['newDamage']) && $_POST['newDamage'] !== '' ? $_POST['newDamage'] : 0;

// Validate inputs
if (
    empty($existingItem) ||
    empty($newItemCode) ||
    empty($newSize) ||
    $newQuantity === '' ||
    $newQuantity === null
) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Get the original item details
$sql = "SELECT item_name, category, price FROM inventory WHERE item_code LIKE ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
$prefix = $existingItem . '%';
mysqli_stmt_bind_param($stmt, "s", $prefix);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$originalItem = mysqli_fetch_assoc($result);

if (!$originalItem) {
    echo json_encode(['success' => false, 'message' => 'Original item not found']);
    exit;
}

// Check if the new item code already exists
$sql = "SELECT item_code FROM inventory WHERE item_code = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $newItemCode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(['success' => false, 'message' => 'Item code already exists']);
    exit;
}

// Insert the new item size
$sql = "INSERT INTO inventory (item_code, item_name, category, sizes, actual_quantity, damage, price, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssiid", 
    $newItemCode,
    $originalItem['item_name'],
    $originalItem['category'],
    $newSize,
    $newQuantity,
    $newDamage,
    $originalItem['price']
);

if (mysqli_stmt_execute($stmt)) {
    // Log the activity
    $activity_description = "New size added for {$originalItem['item_name']} ({$newItemCode}) - Size: {$newSize}, Initial stock: {$newQuantity}, Damage: {$newDamage}";
    $log_activity_query = "INSERT INTO activities (action_type, description, item_code, timestamp) VALUES ('Add Item Size', ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $log_activity_query);
    mysqli_stmt_bind_param($stmt, "ss", $activity_description, $newItemCode);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true, 'message' => 'New size added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding new size: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?> 