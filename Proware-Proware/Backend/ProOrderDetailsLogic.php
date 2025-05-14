<?php
session_start();
require_once '../Includes/connection.php';

$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$course = $_POST['course'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$cart_items = json_decode($_POST['cart_items'] ?? '[]', true);
if (!is_array($cart_items)) {
    $cart_items = [];
}
$included_items = json_decode($_POST['included_items'] ?? '[]', true);
if (!is_array($included_items)) {
    $included_items = [];
}
$total_amount = $_POST['total_amount'] ?? 0;

$selected_items = array_filter($cart_items, function($item) use ($included_items) {
    return in_array($item['id'], $included_items);
});
$selected_items = array_values($selected_items);

// Generate order number in the format SI-<mmdd>-<sequential>
$order_number = '';
if (!empty($selected_items)) {
    $prefix = 'SI';
    $date_part = date('md');
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT order_number FROM pre_orders WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1");
    $like_pattern = $prefix . '-' . $date_part . '-%';
    $stmt->execute([$like_pattern]);
    $last_order = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($last_order && preg_match('/(\\d{6})$/', $last_order['order_number'], $matches)) {
        $last_seq = (int)$matches[1];
        $new_seq = $last_seq + 1;
    } else {
        $new_seq = 1;
    }
    $order_number = sprintf('%s-%s-%06d', $prefix, $date_part, $new_seq);
}

try {
    $conn->beginTransaction();
    foreach ($selected_items as &$item) {
        if (isset($item['image_path'])) {
            $item['image_path'] = basename($item['image_path']);
        }
    }
    unset($item);
    $stmt = $conn->prepare("
        INSERT INTO pre_orders (order_number, user_id, items, phone, total_amount, status, payment_date) 
        VALUES (?, ?, ?, ?, ?, 'pending', NULL)
    ");
    $stmt->execute([
        $order_number,
        $_SESSION['user_id'],
        json_encode($selected_items),
        $phone,
        $total_amount
    ]);
    if (!empty($included_items)) {
        $chunk_size = 500;
        $user_id = $_SESSION['user_id'];
        foreach (array_chunk($included_items, $chunk_size) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND id IN ($placeholders)");
            $params = array_merge([$user_id], $chunk);
            $stmt->execute($params);
        }
    }
    $conn->commit();
} catch (PDOException $e) {
    $conn->rollBack();
} 