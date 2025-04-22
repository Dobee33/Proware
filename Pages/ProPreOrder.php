<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pre Order Page</title>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/ProPreOrder.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php
    include("../Includes/Header.php");
    require_once '../Includes/connection.php';

    // Fetch cart items for the current user
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $final_cart_items = [];
    foreach ($cart_items as $cart_item) {
        // Try to get inventory details for each cart item
        $stmt = $conn->prepare("SELECT item_name, price, image_path FROM inventory WHERE item_code = ?");
        $stmt->execute([$cart_item['item_code']]);
        $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($inventory_item) {
            $final_cart_items[] = array_merge($cart_item, $inventory_item);
        } else {
            // Try to find the item with a LIKE query to catch potential formatting differences
            $stmt = $conn->prepare("SELECT item_name, price, image_path FROM inventory WHERE item_code LIKE ?");
            $stmt->execute(['%' . $cart_item['item_code'] . '%']);
            $inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($inventory_item) {
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
    
    $total_amount = 0;
    ?>
    <div class="preorder-container">
        <div class="process-steps">
            <div class="step active">1. Pre Order Cart</div>
            <div class="step">2. Checkout Details</div>
            <div class="step">3. Pre Order Details</div>
        </div>

        <h1>Cart</h1>
        
        <div class="cart-layout">
            <div class="products-section">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Include in Checkout</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($final_cart_items)): ?>
                            <?php foreach ($final_cart_items as $item): 
                                $item_total = $item['price'] * $item['quantity'];
                                $total_amount += $item_total;
                                // Remove size suffix from item name
                                $clean_name = rtrim($item['item_name'], " SMLX234567");
                            ?>
                                <tr data-item-id="<?php echo $item['id']; ?>" data-item-code="<?php echo $item['item_code']; ?>">
                                    <td>
                                        <img src="../uploads/itemlist/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($clean_name); ?>" 
                                             class="product-image">
                                    </td>
                                    <td><?php echo htmlspecialchars($clean_name); ?></td>
                                    <td><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                    </td>
                                    <td>
                                        <div class="toggle-container">
                                            <button class="toggle-checkout-btn check" data-item-id="<?php echo $item['id']; ?>" data-included="true">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="toggle-checkout-btn x" data-item-id="<?php echo $item['id']; ?>" data-included="false">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-cart">Your cart is empty</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-section">
                <table class="total-table">
                    <tr>
                        <th>Total Amount:</th>
                        <td>₱<?php 
                            $included_total = 0;
                            foreach ($final_cart_items as $item) {
                                // Only add to total if item is included (default is true)
                                $included_total += $item['price'] * $item['quantity'];
                            }
                            echo number_format($included_total, 2); 
                        ?></td>
                    </tr>
                </table>

                <?php if (!empty($final_cart_items)): ?>
                <form action="ProCheckout.php" method="POST" id="checkoutForm">
                    <input type="hidden" name="cart_items" id="cartItemsInput" value=''>
                    <input type="hidden" name="total_amount" id="totalAmountInput" value="<?php echo $included_total; ?>">
                    <input type="hidden" name="included_items" id="includedItems" value="">
                    <button type="submit" class="proceed-btn">Proceed to Checkout</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../Javascript/ProPreOrder.js"></script>
</body>

</html>