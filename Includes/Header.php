<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar" role="navigation" aria-label="Main navigation">
    <!-- Left side - Logo -->
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

    <!-- Middle - Navigation Links -->
    <ul class="nav-links">
        <li><a href="ProHome.php">Homepage</a></li>
        <li><a href="ProItemList.php">Item List</a></li>
        <li><a href="ProPreOrder.php">Pre Order</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="faq.php">FAQ</a></li>
    </ul>

    <!-- Right side - Icons -->
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

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="icon">
                <a href="#" class="fas fa-user"></a>
            </div>
            <div class="icon">
                <a href="login.php" class="fas fa-sign-out-alt" title="Sign Out" onclick="signOut(event)"></a>
            </div>
        <?php else: ?>
            <div class="icon">
                <a href="login.php" class="fas fa-sign-in-alt" title="Sign In"></a>
            </div>
        <?php endif; ?>
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

    // Add sign out function
    function signOut(event) {
        event.preventDefault();
        fetch('logout.php')
            .then(response => {
                window.location.href = 'login.php';
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Enhanced mobile menu functionality
    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');

        hamburger.addEventListener('click', function () {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    });

</script>