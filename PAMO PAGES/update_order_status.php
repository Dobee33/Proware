<?php
session_start();
require_once '../Includes/connection.php';
require_once '../Includes/notifications.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
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

        // Get order details first
        $stmt = $conn->prepare("SELECT po.*, a.id as user_id 
                               FROM pre_orders po 
                               JOIN account a ON po.user_id = a.id 
                               WHERE po.id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception('Order not found');
        }

        // Update order status
        $updateStmt = $conn->prepare("UPDATE pre_orders SET status = ? WHERE id = ?");
        if (!$updateStmt->execute([$status, $order_id])) {
            throw new Exception('Failed to update order status');
        }

        // Create notification message based on status
        $message = "Your order #{$order['order_number']} has been " . 
                  ($status === 'approved' ? 'approved! You can now proceed with the payment.' : 'rejected.');

        // Create notification
        if (!createNotification($conn, $order['user_id'], $message, $order['order_number'], $status)) {
            throw new Exception('Failed to create notification');
        }

        // Commit transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = 'Order status updated successfully';
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $response['success'] = false;
        $response['message'] = 'Error updating order status: ' . $e->getMessage();
        
        // Add debug information
        if (isset($order)) {
            $response['debug'] = [
                'order_id' => $order_id,
                'user_id' => $order['user_id'] ?? 'not found',
                'order_number' => $order['order_number'] ?? 'not found',
                'status' => $status
            ];
        }
    }

    echo json_encode($response);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 