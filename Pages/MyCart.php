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
    $stmt = $conn->prepare("SELECT item_name, price, image_path, category FROM inventory WHERE item_code = ?");
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
        $stmt = $conn->prepare("SELECT item_name, price, image_path, category FROM inventory WHERE item_code LIKE ?");
        $stmt->execute(['%' . $cart_item['item_code'] . '%']);
        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($inventory_item) {
            error_log("Found similar inventory item using LIKE query for: " . $cart_item['item_code']);
            $final_cart_items[] = array_merge($cart_item, $inventory_item);
        } else {
            $final_cart_items[] = array_merge($cart_item, [
                'item_name' => 'Item no longer available',
                'price' => 0,
                'image_path' => 'default.jpg',
                'category' => ''
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
        <div class="heading-cart">
            <div class="header-content">
                <h1><i class="fas fa-shopping-cart"></i> My Cart</h1>
                
            </div>
        </div>

        <div class="cart-content">
            <?php if (!empty($final_cart_items)): ?>
                <div class="cart-grid">
                    <div class="cart-items-container">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th class="image-col">Image</th>
                                    <th class="item-col">Item Name</th>
                                    <th class="size-col">Size</th>
                                    <th class="price-col">Price</th>
                                    <th class="quantity-col">Quantity</th>
                                    <th class="subtotal-col">Subtotal</th>
                                    <th class="action-col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($final_cart_items as $item):
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $cart_total += $subtotal;
                                ?>
                                <tr class="cart-row">
                                    <td class="image-col">
                                        <div class="item-image">
                                            <img src="../uploads/itemlist/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                        </div>
                                    </td>
                                    <td class="item-col">
                                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                    </td>
                                    <td class="size-col">
                                        <?php if (!empty($item['size'])): ?>
                                            <span class="item-size"><?php echo htmlspecialchars($item['size']); ?></span>
                                        <?php else: ?>
                                            <span class="item-size-na">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="price-col">
                                        <span class="item-price">₱<?php echo number_format($item['price'], 2); ?></span>
                                    </td>
                                    <td class="quantity-col">
                                        <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                    </td>
                                    <td class="subtotal-col">
                                        <span class="item-subtotal">₱<?php echo number_format($subtotal, 2); ?></span>
                                    </td>
                                    <td class="action-col">
                                        <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="remove-btn" title="Remove item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                        <div class="button-container">
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
            background-color: #f4ebb6;
        }

        .heading-cart {
            background: linear-gradient(135deg, var(--primary-color) 0%, #005a94 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            margin-top: -10px;
            color: white;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: center;
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

        .cart-items-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table td {
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
            height: 100px; /* Set a consistent height for all cells */
        }

        .cart-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #eee;
            text-align: center;
            padding: 1.5rem 1rem;
        }

        .cart-row {
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease;
        }

        .cart-row:hover {
            background-color: #f9f9f9;
        }

        .cart-row:last-child {
            border-bottom: none;
        }

        .image-col {
            width: 120px;
        }

        .item-col {
            width: 300px;
        }

        .size-col {
            width: 100px;
            text-align: center;
        }

        .price-col {
            width: 120px;
            text-align: center;
        }

        .quantity-col {
            width: 80px;
            text-align: center;
        }

        .subtotal-col {
            width: 150px;
            text-align: center;
        }

        .action-col {
            width: 50px;
            text-align: center;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            margin: 0 auto; /* Center the image */
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-col h3 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 500;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        
        .item-name {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .item-price, .item-quantity, .item-subtotal {
            display: inline-block;
            font-weight: 600;
            color: #444;
        }
        
        .item-size {
            display: inline-block;
            font-weight: 500;
            background-color: #e6f2ff;
            color: #0066cc;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .item-size-na {
            color: #999;
            font-style: italic;
        }

        .item-quantity {
            font-weight: 500;
            background-color: #f5f5f5;
            padding: 4px 10px;
            border-radius: 4px;
        }

        .item-subtotal {
            font-weight: 600;
            color: var(--primary-color);
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
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin: 0 0.5rem;
        }

        .cart-summary h2 {
            margin: 0 0 1rem 0;
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .summary-details {
            margin-bottom: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
            color: #666;
        }

        .summary-row.total {
            border-bottom: none;
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 2px solid #000;
            margin-bottom: 1.5rem;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .checkout-btn,
        .continue-shopping {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 100%;
            padding: 0.875rem 1rem;
            text-align: center;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .checkout-btn {
            background: var(--primary-color);
            color: white;
        }

        .continue-shopping {
            background: #a6d1e6;
            color: var(--primary-color);
            border: none;
            margin: 0;
        }

        .checkout-btn i,
        .continue-shopping i {
            margin-right: 0.5rem;
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
                margin: 0 0.5rem;
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
                padding: 0.5rem;
            }
            
            .cart-table {
                min-width: 650px; /* Force horizontal scroll on mobile */
            }
            
            .cart-items-container {
                margin: 0 0.5rem 1rem;
            }

            .cart-summary {
                padding: 1.25rem;
            }

            .button-container {
                padding: 0;
            }

            .checkout-btn,
            .continue-shopping {
                padding: 0.75rem;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .cart-content {
                padding: 0.25rem;
            }

            .cart-summary {
                padding: 1rem;
            }

            .cart-summary h2 {
                font-size: 1.2rem;
                margin-bottom: 0.75rem;
            }

            .summary-details {
                margin-bottom: 1rem;
            }

            .summary-row {
                padding: 0.5rem 0;
                font-size: 0.9rem;
            }

            .summary-row.total {
                font-size: 1rem;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
            }

            .button-container {
                padding: 0;
            }

            .checkout-btn,
            .continue-shopping {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>

    <script>
        function removeFromCart(itemId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${itemId}`
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