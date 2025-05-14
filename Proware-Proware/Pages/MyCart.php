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
    // Try to get inventory details for each cart item
    $stmt = $conn->prepare("SELECT item_name, price, image_path, category, actual_quantity FROM inventory WHERE item_code = ?");
    $stmt->execute([$cart_item['item_code']]);
    $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback logic for image
    if ($inventory_item && (empty($inventory_item['image_path']) || !file_exists('../' . $inventory_item['image_path']))) {
        // Get prefix before dash
        $prefix = explode('-', $cart_item['item_code'])[0];
        $stmt2 = $conn->prepare("SELECT image_path FROM inventory WHERE item_code LIKE ? AND image_path IS NOT NULL AND image_path != '' LIMIT 1");
        $likePrefix = $prefix . '-%';
        $stmt2->execute([$likePrefix]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($row2 && !empty($row2['image_path'])) {
            $inventory_item['image_path'] = $row2['image_path'];
        } else {
            $inventory_item['image_path'] = 'uploads/itemlist/default.png'; // fallback default
        }
    }
    
    if ($inventory_item) {
        $final_cart_items[] = array_merge($cart_item, $inventory_item);
    } else {
        // Try to find the item with a LIKE query to catch potential formatting differences
        $stmt = $conn->prepare("SELECT item_name, price, image_path, category, actual_quantity FROM inventory WHERE item_code LIKE ?");
        $stmt->execute(['%' . $cart_item['item_code'] . '%']);
        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Fallback logic for image
        if ($inventory_item && (empty($inventory_item['image_path']) || !file_exists('../' . $inventory_item['image_path']))) {
            $prefix = explode('-', $cart_item['item_code'])[0];
            $stmt2 = $conn->prepare("SELECT image_path FROM inventory WHERE item_code LIKE ? AND image_path IS NOT NULL AND image_path != '' LIMIT 1");
            $likePrefix = $prefix . '-%';
            $stmt2->execute([$likePrefix]);
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($row2 && !empty($row2['image_path'])) {
                $inventory_item['image_path'] = $row2['image_path'];
            } else {
                $inventory_item['image_path'] = 'uploads/itemlist/default.png';
            }
        }
        
        if ($inventory_item) {
            $final_cart_items[] = array_merge($cart_item, $inventory_item);
        } else {
            $final_cart_items[] = array_merge($cart_item, [
                'item_name' => 'Item no longer available',
                'price' => 0,
                'image_path' => 'uploads/itemlist/default.png',
                'category' => '',
                'actual_quantity' => 0
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
                                    <td class="image-col" data-label="Image">
                                        <div class="item-image">
                                            <img src="../<?php echo htmlspecialchars($item['image_path']); ?>"
                                                alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                                                
                                        </div>
                                    </td>
                                    <td class="item-col" data-label="Item Name">
                                        <div class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                    </td>
                                    <td class="size-col" data-label="Size">
                                        <?php if (!empty($item['size'])): ?>
                                            <span class="item-size"><?php echo htmlspecialchars($item['size']); ?></span>
                                        <?php else: ?>
                                            <span class="item-size-na">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="price-col" data-label="Price">
                                        <span class="item-price">₱<?php echo number_format($item['price'], 2); ?></span>
                                    </td>
                                    <td class="quantity-col" data-label="Quantity">
                                        <div class="quantity-control">
                                            <button type="button" class="qty-btn minus">-</button>
                                            <input type="number" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $item['actual_quantity']; ?>" 
                                                   class="qty-input" 
                                                   data-item-id="<?php echo $item['id']; ?>"
                                                   data-item-code="<?php echo $item['item_code']; ?>"
                                                   data-max-stock="<?php echo $item['actual_quantity']; ?>">
                                            <button type="button" class="qty-btn plus">+</button>
                                        </div>
                                    </td>
                                    <td class="subtotal-col" data-label="Subtotal">
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
                    <!-- MOBILE CART CARDS START -->
                    <div class="cart-items-mobile">
                        <?php foreach ($final_cart_items as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                        <div class="cart-item-card">
                            <div class="card-img-section">
                                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                            </div>
                            <div class="card-details-section">
                                <div class="card-title-row">
                                    <div class="card-item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                    <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="remove-btn" title="Remove item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="card-meta-row">
                                    <span class="card-item-size">
                                        <?php echo !empty($item['size']) ? htmlspecialchars($item['size']) : '-'; ?>
                                    </span>
                                </div>
                                <div class="card-price-row">
                                    <span class="card-item-price">₱<?php echo number_format($item['price'], 2); ?></span>
                                </div>
                                <div class="card-qty-row">
                                    <div class="quantity-control">
                                        <button type="button" class="qty-btn minus">-</button>
                                        <input type="number" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['actual_quantity']; ?>" 
                                               class="qty-input" 
                                               data-item-id="<?php echo $item['id']; ?>"
                                               data-item-code="<?php echo $item['item_code']; ?>"
                                               data-max-stock="<?php echo $item['actual_quantity']; ?>">
                                        <button type="button" class="qty-btn plus">+</button>
                                    </div>
                                    <span class="card-item-subtotal">Subtotal: ₱<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- MOBILE CART CARDS END -->
                    <div class="cart-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Total Items</span>
                                <span><?php 
                                    $total_quantity = 0;
                                    foreach ($final_cart_items as $item) {
                                        $total_quantity += $item['quantity'];
                                    }
                                    echo $total_quantity; 
                                ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total Amount</span>
                                <span>₱<?php echo number_format($cart_total, 2); ?></span>
                            </div>
                        </div>
                        <div class="button-container">
                            <a href="ProPreOrder.php" class="checkout-btn">
                                <i class="fas fa-lock"></i>
                                Proceed to Order
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
            color: yellow;
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
            color: var(--primary-color);
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
            background: yellow;
            color: black;
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
                width: 100%;
            }

            .cart-items-container {
                margin: 0 0.5rem 1rem;
                width: 100%;
            }

            .cart-table td {
                display: table-cell;
                vertical-align: middle;
            }

            .cart-table tr {
                display: table-row;
            }

            .cart-table tbody {
                display: table-row-group;
            }

            .size-col, .price-col, .quantity-col {
                min-width: 80px;
                text-align: center;
            }

            .item-size, .item-price, .quantity-control {
                display: inline-block;
                text-align: center;
            }

            .quantity-control {
                min-width: 90px;
                display: flex;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .cart-content {
                padding: 0.25rem;
            }

            .cart-items-container {
                margin: 0 0.25rem 1rem;
            }

            .size-col, .price-col, .quantity-col {
                min-width: 60px;
            }

            .cart-table td::before {
                width: 100px;
                font-size: 0.9rem;
            }

            .item-image {
                width: 80px;
                height: 80px;
            }

            .item-name {
                font-size: 0.95rem;
            }

            .item-size, .item-quantity {
                font-size: 0.9rem;
            }

            .item-subtotal {
                font-size: 1rem;
            }

            .cart-summary {
                padding: 1rem;
            }

            .cart-summary h2 {
                font-size: 1.2rem;
                margin-bottom: 0.75rem;
            }
        }

        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .qty-btn {
            background-color: #f0f0f0;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .qty-btn:hover {
            background-color: #e0e0e0;
        }

        .qty-input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px;
            font-weight: 500;
        }

        .qty-input::-webkit-inner-spin-button,
        .qty-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hide mobile card layout by default */
        .cart-items-mobile {
            display: none;
        }
        /* Show table, hide cards on desktop */
        @media (min-width: 769px) {
            .cart-items-mobile {
                display: none !important;
            }
            .cart-items-container {
                display: block;
            }
        }
        /* Show cards, hide table on mobile */
        @media (max-width: 768px) {
            .cart-items-container {
                display: none !important;
            }
            .cart-items-mobile {
                display: block;
            }
            .cart-items-mobile {
                margin: 0 0.5rem 1rem;
            }
            .cart-item-card {
                display: flex;
                gap: 1rem;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                margin-bottom: 1.2rem;
                padding: 1rem;
                align-items: flex-start;
            }
            .card-img-section {
                flex: 0 0 80px;
                width: 80px;
                height: 80px;
                border-radius: 8px;
                overflow: hidden;
                background: #f7f7f7;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .card-img-section img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .card-details-section {
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
                gap: 0.3rem;
            }
            .card-title-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .card-item-name {
                font-size: 1rem;
                font-weight: 600;
                color: var(--primary-color);
                margin-bottom: 0.1rem;
                word-break: break-word;
            }
            .remove-btn {
                font-size: 1.1rem;
                color: #dc3545;
                background: none;
                border: none;
                cursor: pointer;
                opacity: 0.7;
                padding: 0.2rem 0.4rem;
            }
            .remove-btn:hover {
                color: #c82333;
                opacity: 1;
            }
            .card-meta-row {
                font-size: 0.92rem;
                color: #888;
            }
            .card-item-size {
                background: #e6f2ff;
                color: #0066cc;
                border-radius: 4px;
                padding: 2px 8px;
                font-size: 0.9rem;
            }
            .card-price-row {
                font-size: 1.05rem;
                color: #444;
                font-weight: 600;
            }
            .card-qty-row {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-top: 0.2rem;
            }
            .card-item-subtotal {
                font-size: 0.98rem;
                color: var(--primary-color);
                font-weight: 500;
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

        document.addEventListener("DOMContentLoaded", function () {
            // Handle quantity changes
            document.querySelectorAll(".qty-btn").forEach((btn) => {
                btn.addEventListener("click", function () {
                    const input = this.parentElement.querySelector(".qty-input");
                    const currentValue = parseInt(input.value);
                    const maxStock = parseInt(input.dataset.maxStock);

                    if (this.classList.contains("plus")) {
                        if (currentValue < maxStock) {
                            input.value = currentValue + 1;
                        } else {
                            alert(`Maximum available stock is ${maxStock}.`);
                        }
                    } else if (this.classList.contains("minus") && currentValue > 1) {
                        input.value = currentValue - 1;
                    }

                    // Update cart in database
                    const itemId = input.dataset.itemId;
                    updateCartItem(itemId, input.value);
                });
            });

            // Handle direct quantity input
            document.querySelectorAll(".qty-input").forEach((input) => {
                input.addEventListener("change", function () {
                    const maxStock = parseInt(this.dataset.maxStock);
                    const newValue = parseInt(this.value);

                    if (newValue < 1) {
                        this.value = 1;
                    } else if (newValue > maxStock) {
                        this.value = maxStock;
                        alert(`Maximum available stock is ${maxStock}.`);
                    }
                    
                    // Update cart in database
                    const itemId = this.dataset.itemId;
                    updateCartItem(itemId, this.value);
                });
            });

            // Function to update cart item
            async function updateCartItem(itemId, quantity) {
                try {
                    const response = await fetch("../Includes/cart_operations.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `action=update&item_id=${itemId}&quantity=${quantity}`,
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Reload the page to update totals
                        location.reload();
                    } else {
                        console.error("Failed to update cart:", data.message);
                        alert("Failed to update quantity");
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("Error updating quantity");
                }
            }
        });
    </script>
</body>
</html> 