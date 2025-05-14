<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../Includes/connection.php';
require_once '../Includes/notifications.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Debug: Log incoming request
    error_log("Received status update request: " . json_encode($_POST));
    
    // Validate input parameters
    if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
        $response['message'] = 'Missing required parameters';
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // Debug: Log the query we're about to execute
        error_log("Fetching order details for ID: " . $order_id);

        // Get order details first
        $stmt = $conn->prepare("SELECT * FROM pre_orders WHERE id = ? FOR UPDATE");
        
        if (!$stmt->execute([$order_id])) {
            throw new Exception('Failed to get order details');
        }
        
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Log what we found
        error_log("Order query result: " . json_encode($order));

        if (!$order) {
            throw new Exception('Order not found');
        }

        // If status is being changed to completed, process inventory updates
        if ($status === 'completed') {
            error_log("Processing completed order: " . $order_id);
            
            // Decode the items JSON
            $order_items = json_decode($order['items'], true);
            if (!$order_items) {
                throw new Exception('Invalid order items data');
            }
            
            foreach ($order_items as $item) {
                error_log("Processing item: " . json_encode($item));
                
                // Get current inventory with lock
                $stockStmt = $conn->prepare("SELECT * FROM inventory WHERE item_code = ? FOR UPDATE");
                if (!$stockStmt->execute([$item['item_code']])) {
                    throw new Exception('Failed to get inventory for item: ' . $item['item_code']);
                }
                
                $inventory = $stockStmt->fetch(PDO::FETCH_ASSOC);
                if (!$inventory) {
                    throw new Exception('Item no longer exists in inventory: ' . $item['item_code']);
                }
                
                error_log("Current inventory state: " . json_encode($inventory));
                
                // Verify sufficient quantity
                $new_quantity = $inventory['actual_quantity'] - $item['quantity'];
                if ($new_quantity < 0) {
                    throw new Exception('Insufficient quantity for item: ' . $inventory['item_name']);
                }
                
                // Update inventory with optimistic locking
                $updateStockStmt = $conn->prepare(
                    "UPDATE inventory 
                    SET actual_quantity = ?,
                        sold_quantity = sold_quantity + ?,
                        status = CASE 
                            WHEN ? <= 0 THEN 'Out of Stock'
                            WHEN ? <= 10 THEN 'Low Stock'
                            ELSE 'In Stock'
                        END
                    WHERE item_code = ? AND actual_quantity = ?"
                );
                
                if (!$updateStockStmt->execute([
                    $new_quantity,
                    $item['quantity'],
                    $new_quantity,
                    $new_quantity,
                    $item['item_code'],
                    $inventory['actual_quantity']
                ])) {
                    throw new Exception('Failed to update inventory for item: ' . $inventory['item_name']);
                }
                
                if ($updateStockStmt->rowCount() === 0) {
                    throw new Exception('Item ' . $inventory['item_name'] . ' was modified by another transaction. Please try again.');
                }
                
                error_log("Updated inventory quantity for {$inventory['item_name']}: {$inventory['actual_quantity']} -> {$new_quantity}");
                
                // Record the sale
                $saleStmt = $conn->prepare(
                    "INSERT INTO sales (
                        transaction_number, 
                        item_code, 
                        size, 
                        quantity, 
                        price_per_item, 
                        total_amount, 
                        sale_date
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW())"
                );
                
                $transaction_number = $order['order_number'];
                $size = $item['size'] ?? 'One Size';
                $quantity = $item['quantity'];
                $price_per_item = $item['price'];
                $total_amount = $item['price'] * $item['quantity'];
                
                if (!$saleStmt->execute([
                    $transaction_number,
                    $item['item_code'],
                    $size,
                    $quantity,
                    $price_per_item,
                    $total_amount
                ])) {
                    throw new Exception('Failed to record sale for item: ' . $inventory['item_name']);
                }
                
                // Log activity
                $activity_description = "Order completed - Order #: {$order['order_number']}, Item: {$inventory['item_name']}, Quantity: {$item['quantity']}";
                $activityStmt = $conn->prepare(
                    "INSERT INTO activities (
                        action_type,
                        description,
                        item_code,
                        user_id,
                        timestamp
                    ) VALUES (?, ?, ?, ?, NOW())"
                );
                
                if (!$activityStmt->execute([
                    'Order Completed',
                    $activity_description,
                    $item['item_code'],
                    $order['user_id']
                ])) {
                    throw new Exception('Failed to log activity for item: ' . $inventory['item_name']);
                }
                
                error_log("Recorded sale and activity for {$inventory['item_name']}");
            }
        }

        // Update order status
        $updateStmt = $conn->prepare("UPDATE pre_orders SET status = ? WHERE id = ?");
        if (!$updateStmt->execute([$status, $order_id])) {
            throw new Exception('Failed to update order status');
        }
        
        // If status is completed, record the payment date
        if ($status === 'completed') {
            $paymentDateStmt = $conn->prepare("UPDATE pre_orders SET payment_date = NOW() WHERE id = ?");
            if (!$paymentDateStmt->execute([$order_id])) {
                throw new Exception('Failed to record payment date');
            }
        }

        // Create notification message based on status  
        $message = "Your order #{$order['order_number']} has been " . 
                  ($status === 'approved' ? 'approved! You can now proceed with the payment.' : 
                  ($status === 'rejected' ? 'rejected.' :
                  ($status === 'completed' ? 'completed. Thank you for your purchase!' : 'updated.')));

        try {
            error_log("Creating notification for user: " . $order['user_id']);
            createNotification($conn, $order['user_id'], $message, $order['order_number'], $status);
        } catch (Exception $e) {
            error_log("Failed to create notification: " . $e->getMessage());
            // Continue processing even if notification fails
        }

        // Commit transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = 'Order status updated successfully';
        $response['debug'] = [
            'order_id' => $order_id,
            'status' => $status,
            'order_number' => $order['order_number'],
            'user_info' => [
                'id' => $order['user_id'],
                'name' => $order['first_name'] . ' ' . $order['last_name']
            ]
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error updating order status: " . $e->getMessage());
        $response['success'] = false;
        $response['message'] = 'Error updating order status: ' . $e->getMessage();
        $response['debug'] = [
            'order_id' => $order_id,
            'status' => $status,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }

    echo json_encode($response);
    ob_end_flush();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    ob_end_flush();
} 