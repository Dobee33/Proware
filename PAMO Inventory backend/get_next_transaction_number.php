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

// Get the last order_number from pre_orders
$sql1 = "SELECT order_number FROM pre_orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("s", $like_pattern);
$stmt1->execute();
$result1 = $stmt1->get_result();
$last_preorder = $result1->fetch_assoc();
$stmt1->close();

// Get the last transaction_number from sales
$sql2 = "SELECT transaction_number FROM sales WHERE transaction_number LIKE ? ORDER BY id DESC LIMIT 1";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("s", $like_pattern);
$stmt2->execute();
$result2 = $stmt2->get_result();
$last_sales = $result2->fetch_assoc();
$stmt2->close();

$last_seq = 0;
if ($last_preorder && preg_match('/(\d{6})$/', $last_preorder['order_number'], $matches1)) {
    $last_seq = max($last_seq, (int)$matches1[1]);
}
if ($last_sales && preg_match('/(\d{6})$/', $last_sales['transaction_number'], $matches2)) {
    $last_seq = max($last_seq, (int)$matches2[1]);
}
$new_seq = $last_seq + 1;
$next_order_number = sprintf('%s-%s-%06d', $prefix, $date_part, $new_seq);

echo json_encode([
    'success' => true,
    'next_transaction_number' => $next_order_number
]);

$conn->close(); 