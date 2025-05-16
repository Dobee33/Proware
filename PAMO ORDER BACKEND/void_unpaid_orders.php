<?php
date_default_timezone_set('Asia/Manila');
require_once '../Includes/connection.php';
require_once '../Includes/notifications.php';

$query = "
    SELECT po.*, a.first_name, a.last_name, a.email
    FROM pre_orders po
    JOIN account a ON po.user_id = a.id
    WHERE po.status = 'approved'
    AND po.payment_date IS NULL
    AND po.created_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE)
";

$stmt = $conn->prepare($query);
$stmt->execute();
$unpaid_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Output number of orders found
file_put_contents(__DIR__ . '/void_debug.log', date('Y-m-d H:i:s') . " - Found " . count($unpaid_orders) . " unpaid approved orders\n", FILE_APPEND);

foreach ($unpaid_orders as $order) {
    try {
        // Start transaction
        $conn->beginTransaction();

        // Update order status to voided
        $updateStmt = $conn->prepare("UPDATE pre_orders SET status = 'voided' WHERE id = ?");
        $updateStmt->execute([$order['id']]);

        // Increment strike for the user
        $strikeStmt = $conn->prepare("UPDATE account SET pre_order_strikes = pre_order_strikes + 1, last_strike_time = NOW() WHERE id = ?");
        $strikeStmt->execute([$order['user_id']]);
        // Check if strikes >= 3, then set is_strike = 1
        $checkStrikeStmt = $conn->prepare("SELECT pre_order_strikes FROM account WHERE id = ?");
        $checkStrikeStmt->execute([$order['user_id']]);
        $strikes = $checkStrikeStmt->fetchColumn();
        if ($strikes >= 3) {
            $blockStmt = $conn->prepare("UPDATE account SET is_strike = 1 WHERE id = ?");
            $blockStmt->execute([$order['user_id']]);
        }

        // Debug: Log each voided order
        file_put_contents(__DIR__ . '/void_debug.log', date('Y-m-d H:i:s') . " - Voided order ID: {$order['id']}\n", FILE_APPEND);

        // Create notification for the user
        $message = "Your order #{$order['order_number']} has been voided because payment was not made within 5 minutes. Please place a new order if you still wish to purchase these items.";
        createNotification($conn, $order['user_id'], $message, $order['order_number'], 'voided');

        // Log the activity
        $order_items = json_decode($order['items'], true);
        foreach ($order_items as $item) {
            $activity_description = "Voided - Order #: {$order['order_number']}, Item: {$item['item_name']}, Quantity: {$item['quantity']}";
            $activityStmt = $conn->prepare(
                "INSERT INTO activities (
                    action_type,
                    description,
                    item_code,
                    user_id,
                    timestamp
                ) VALUES (?, ?, ?, ?, NOW())"
            );
            $activityStmt->execute([
                'Voided',
                $activity_description,
                $item['item_code'],
                null
            ]);
        }

        // Commit transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error voiding order {$order['id']}: " . $e->getMessage());
    }
} 