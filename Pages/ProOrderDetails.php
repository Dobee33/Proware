<?php
session_start();
require_once '../Includes/connection.php';

// Get the submitted form data
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

// Debugging logs
error_log('Cart Items: ' . print_r($cart_items, true));
error_log('Included Items: ' . print_r($included_items, true));

if (empty($cart_items) || empty($included_items)) {
    error_log('Cart or included items are empty!');
    // Optionally, you can show a user-friendly error or redirect here
}

// Filter cart items to only include selected items
$selected_items = array_filter($cart_items, function($item) use ($included_items) {
    return in_array($item['id'], $included_items);
});

// Reindex the array
$selected_items = array_values($selected_items);

// Generate order number in the format SI-<mmdd>-<sequential>
$order_number = '';
if (!empty($selected_items)) {
    $prefix = 'SI';
    $date_part = date('md');
    $today = date('Y-m-d');
    
    // Query for the latest order number for today
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

// Save order to database
try {
    // Begin transaction
    $conn->beginTransaction();

    // Process selected items to store only the filename for image paths
    foreach ($selected_items as &$item) {
        if (isset($item['image_path'])) {
            // Extract only the filename from the path
            $item['image_path'] = basename($item['image_path']);
        }
    }
    unset($item); // Break the reference

    // Insert into pre_orders table
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

    // Delete only the included items from cart
    if (!empty($included_items)) {
        $chunk_size = 500; // Safe chunk size for most MySQL setups
        $user_id = $_SESSION['user_id'];
        foreach (array_chunk($included_items, $chunk_size) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND id IN ($placeholders)");
            $params = array_merge([$user_id], $chunk);
            $stmt->execute($params);
        }
    } else {
        error_log('No included items to delete from cart.');
    }

    // Commit transaction
    $conn->commit();

} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    error_log("Error saving order: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pre Order Details</title>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="../CSS/ProOrderDetails.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="preorder-container">
        <div class="process-steps">
            <div class="step completed">1. Pre Order Cart</div>
            <div class="step completed">2. Checkout Details</div>
            <div class="step active">3. Pre Order Details</div>
        </div>

        <div class="order-success">
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>Thank You for Your Pre-Order!</h2>
                <p>Your order has been successfully placed.</p>
            </div>

            <div class="order-details">
                <h3>Order Details</h3>
                <div class="details-group">
                    <p><strong>Order Number:</strong> <span><?php echo htmlspecialchars($order_number); ?></span></p>
                    <p><strong>Date:</strong> <span><?php echo date('F d, Y'); ?></span></p>
                </div>

                <div class="customer-info">
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> <span><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></span></p>
                    <p><strong>Student Number:</strong> <span><?php echo htmlspecialchars($_POST['studentNumber']); ?></span></p>
                    <p><strong>Course/Strand:</strong> <span><?php echo htmlspecialchars($course); ?></span></p>
                    <p><strong>Email:</strong> <span><?php echo htmlspecialchars($email); ?></span></p>
                    <p><strong>Phone:</strong> <span><?php echo htmlspecialchars($phone); ?></span></p>
                </div>

                <div class="ordered-items">
                    <h4>Ordered Items</h4>
                    <div class="item-list">
                        <?php if (!empty($selected_items)): ?>
                            <div class="order-item header">
                                <span class="item-name">Item</span>
                                <span class="item-size">Size</span>
                                <span class="item-quantity">Qty</span>
                                <span class="item-price">Price</span>
                            </div>
                            <?php foreach ($selected_items as $item): 
                                // Remove size suffix from item name
                                $clean_name = rtrim($item['item_name'], " SMLX234567");
                            ?>
                                <div class="order-item">
                                    <span class="item-name"><?php echo htmlspecialchars($clean_name); ?></span>
                                    <span class="item-size"><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></span>
                                    <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                    <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <div class="order-total">
                                <span>Total Amount:</span>
                                <span class="total-amount">₱<?php echo number_format($total_amount, 2); ?></span>
                            </div>
                        <?php else: ?>
                            <p>No items in order</p>
                        <?php endif; ?>
                    </div>
                </div>

                <button class="back-home-btn" onclick="window.location.href='MyOrders.php'">
                    View Orders
                </button>
            </div>
        </div>
    </div>

    <script src="../Javascript/ProOrderDetails.js"></script>
</body>

</html>