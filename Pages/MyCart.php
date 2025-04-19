<?php
session_start();
require_once '../Includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get cart items
$cart_query = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$cart_query->execute([$_SESSION['user_id']]);
$cart_items = $cart_query->fetchAll(PDO::FETCH_ASSOC);

$final_cart_items = [];
foreach ($cart_items as $cart_item) {
    // Debug: Log the cart item code
    error_log("Cart Item Code: " . $cart_item['item_code']);
    
    // Try to get inventory details for each cart item
    $stmt = $conn->prepare("SELECT item_name, price, image_path FROM inventory WHERE item_code = ?");
    $stmt->execute([$cart_item['item_code']]);
    $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($inventory_item) {
        // Debug: Log successful match
        error_log("Found matching inventory item for: " . $cart_item['item_code']);
        $final_cart_items[] = array_merge($cart_item, $inventory_item);
    } else {
        // Debug: Log failed match
        error_log("No matching inventory item found for: " . $cart_item['item_code']);
        
        // Try to find the item with a LIKE query to catch potential formatting differences
        $stmt = $conn->prepare("SELECT item_name, price, image_path FROM inventory WHERE item_code LIKE ?");
        $stmt->execute(['%' . $cart_item['item_code'] . '%']);
        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($inventory_item) {
            error_log("Found similar inventory item using LIKE query for: " . $cart_item['item_code']);
            $final_cart_items[] = array_merge($cart_item, $inventory_item);
        } else {
            $final_cart_items[] = array_merge($cart_item, [
                'item_name' => 'Item no longer available',
                'price' => 0,
                'image_path' => 'default.jpg'
            ]);
        }
    }
}

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
<?php
    include("../Includes/header.php");
    ?>

    <div class="cart-page">
        <div class="cart-header">
            <div class="header-content">
                <h1><i class="fas fa-shopping-cart"></i> My Cart</h1>
                <div class="cart-count">
                    <?php echo count($final_cart_items); ?> items
                </div>
            </div>
        </div>

        <div class="cart-content">
            <?php if (!empty($final_cart_items)): ?>
                <div class="cart-grid">
                    <div class="cart-items">
                        <?php foreach ($final_cart_items as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $cart_total += $subtotal;
                        ?>
                            <div class="cart-item">
                                <div class="item-image">
                                    <img src="../uploads/itemlist/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                </div>
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                    <div class="item-meta">
                                        <span class="item-price">₱<?php echo number_format($item['price'], 2); ?></span>
                                        <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="item-subtotal">
                                        Subtotal: ₱<?php echo number_format($subtotal, 2); ?>
                                    </div>
                                </div>
                                <button onclick="removeFromCart('<?php echo $item['item_code']; ?>')" class="remove-btn" title="Remove item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Total Items</span>
                                <span><?php echo count($final_cart_items); ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total Amount</span>
                                <span>₱<?php echo number_format($cart_total, 2); ?></span>
                            </div>
                        </div>
                        <a href="ProPreOrder.php" class="checkout-btn">
                            <i class="fas fa-lock"></i>
                            Proceed to Pre Order
                        </a>
                        <a href="ProItemList.php" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-basket"></i>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="ProItemList.php" class="shop-now-btn">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .cart-page {
            padding-top: 80px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .cart-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #005a94 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            color: white;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-count {
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            backdrop-filter: blur(5px);
            font-weight: 500;
        }

        .cart-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .cart-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .cart-item {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            gap: 2rem;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .item-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            margin: 0 0 0.5rem 0;
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .item-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .item-price {
            font-weight: 600;
        }

        .item-subtotal {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.5rem;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .remove-btn:hover {
            color: #c82333;
            opacity: 1;
            transform: scale(1.1);
        }

        .cart-summary {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            position: sticky;
            top: 100px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .cart-summary h2 {
            margin: 0 0 1.5rem 0;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .summary-details {
            margin-bottom: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            color: #666;
        }

        .summary-row.total {
            border-bottom: none;
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 1rem;
        }

        .checkout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .checkout-btn:hover {
            background: yellow;
            color: black;
            transform: translateY(-2px);
        }

        .continue-shopping {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 1rem;
            background: #f8f9fa;
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .continue-shopping:hover {
            background: #e9ecef;
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1.5rem;
        }

        .empty-cart h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .shop-now-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .shop-now-btn:hover {
            background: var(--primary-color-dark);
            transform: translateY(-2px);
        }

        @media (max-width: 1024px) {
            .cart-grid {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .cart-page {
                padding-top: 70px;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding: 1rem;
            }

            .cart-content {
                padding: 1rem;
            }

            .cart-item {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }

            .item-image {
                width: 100px;
                height: 100px;
                margin: 0 auto;
            }

            .item-meta {
                justify-content: center;
            }

            .remove-btn {
                margin-top: 1rem;
            }
        }
    </style>

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