<?php
session_start();
require_once '../Includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get cart items
$cart_query = $conn->prepare("
    SELECT c.*, i.item_name, i.price, i.image_path 
    FROM cart c 
    JOIN inventory i ON c.item_code = i.item_code 
    WHERE c.user_id = ?
");
$cart_query->execute([$_SESSION['user_id']]);
$cart_items = $cart_query->fetchAll(PDO::FETCH_ASSOC);
$cart_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="../CSS/MyOrders.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="../CSS/sidebar_MyOrder.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="page-wrapper">
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="order-navigation">
                    <a href="MyOrders.php" class="nav-tab">
                        <i class="fas fa-box"></i> My Orders
                    </a>
                    <a href="MyCart.php" class="nav-tab active">
                        <i class="fas fa-shopping-cart"></i> My Cart
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Cart Section -->
            <div class="cart-section <?php echo empty($cart_items) ? 'empty' : ''; ?>">
                <h2><i class="fas fa-shopping-cart"></i> My Cart (<?php echo count($cart_items); ?> items)</h2>
                <?php if (!empty($cart_items)): ?>
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $cart_total += $subtotal;
                        ?>
                            <div class="cart-item">
                                <div class="item-details">
                                    <div class="item-image">
                                        <img src="../uploads/itemlist/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                    </div>
                                    <div class="item-info">
                                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                        <p class="item-price">₱<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></p>
                                    </div>
                                </div>
                                <div class="item-total">
                                    <p>₱<?php echo number_format($subtotal, 2); ?></p>
                                    <button onclick="removeFromCart('<?php echo $item['item_code']; ?>')" class="remove-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-total">
                        <h3>Total: ₱<?php echo number_format($cart_total, 2); ?></h3>
                        <a href="ProCheckout.php" class="checkout-btn">Proceed to Checkout</a>
                    </div>
                <?php else: ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Your cart is empty</p>
                        <a href="ProItemList.php" class="shop-now-btn">Shop Now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function removeFromCart(itemCode) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_code=${itemCode}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error removing item from cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item from cart');
                });
            }
        }
    </script>
</body>
</html> 