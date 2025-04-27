<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "proware");

if (!$conn) {
    die(json_encode([
        'success' => false,
        'message' => 'Connection failed: ' . mysqli_connect_error()
    ]));
}

// Get item code and size from request
$item_code = isset($_GET['item_code']) ? mysqli_real_escape_string($conn, $_GET['item_code']) : '';
$size = isset($_GET['size']) ? mysqli_real_escape_string($conn, $_GET['size']) : '';

if (empty($item_code) || empty($size)) {
    die(json_encode([
        'success' => false,
        'message' => 'Item code and size are required'
    ]));
}

// Get the price from inventory
$sql = "SELECT price FROM inventory WHERE item_code = ? AND sizes LIKE ?";
$stmt = $conn->prepare($sql);
$size_pattern = "%$size%";
$stmt->bind_param("ss", $item_code, $size_pattern);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if ($item) {
    echo json_encode([
        'success' => true,
        'price' => $item['price']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Price not found for the selected item and size'
    ]);
}

mysqli_close($conn);
?> 