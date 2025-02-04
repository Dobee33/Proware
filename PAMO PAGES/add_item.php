<?php
// Prevent any output before our JSON response
ob_clean();

// Set proper JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Enable error reporting but log to file instead of output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    $conn = mysqli_connect("localhost", "root", "", "proware");

    if (!$conn) {
        throw new Exception('Connection failed: ' . mysqli_connect_error());
    }

    // Get and decode the raw POST data
    $rawData = file_get_contents('php://input');
    error_log("Raw data received: " . $rawData);

    $data = json_decode($rawData, true);
    error_log("Decoded data: " . print_r($data, true));

    if (!$data || !is_array($data)) {
        throw new Exception('Invalid JSON data received');
    }

    // Validate required fields
    $required_fields = ['item_code', 'category', 'item_name', 'sizes', 'price', 'quantity'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }

    // Sanitize and prepare data
    $item_code = mysqli_real_escape_string($conn, $data['item_code']);
    $category = mysqli_real_escape_string($conn, $data['category']);
    $item_name = mysqli_real_escape_string($conn, $data['item_name']);
    $sizes = mysqli_real_escape_string($conn, $data['sizes']);
    $price = floatval($data['price']);
    $quantity = intval($data['quantity']);

    // Set initial values
    $actual_quantity = $quantity;
    $new_delivery = $quantity;
    $beginning_quantity = $quantity;
    $damage = 0;
    $sold_quantity = 0;
    $status = ($quantity <= 0) ? 'Out of Stock' : (($quantity <= 10) ? 'Low Stock' : 'In Stock');

    $sql = "INSERT INTO inventory (
        item_code, category, item_name, sizes, price, 
        actual_quantity, new_delivery, beginning_quantity, 
        damage, sold_quantity, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssdiiiiis",
        $item_code,
        $category,
        $item_name,
        $sizes,
        $price,
        $actual_quantity,
        $new_delivery,
        $beginning_quantity,
        $damage,
        $sold_quantity,
        $status
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error executing statement: ' . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // Success response
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Error in add_item.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>