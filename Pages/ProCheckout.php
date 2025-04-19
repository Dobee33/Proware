<?php
session_start(); // Start the session if you're using sessions
include("../Includes/Header.php");
require_once '../Includes/connection.php';

// Fetch user information
$stmt = $conn->prepare("SELECT * FROM account WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch cart items
$stmt = $conn->prepare("
    SELECT c.*, i.item_name, i.price, i.image_path 
    FROM cart c 
    JOIN inventory i ON c.item_code = i.item_code 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Get the cart items and included items from the form
$cart_items = json_decode($_POST['cart_items'], true);
$included_items = json_decode($_POST['included_items'], true);
$total_amount = $_POST['total_amount'];

// Filter cart items to only include selected items
$selected_items = array_filter($cart_items, function($item) use ($included_items) {
    return in_array($item['id'], $included_items);
});

// Reindex the array
$selected_items = array_values($selected_items);

// Store the selected items in session for later use
$_SESSION['checkout_items'] = $selected_items;
$_SESSION['checkout_total'] = $total_amount;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pre Order Checkout</title>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/ProCheckout.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="preorder-container">
        <div class="process-steps">
            <div class="step completed">1. Pre Order Cart</div>
            <div class="step active">2. Checkout Details</div>
            <div class="step">3. Pre Order Details</div>
        </div>

        <div class="checkout-content">
            <div class="checkout-form">
                <h2>Checkout Details</h2>
                <form action="ProOrderDetails.php" method="POST">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="course">Course/Strand</label>
                        <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($user['program_or_position']); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="phone-input">
                            <span class="country-code">+63</span>
                            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" maxlength="10" placeholder="9123456789" required>
                        </div>
                        <small class="phone-hint">Enter your 10-digit mobile number (e.g., 9123456789)</small>
                    </div>
                    <input type="hidden" name="cart_items" value='<?php echo json_encode($cart_items); ?>'>
                    <input type="hidden" name="included_items" value='<?php echo json_encode($included_items); ?>'>
                    <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                    <button type="submit" class="place-order-btn">Place Order</button>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-items">
                    <?php if (!empty($selected_items)): ?>
                        <div class="summary-header">
                            <span class="item-name-header">Item</span>
                            <span class="item-size-header">Size</span>
                            <span class="item-quantity-header">Qty</span>
                            <span class="item-price-header">Price</span>
                        </div>
                        <?php foreach ($selected_items as $item): 
                            // Remove size suffix from item name
                            $clean_name = rtrim($item['item_name'], " SMLX234567");
                        ?>
                            <div class="summary-item">
                                <span class="item-name"><?php echo htmlspecialchars($clean_name); ?></span>
                                <span class="item-size"><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></span>
                                <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-cart-message">No items selected for checkout</p>
                    <?php endif; ?>
                </div>
                <div class="summary-total">
                    <span>Total Amount:</span>
                    <span class="total-amount">₱<?php echo number_format($total_amount, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<style>
    .phone-input {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .country-code {
        padding: 8px;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 4px 0 0 4px;
    }
    
    .phone-input input {
        border-radius: 0 4px 4px 0;
        flex: 1;
    }
    
    .phone-hint {
        display: block;
        margin-top: 5px;
        color: #666;
        font-size: 0.8em;
    }
</style>