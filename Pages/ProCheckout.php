<?php
session_start(); // Start the session if you're using sessions
include("../Includes/Header.php");

// Verify if data was received from ProPreOrder.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store the cart data in session if needed
    if (isset($_POST['cart_items'])) {
        $_SESSION['cart_items'] = $_POST['cart_items'];
    }
    if (isset($_POST['total_amount'])) {
        $_SESSION['total_amount'] = $_POST['total_amount'];
    }
}
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
    <?php
    include("../Includes/Header.php");
    ?>

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
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="course">Course/Strand</label>
                        <select id="course" name="course" required>
                            <option value="">Select Course/Strand</option>
                            <option value="STEM">STEM</option>
                            <option value="ABM">ABM</option>
                            <option value="HUMSS">HUMSS</option>
                            <option value="GAS">GAS</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <button type="submit" class="place-order-btn">Place Order</button>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-items">
                    <!-- Sample item, populate with PHP -->
                    <div class="summary-item">
                        <span class="item-name">Product Name</span>
                        <span class="item-price">₱500.00</span>
                    </div>
                </div>
                <div class="summary-total">
                    <span>Total Amount:</span>
                    <span class="total-amount">₱500.00</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<style>
</style>