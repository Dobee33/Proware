<?php
session_start();
require_once '../Includes/connection.php';
include("../Includes/Header.php");

// Get the submitted form data
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$course = $_POST['course'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$cart_items = json_decode($_POST['cart_items'] ?? '[]', true);
$included_items = json_decode($_POST['included_items'] ?? '[]', true);
$total_amount = $_POST['total_amount'] ?? 0;

// Filter cart items to only include selected items
$selected_items = array_filter($cart_items, function($item) use ($included_items) {
    return in_array($item['id'], $included_items);
});

// Reindex the array
$selected_items = array_values($selected_items);

// Generate order number based on the first item's code
$order_number = '';
if (!empty($selected_items)) {
    $first_item = $selected_items[0];
    $order_number = 'PRO-' . $first_item['item_code'] . '-' . date('YmdHis');
}

// Save order to database
try {
    // Begin transaction
    $conn->beginTransaction();

    // Insert into pre_orders table
    $stmt = $conn->prepare("
        INSERT INTO pre_orders (order_number, user_id, items, phone, total_amount, status) 
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $order_number,
        $_SESSION['user_id'],
        json_encode($selected_items),
        $phone,
        $total_amount
    ]);

    // Delete only the included items from cart
    $placeholders = str_repeat('?,', count($included_items) - 1) . '?';
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND id IN ($placeholders)");
    $params = array_merge([$_SESSION['user_id']], $included_items);
    $stmt->execute($params);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Pre Order Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="../CSS/ProOrderDetails.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton+SC&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                    <p><strong>Course/Strand:</strong> <span><?php echo htmlspecialchars($course); ?></span></p>
                    <p><strong>Email:</strong> <span><?php echo htmlspecialchars($email); ?></span></p>
                    <p><strong>Phone:</strong> <span><?php echo htmlspecialchars($phone); ?></span></p>
                </div>

                <div class="ordered-items">
                    <h4>Ordered Items</h4>
                    <div class="item-list">
                        <?php if (!empty($selected_items)): ?>
                            <?php foreach ($selected_items as $item): ?>
                                <div class="order-item">
                                    <span class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></span>
                                    <span class="item-quantity">x<?php echo $item['quantity']; ?></span>
                                    <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No items in order</p>
                        <?php endif; ?>
                    </div>
                    <div class="order-total">
                        <span>Total Amount:</span>
                        <span class="total-amount">₱<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                </div>

                <button class="back-home-btn" onclick="window.location.href='home.php'">
                    Back to Home
                </button>
            </div>
        </div>
    </div>

    <style>
       
    </style>
</body>
</html>