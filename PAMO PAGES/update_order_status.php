<?php
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
        $stmt = $conn->prepare("SELECT po.*, a.id as user_id, a.first_name, a.last_name 
                               FROM pre_orders po 
                               JOIN account a ON po.user_id = a.id 
                               WHERE po.id = ?");
        
        if (!$stmt->execute([$order_id])) {
            $error = $stmt->errorInfo();
            throw new Exception('Failed to fetch order details: ' . json_encode($error));
        }
        
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Log what we found
        error_log("Order query result: " . json_encode($order));

        if (!$order) {
            throw new Exception('Order not found for ID: ' . $order_id);
        }

        // If status is being changed to completed, deduct items from inventory
        if ($status === 'completed') {
            error_log("Deducting items from inventory for order: " . $order_id);
            
            // Decode the items JSON
            $order_items = json_decode($order['items'], true);
            
            foreach ($order_items as $item) {
                // Get the clean item name without size suffix
                $clean_name = rtrim($item['item_name'], " SMLX234567");
                
                // Get current actual_quantity from inventory
                $stockStmt = $conn->prepare("SELECT actual_quantity FROM inventory WHERE item_name = ?");
                if (!$stockStmt->execute([$clean_name])) {
                    throw new Exception('Failed to get inventory actual quantity for item: ' . $clean_name);
                }
                
                $inventory = $stockStmt->fetch(PDO::FETCH_ASSOC);
                if (!$inventory) {
                    throw new Exception('Item not found in inventory: ' . $clean_name);
                }
                
                $new_quantity = $inventory['actual_quantity'] - $item['quantity'];
                if ($new_quantity < 0) {
                    throw new Exception('Insufficient actual quantity for item: ' . $clean_name);
                }
                
                // Update inventory actual_quantity
                $updateStockStmt = $conn->prepare("UPDATE inventory SET actual_quantity = ? WHERE item_name = ?");
                if (!$updateStockStmt->execute([$new_quantity, $clean_name])) {
                    throw new Exception('Failed to update inventory actual quantity for item: ' . $clean_name);
                }
                
                error_log("Updated actual quantity for {$clean_name}: {$inventory['actual_quantity']} -> {$new_quantity}");
            }
        }

        // Update order status
        $updateStmt = $conn->prepare("UPDATE pre_orders SET status = ? WHERE id = ?");
        if (!$updateStmt->execute([$status, $order_id])) {
            $error = $updateStmt->errorInfo();
            throw new Exception('Failed to update order status: ' . json_encode($error));
        }
        
        // If status is completed, record the payment date
        if ($status === 'completed') {
            error_log("Recording payment date for order: " . $order_id);
            
            $paymentDateStmt = $conn->prepare("UPDATE pre_orders SET payment_date = NOW() WHERE id = ?");
            if (!$paymentDateStmt->execute([$order_id])) {
                $error = $paymentDateStmt->errorInfo();
                throw new Exception('Failed to record payment date: ' . json_encode($error));
            }
        }

        // Create notification message based on status
        $message = "Your order #{$order['order_number']} has been " . 
                  ($status === 'approved' ? 'approved! You can now proceed with the payment.' : 
                  ($status === 'rejected' ? 'rejected.' :
                  ($status === 'completed' ? 'completed. Thank you for your purchase!' : 'updated.')));

        // Try to create notification, but don't fail if it doesn't work
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
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 