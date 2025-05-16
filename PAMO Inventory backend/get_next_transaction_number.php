<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "proware");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]);
    exit;
}

$prefix = 'SI';
$date_part = date('md');
$like_pattern = $prefix . '-' . $date_part . '-%';
$sql = "SELECT order_number FROM pre_orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $like_pattern);
$stmt->execute();
$result = $stmt->get_result();
$last_order = $result->fetch_assoc();

if ($last_order && preg_match('/(\d{6})$/', $last_order['order_number'], $matches)) {
    $last_seq = (int)$matches[1];
    $new_seq = $last_seq + 1;
} else {
    $new_seq = 1;
}
$next_order_number = sprintf('%s-%s-%06d', $prefix, $date_part, $new_seq);

echo json_encode([
    'success' => true,
    'next_transaction_number' => $next_order_number
]);

$stmt->close();
$conn->close(); 