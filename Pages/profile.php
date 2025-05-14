<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../Includes/connection.php';

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM account WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle password change
$passwordMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verify current password
    if (password_verify($currentPassword, $user['password'])) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE account SET password = ? WHERE id = ?");
            
            if ($updateStmt->execute([$hashedPassword, $_SESSION['user_id']])) {
                $passwordMessage = '<div class="alert success">Password successfully updated!</div>';
            } else {
                $passwordMessage = '<div class="alert error">Error updating password.</div>';
            }
        } else {
            $passwordMessage = '<div class="alert error">New passwords do not match!</div>';
        }
    } else {
        $passwordMessage = '<div class="alert error">Current password is incorrect!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></title>
    <link rel="stylesheet" href="../CSS/global.css">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../CSS/header.css">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../Includes/Header.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="../Images/default-avatar.png" alt="Profile Picture">
                <button class="change-photo-btn">Change Photo</button>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p class="user-id">Student ID: <?php echo htmlspecialchars($user['id_number']); ?></p>
            </div>
        </div>

        <div class="profile-content">
            <div class="info-section">
                <h2>Personal Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Role:</label>
                        <span><?php echo htmlspecialchars($user['role_category']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Program/Position:</label>
                        <span><?php echo htmlspecialchars($user['program_or_position']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                </div>
            </div>

            <div class="cart-section">
                <h2>Your Cart</h2>
                <div class="cart-items">
                    <?php
                    // Fetch cart items
                    $stmt = $conn->prepare("
                        SELECT c.*, i.item_name, i.price, i.image_path 
                        FROM cart c 
                        JOIN inventory i ON c.item_code = i.item_code 
                        WHERE c.user_id = ?
                    ");
                    $stmt->execute([$_SESSION['user_id']]);
                    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $subtotal = 0;
                    
                    if (!empty($cart_items)):
                        foreach ($cart_items as $item):
                            $item_total = $item['price'] * $item['quantity'];
                            $subtotal += $item_total;
                    ?>
                    <div class="cart-item" data-item-id="<?php echo $item['id']; ?>">
                        <div class="cart-item-image">
                            <img src="../uploads/itemlist/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                        </div>
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                            <p class="sku">SKU: <?php echo htmlspecialchars($item['item_code']); ?></p>
                            <p class="price">Price: ₱<?php echo number_format($item['price'], 2); ?></p>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">-</button>
                                <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="99">
                                <button class="quantity-btn plus">+</button>
                            </div>
                            <button class="remove-item">×</button>
                        </div>
                    </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <p class="empty-cart-message">Your cart is empty</p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($cart_items)): ?>
                <div class="cart-summary">
                    <div class="subtotal">
                        <span>Subtotal</span>
                        <span class="amount">₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <button class="checkout-btn">Check out</button>
                </div>
                <?php endif; ?>
            </div>

            <div class="password-section">
                <h2>Change Password</h2>
                <?php echo $passwordMessage; ?>
                <form method="POST" class="password-form">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="change-password-btn">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .profile-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: 20px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 2px solid #e9ecef;
        }

        .profile-avatar {
            text-align: center;
            margin-right: 30px;
        }

        .profile-avatar img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .change-photo-btn {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: var(--primary-font-family);
            font-size: 14px;
            opacity: 0.9;
        }

        .change-photo-btn:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .profile-info h1 {
            margin: 0 0 10px 0;
            color: var(--primary-color);
            font-size: 2.2em;
            font-family: var(--primary-font-family);
        }

        .user-id {
            color: #6c757d;
            font-size: 1.1em;
            font-family: var(--primary-font-family);
        }

        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .info-section, .password-section {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }

        .info-section h2, .password-section h2 {
            color: var(--primary-color);
            font-size: 1.5em;
            margin-bottom: 20px;
            font-family: var(--primary-font-family);
            font-weight: 300;
        }

        .info-grid {
            display: grid;
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .info-item label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item span {
            color: #212529;
            font-size: 1.1em;
        }

        .password-form {
            display: grid;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 8px;
            color: #495057;
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1em;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .change-password-btn {
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1em;
            font-family: var(--primary-font-family);
            margin-top: 10px;
            opacity: 0.9;
        }

        .change-password-btn:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-family: var(--primary-font-family);
            font-size: 0.95em;
        }

        .alert.success {
            background-color: #e8f5e9;
            color: #1b5e20;
            border: 1px solid #c8e6c9;
        }

        .alert.error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fee2e2;
        }

        @media (max-width: 768px) {
            .profile-container {
                margin: 80px auto 30px;
                padding: 15px;
            }

            .profile-content {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }

            .profile-avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }

            .profile-info h1 {
                font-size: 1.8em;
            }

            .info-section, .password-section {
                padding: 20px;
            }

            .form-group input {
                padding: 10px;
            }
        }

        /* Add these new styles for the cart section */
        .cart-section {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            width: 100%;
            margin-bottom: 30px;
        }

        .cart-section h2 {
            color: var(--primary-color);
            font-size: 1.5em;
            margin-bottom: 20px;
            font-family: var(--primary-font-family);
            font-weight: 300;
        }

        .cart-items {
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            position: relative;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            margin-right: 20px;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-item-details {
            flex: 1;
            position: relative;
        }

        .cart-item-details h3 {
            margin: 0 0 5px 0;
            font-size: 1.1em;
            color: #333;
        }

        .cart-item-details .sku {
            color: #6c757d;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .cart-item-details .price {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #dee2e6;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            transition: all 0.2s;
        }

        .quantity-btn:hover {
            background: #f8f9fa;
        }

        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 1em;
        }

        .remove-item {
            position: absolute;
            top: 0;
            right: 0;
            background: none;
            border: none;
            font-size: 1.5em;
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
        }

        .cart-summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .subtotal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.1em;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            padding: 12px;
            background-color: #000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkout-btn:hover {
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .cart-section {
                width: 100%;
                margin-bottom: 20px;
            }

            .cart-item {
                flex-direction: column;
            }

            .cart-item-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cart popup functionality
            const cartIcon = document.querySelector('.cart-icon');
            const cartPopup = document.querySelector('.cart-popup');

            // Toggle cart popup when clicking the cart icon
            cartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                cartPopup.classList.toggle('show');
                cartIcon.classList.toggle('active');
                
                // Hide notification popup when cart is shown
                notificationPopup.classList.remove('show');
                notificationIcon.classList.remove('active');
            });

            // Notification popup functionality
            const notificationIcon = document.querySelector('.notification-icon');
            const notificationPopup = document.querySelector('.notification-popup');

            // Toggle notification popup when clicking the notification icon
            notificationIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                notificationPopup.classList.toggle('show');
                notificationIcon.classList.toggle('active');
                
                // Hide cart popup when notifications are shown
                cartPopup.classList.remove('show');
                cartIcon.classList.remove('active');
            });

            // Close popups when clicking outside
            document.addEventListener('click', function(e) {
                if (!cartIcon.contains(e.target) && !cartPopup.contains(e.target)) {
                    cartPopup.classList.remove('show');
                    cartIcon.classList.remove('active');
                }
                if (!notificationIcon.contains(e.target) && !notificationPopup.contains(e.target)) {
                    notificationPopup.classList.remove('show');
                    notificationIcon.classList.remove('active');
                }
            });

            // Prevent clicks inside popups from closing them
            cartPopup.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            notificationPopup.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Quantity controls
            const quantityControls = document.querySelectorAll('.quantity-controls');
            
            quantityControls.forEach(control => {
                const minusBtn = control.querySelector('.minus');
                const plusBtn = control.querySelector('.plus');
                const input = control.querySelector('.quantity-input');
                const cartItem = control.closest('.cart-item');
                const itemId = cartItem.dataset.itemId;
                
                minusBtn.addEventListener('click', () => {
                    let value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                        updateCartItem(itemId, input.value);
                    }
                });
                
                plusBtn.addEventListener('click', () => {
                    let value = parseInt(input.value);
                    if (value < 99) {
                        input.value = value + 1;
                        updateCartItem(itemId, input.value);
                    }
                });
                
                input.addEventListener('change', () => {
                    let value = parseInt(input.value);
                    if (value < 1) input.value = 1;
                    if (value > 99) input.value = 99;
                    updateCartItem(itemId, input.value);
                });
            });
            
            // Remove item functionality
            const removeButtons = document.querySelectorAll('.remove-item');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const cartItem = this.closest('.cart-item');
                    const itemId = cartItem.dataset.itemId;
                    removeCartItem(itemId);
                });
            });
            
            // Function to update cart item quantity
            async function updateCartItem(itemId, quantity) {
                try {
                    const response = await fetch('../Includes/cart_operations.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=update&item_id=${itemId}&quantity=${quantity}`
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        updateSubtotal();
                        // Update cart count in header if needed
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
            
            // Function to remove cart item
            async function removeCartItem(itemId) {
                if (confirm('Are you sure you want to remove this item?')) {
                    try {
                        const response = await fetch('../Includes/cart_operations.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=remove&item_id=${itemId}`
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                            cartItem.remove();
                            updateSubtotal();
                            
                            // Update cart count in header
                            const cartCount = document.querySelector('.cart-count');
                            if (cartCount) {
                                cartCount.textContent = data.cart_count;
                            }
                            
                            // Check if cart is empty
                            const cartItems = document.querySelectorAll('.cart-item');
                            if (cartItems.length === 0) {
                                document.querySelector('.cart-items').innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                                document.querySelector('.cart-summary')?.remove();
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            }
            
            // Update subtotal function
            function updateSubtotal() {
                const cartItems = document.querySelectorAll('.cart-item');
                let subtotal = 0;
                
                cartItems.forEach(item => {
                    const price = parseFloat(item.querySelector('.price').textContent.replace('Price: ₱', ''));
                    const quantity = parseInt(item.querySelector('.quantity-input').value);
                    subtotal += price * quantity;
                });
                
                const subtotalElement = document.querySelector('.amount');
                if (subtotalElement) {
                    subtotalElement.textContent = '₱' + subtotal.toFixed(2);
                }
            }
        });
    </script>

    <style>
        /* Add these styles to your existing CSS */
        
        .notification-icon, .cart-icon {
            position: relative;
            cursor: pointer;
        }

        .notification-popup, .cart-popup {
            position: absolute;
            top: 100%;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 1rem;
            display: none;
            z-index: 1001;
            margin-top: 10px;
            border: 1px solid #e9ecef;
            animation: slideIn 0.3s ease;
        }

        .notification-popup.show, .cart-popup.show {
            display: block;
        }

        .notification-header, .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .notification-header h3, .cart-header h3 {
            font-size: 1.1em;
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }

        .notification-count, .cart-count {
            color: #6c757d;
            font-size: 0.9em;
        }

        .notification-items, .cart-items {
            max-height: 300px;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .empty-notification-message, .empty-cart-message {
            text-align: center;
            color: #6c757d;
            padding: 1rem 0;
            font-size: 0.9em;
        }

        .notification-footer, .cart-footer {
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        .view-all-btn, .checkout-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            transition: all 0.3s ease;
            opacity: 0.9;
        }

        .view-all-btn:hover, .checkout-btn:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .notification-item, .cart-item {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.3s ease;
        }

        .notification-item:last-child, .cart-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover, .cart-item:hover {
            background-color: #f8f9fa;
        }

        /* Animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Responsive */
        @media screen and (max-width: 768px) {
            .notification-popup, .cart-popup {
                position: fixed;
                top: auto;
                bottom: 70px;
                right: 10px;
                width: calc(100% - 20px);
                max-width: 320px;
                margin: 0;
            }
        }

        /* Active state for icons */
        .notification-icon.active, .cart-icon.active {
            background-color: var(--primary-color);
            border-radius: 50%;
        }

        .notification-icon.active a, .cart-icon.active a {
            color: white;
        }
    </style>
</body>
</html>
