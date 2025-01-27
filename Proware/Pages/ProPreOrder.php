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
</head>

<body>
    <?php
    include("../Includes/Header.php");
    ?>
    <div class="preorder-container">
        <div class="process-steps">
            <div class="step active">1. Pre Order Cart</div>
            <div class="step">2. Checkout Details</div>
            <div class="step">3. Pre Order Details</div>
        </div>

        <div class="cart-overview">
            <h2>Pre Order Cart</h2>
            <div class="cart-items">
                <!-- Sample item, you'll need to populate this with PHP from your database -->
                <div class="cart-item">
                    <img src="path_to_image.jpg" alt="Product Name">
                    <div class="item-details">
                        <h3>Product Name</h3>
                        <p class="price">₱500.00</p>
                        <div class="quantity">
                            <button type="button" class="qty-btn">-</button>
                            <input type="number" value="1" min="1">
                            <button type="button" class="qty-btn">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cart-summary">
                <div class="total">
                    <span>Total Amount:</span>
                    <span class="total-amount">₱500.00</span>
                </div>
                <form action="ProCheckout.php" method="POST" id="checkoutForm">
                    <!-- Add hidden inputs to pass cart data -->
                    <input type="hidden" name="cart_items"
                        value="<?php echo isset($cartItems) ? htmlspecialchars(json_encode($cartItems)) : ''; ?>">
                    <input type="hidden" name="total_amount" value="500.00">
                    <button type="submit" class="proceed-btn">Proceed to Checkout</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add this JavaScript to ensure the form submits properly
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            // You can add validation here if needed
            this.submit(); // Submit the form
        });
    </script>
</body>

</html>

<style>
    .preorder-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 80px 0 1rem 0;
    }

    .process-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
    }

    .step {
        font-family: var(--primary-font-family);
        color: var(--text-color);
        padding: 0.5rem 1rem;
    }

    .step.active {
        color: var(--primary-color);
    }

    .step.completed {
        color: green;
    }

    .checkout-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .checkout-form,
    .order-summary {
        background: white;
        padding: 2rem;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-family: var(--secondary-font-family);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: var(--secondary-font-family);
    }

    .place-order-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        cursor: pointer;
        border-radius: 4px;
        width: 100%;
        margin-top: 1rem;
        font-family: var(--secondary-font-family);
    }

    .place-order-btn:hover {
        opacity: 0.9;
    }

    .order-summary {
        align-self: start;
    }

    .summary-items {
        margin: 1.5rem 0;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
    }

    .summary-total {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 2px solid #eee;
        display: flex;
        justify-content: space-between;
        font-weight: bold;
    }

    .total-amount {
        color: var(--primary-color);
    }
</style>