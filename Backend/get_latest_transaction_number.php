<?php
require_once '../Includes/connection.php';
date_default_timezone_set('Asia/Manila');

$prefix = 'SI';
$date_part = date('md');
$like_pattern = $prefix . '-' . $date_part . '-%';

// Get the last order_number from pre_orders
$stmt1 = $conn->prepare("SELECT order_number FROM pre_orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1");
$stmt1->execute([$like_pattern]);
$last_preorder = $stmt1->fetch(PDO::FETCH_ASSOC);

// Get the last transaction_number from sales
$stmt2 = $conn->prepare("SELECT transaction_number FROM sales WHERE transaction_number LIKE ? ORDER BY id DESC LIMIT 1");
$stmt2->execute([$like_pattern]);
$last_sales = $stmt2->fetch(PDO::FETCH_ASSOC);

$last_seq = 0;
if ($last_preorder && preg_match('/(\d{6})$/', $last_preorder['order_number'], $matches1)) {
    $last_seq = max($last_seq, (int)$matches1[1]);
}
if ($last_sales && preg_match('/(\d{6})$/', $last_sales['transaction_number'], $matches2)) {
    $last_seq = max($last_seq, (int)$matches2[1]);
}
$new_seq = $last_seq + 1;
$new_order_number = sprintf('%s-%s-%06d', $prefix, $date_part, $new_seq);

header('Content-Type: application/json');
echo json_encode(['transaction_number' => $new_order_number]); 