<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="logo">
        <a href="ProHome.php" aria-label="Home">
            <img src="../Images/STI-LOGO.png" alt="STI Logo">
        </a>
    </div>

    <!-- Hamburger Menu for Mobile -->
    <div class="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>

    <ul class="nav-links">
        <li><a href="ProHome.php">Homepage</a></li>
        <li><a href="ProItemList.php">Item List</a></li>
        <li><a href="ProPreOrder.php">Pre Order</a></li>
        <li class="dropdown">
            <a href="#" aria-expanded="false" aria-haspopup="true">More Options &#9662;</a>
            <ul class="dropdown-menu" aria-label="submenu">
                <li><a href="contact.html">Contact</a></li>
                <li><a href="about.html">About Us</a></li>
            </ul>
        </li>
    </ul>

    <div class="icons">
        <div class="icon cart-icon">
            <a href="#" class="fas fa-shopping-cart">
                <?php if (isset($_SESSION['cart_count'])): ?>
                    <span class="cart-count"><?php echo $_SESSION['cart_count']; ?></span>
                <?php endif; ?>
            </a>
            <!-- Cart Popup -->
            <div class="cart-popup">
                <div class="cart-header">
                    <h3>Shopping Cart</h3>
                    <span class="cart-count">0 items</span>
                </div>
                <div class="cart-items">
                    <p class="empty-cart-message">Your cart is empty</p>
                </div>
                <div class="cart-footer">
                    <div class="cart-total">
                        <span>Total:</span>
                        <span>₱0.00</span>
                    </div>
                    <a href="#" class="checkout-btn">Checkout</a>
                </div>
            </div>
        </div>

        <div class="icon notification-icon">
            <a href="#" class="fas fa-bell"></a>
            <!-- Notification Popup -->
            <div class="notification-popup">
                <div class="notification-header">
                    <h3>Notifications</h3>
                    <span class="notification-count">0</span>
                </div>
                <div class="notification-items">
                    <p class="empty-notification-message">No notifications</p>
                </div>
                <div class="notification-footer">
                    <a href="notifications.php" class="view-all-btn">View All</a>
                </div>
            </div>
        </div>

        <div class="icon">
            <a href="#" class="fas fa-user"></a>
        </div>
    </div>
</nav>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    function checkLoginStatus(destination) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            window.location.href = `login.php?redirect=${encodeURIComponent(destination)}`;
        <?php else: ?>
            window.location.href = destination;
        <?php endif; ?>
    }

    // Add this JavaScript for mobile menu functionality
    document.querySelector('.hamburger').addEventListener('click', function () {
        this.classList.toggle('active');
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>