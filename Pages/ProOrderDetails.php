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
    error_log("First item: " . print_r($first_item, true)); // Debug log
    
    try {
        // Get the item code directly from inventory using the item name
        $stmt = $conn->prepare("SELECT item_code FROM inventory WHERE item_name = ?");
        $stmt->execute([$first_item['item_name']]);
        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Inventory item found: " . print_r($inventory_item, true)); // Debug log
        
        if ($inventory_item) {
            $order_number = $inventory_item['item_code'] . '-' . date('YmdHis');
            error_log("Generated order number: " . $order_number); // Debug log
        } else {
            error_log("No inventory item found for name: " . $first_item['item_name']); // Debug log
            // Fallback to using the item code from cart if inventory lookup fails
            $order_number = $first_item['item_code'] . '-' . date('YmdHis');
        }
    } catch (PDOException $e) {
        error_log("Error getting inventory item: " . $e->getMessage());
        // Fallback to using the item code from cart if query fails
        $order_number = $first_item['item_code'] . '-' . date('YmdHis');
    }
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
                        <?php else: ?>
                            <p>No items in order</p>
                        <?php endif; ?>
                    </div>
                    <div class="order-total">
                        <span>Total Amount:</span>
                        <span class="total-amount">₱<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                </div>

                <button class="back-home-btn" onclick="window.location.href='MyOrders.php'">
                    Back to My Orders
                </button>
            </div>
        </div>
    </div>

    <style>
        .order-details {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .order-details h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .details-group {
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }

        .details-group p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .details-group p:last-child {
            margin-bottom: 0;
        }

        .details-group strong {
            color: #333;
            font-weight: 600;
        }

        .details-group span {
            color: #555;
            font-weight: 500;
        }

        .customer-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }

        .customer-info h4 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--primary-color);
        }

        .customer-info p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .customer-info p:last-child {
            margin-bottom: 0;
        }

        .customer-info strong {
            color: #333;
            font-weight: 600;
            min-width: 120px;
        }

        .customer-info span {
            color: #555;
            font-weight: 500;
            text-align: right;
            flex: 1;
            margin-left: 1rem;
        }

        .ordered-items {
            margin-top: 2rem;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .ordered-items h4 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--primary-color);
        }

        .item-list {
            margin-top: 1rem;
        }

        .order-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .order-item.header {
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .item-name {
            font-weight: 500;
        }

        .item-size {
            text-align: center;
        }

        .item-quantity {
            text-align: center;
        }

        .item-price {
            text-align: right;
            font-weight: 500;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid var(--primary-color);
            font-weight: 600;
            font-size: 1.1em;
        }

        .total-amount {
            color: var(--primary-color);
        }

        .back-home-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin-top: 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .back-home-btn:hover {
            background: yellow;
            color: black;
            transform: translateY(-2px);
        }

        .success-message {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background: #e8f5e9;
            border-radius: 8px;
        }

        .success-message i {
            font-size: 3rem;
            color: #2e7d32;
            margin-bottom: 1rem;
        }

        .success-message h2 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        .success-message p {
            color: #1b5e20;
            font-size: 1.1rem;
        }
    </style>
</body>
</html>