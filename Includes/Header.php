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

    <?php if (isset($_SESSION['user_id'])): ?>
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
    <?php else: ?>
        <div class="login">
            <div class="icon">
                <a href="login.php">Log in</a>
            </div>
        </div>
    <?php endif; ?>
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
        window.location.href = 'logout.php';
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

<style>
    .navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: white;
    padding: 0.5rem 1rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

.logo {
    flex: 0 0 auto;
}

.logo img {
    height: 50px;
}

.nav-links {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
    justify-content: center;
    flex: 1;
}

.nav-links li {
    margin: 0 1rem;
    position: relative;
    transition: .3s ease-out;
}

.nav-links a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 100;
    padding: 0.5rem 1rem;
    font-family: var(--primary-font-family);
    font-size: 20px;
    transition: .3s ease-out;
}

.nav-links > li:hover {
    transform: scale(1.1);
}

.nav-links li:hover a {
    color: rgb(0, 0, 0);
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    list-style: none;
    padding: 0.5rem 0;
    border-radius: 5px;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.dropdown-menu li {
    margin: 0;
    transition: .3s ease-out;
    background-color: white;
}

.dropdown-menu a {
    color: var(--primary-color);
    padding: 0.5rem 1rem;
    display: block;
}

.dropdown-menu a:hover {
    transform: scale(1.1);
}

.dropdown:hover .dropdown-menu {
    display: block;
}

/* Icons section styling */
.icons {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex: 0 0 auto;
}

.icon {
    position: relative;
    padding: 6px;
    border-radius: 50%;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
}

.icon a {
    color: var(--primary-color);
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    text-decoration: none;
    transition: all 0.3s ease;
}

/* Hover effects for icons */
.icon:hover {
    background: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.icon:hover a {
    color: white;
}

/* Cart count badge */
.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff4444;
    color: white;
    font-size: 0.7rem;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Active state for icons */
.icon.active {
    background: var(--primary-color);
}

.icon.active a {
    color: white;
}

/* Mobile responsive design */
@media screen and (max-width: 1024px) {
    .navbar {
        padding: 0.5rem;
    }

    .nav-links {
        display: none;
    }

    .hamburger {
        display: block !important;
        z-index: 1001;
    }

    .icons {
        order: 2;
    }

    /* When menu is active */
    .nav-links.active {
        display: flex;
        position: absolute;
        top: 70px;
        left: 0;
        width: 100%;
        flex-direction: column;
        background-color: white;
        padding: 1rem 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .nav-links li {
        margin: 1rem 0;
        text-align: center;
    }

    /* Hamburger animation */
    .hamburger.active .bar:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active .bar:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }

    .hamburger.active .bar:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    .dropdown-menu {
        position: static;
        width: 100%;
        background-color: #f9f9f9;
        box-shadow: none;
        display: none;
    }

    .dropdown.active .dropdown-menu {
        display: block;
    }

    /* Adjust bottom padding for content to account for fixed bottom icons */
    body {
        padding-bottom: 70px;
    }
}

/* Animation for icon interactions */
@keyframes iconPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.icon:active {
    animation: iconPulse 0.3s ease;
}

.hamburger {
    display: none;
    cursor: pointer;
}

.bar {
    width: 25px;
    height: 3px;
    margin: 5px;
    background-color: #333;
    display: block;
    transition: 0.3s ease;
}

/* Animation for menu items */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.nav-links.active li {
    animation: slideIn 0.3s ease forwards;
}

/* Stagger animation delay for menu items */
.nav-links.active li:nth-child(1) { animation-delay: 0.1s; }
.nav-links.active li:nth-child(2) { animation-delay: 0.2s; }
.nav-links.active li:nth-child(3) { animation-delay: 0.3s; }
.nav-links.active li:nth-child(4) { animation-delay: 0.4s; }

/* Cart Icon and Popup */
.cart-icon {
    position: relative;
    cursor: pointer;
}

.cart-popup {
    position: absolute;
    top: 100%;
    right: 0;
    width: 320px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 1rem;
    display: none; /* Hidden by default */
    z-index: 1001;
    margin-top: 10px;
}

.cart-popup.active {
    display: block;
    animation: slideIn 0.3s ease;
}

.cart-icon:hover .cart-popup {
    display: none;
}

.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.cart-header h3 {
    font-size: 1.2rem;
    margin: 0;
    color: var(--primary-color);
}

.empty-cart-message {
    text-align: center;
    color: #666;
    padding: 1rem 0;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.cart-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    margin-right: 1rem;
}

.cart-item-details {
    flex: 1;
}

.cart-item-name {
    font-weight: 500;
    color: var(--primary-color);
}

.cart-item-price {
    color: #666;
    font-size: 0.9rem;
}

.cart-footer {
    padding-top: 1rem;
    border-top: 1px solid #eee;
    margin-bottom: 0; /* Remove extra bottom margin */
}

.cart-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-weight: 500;
    color: var(--primary-color);
}

.checkout-btn {
    display: block;
    width: calc(100% - 2rem); /* Adjust width to account for padding */
    padding: 0.8rem;
    background-color: var(--primary-color);
    color: white;
    text-align: center;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.checkout-btn:hover {
    background-color: #000;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .cart-popup {
        position: fixed;
        top: auto;
        bottom: 80px; /* Adjust based on your bottom icons bar height */
        right: 20px;
        width: calc(100% - 40px);
        max-width: 320px;
    }
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

.cart-popup {
    animation: slideIn 0.3s ease;
}

/* Notification Icon and Popup */
.notification-icon {
    position: relative;
    cursor: pointer;
}

.notification-popup {
    position: absolute;
    top: 100%;
    right: 0;
    width: 320px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 1rem;
    display: none; /* Hidden by default */
    z-index: 1001;
    margin-top: 10px;
}

.notification-popup.active {
    display: block;
    animation: slideIn 0.3s ease;
}

.notification-icon:hover .notification-popup {
    display: none;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.notification-header h3 {
    font-size: 1.2rem;
    margin: 0;
    color: var(--primary-color);
}

.notification-count {
    color: #666;
    font-size: 0.9rem;
}

.notification-items {
    max-height: 300px;
    overflow-y: auto;
    padding: 1rem 0;
}

.empty-notification-message {
    text-align: center;
    color: #666;
    padding: 1rem 0;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 0.8rem 0;
    border-bottom: 1px solid #eee;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 500;
    color: var(--primary-color);
    margin-bottom: 0.3rem;
}

.notification-message {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
}

.notification-time {
    color: #999;
    font-size: 0.8rem;
    margin-top: 0.3rem;
}

.notification-footer {
    padding-top: 1rem;
    border-top: 1px solid #eee;
    text-align: center;
}

.view-all-btn {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.3s ease;
}

.view-all-btn:hover {
    color: #000;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .notification-popup {
        position: fixed;
        top: auto;
        bottom: 80px;
        right: 20px;
        width: calc(100% - 40px);
        max-width: 320px;
    }
}

/* Animation for both cart and notification popups */
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

.notification-popup {
    animation: slideIn 0.3s ease;
}

.login .icon {
    display: inline-block;
    border: 3px solid var(--primary-color);
    border-radius: 8px;
    background: var(--secondary-color);
    transition: background 0.3s, border-color 0.3s;
    white-space: nowrap;
    padding: 0;
    margin-right: 40px;
}

.login .icon a {
    display: block;
    padding: 5px 15px 10px 15px;
    margin-right: 25px;
    color: var(--primary-color);
    font-size: 25px;
    text-decoration: none;
    text-align: center;
    font-family: var(--primary-font-family);
}

.login .icon:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.login .icon:hover a {
    color: yellow;
}


@media screen and (max-width: 768px) {
    .icons {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: white;
        padding: 1rem;
        justify-content: space-around;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        margin: 0;
        z-index: 1000;
    }

    .icon {
        background: #f5f5f5;
        padding: 10px;
    }

    .icon a {
        width: 40px;
        height: 40px;
        font-size: 1.4rem;
    }

    .icon:hover {
        transform: translateY(-2px);
    }
}

/* Animation for icon interactions */
@keyframes iconPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.icon:active {
    animation: iconPulse 0.3s ease;
}

.hamburger {
    display: none;
    cursor: pointer;
}

.bar {
    display: block;
    width: 25px;
    height: 3px;
    margin: 5px auto;
    -webkit-transition: all 0.3s ease;
    transition: all 0.3s ease;
    background-color: var(--primary-color);
}

@media screen and (max-width: 768px) {
    .hamburger {
        display: block;
    }

    .hamburger.active .bar:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active .bar:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }

    .hamburger.active .bar:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    .nav-links {
        position: fixed;
        left: -100%;
        top: 70px;
        gap: 0;
        flex-direction: column;
        background-color: white;
        width: 100%;
        text-align: center;
        transition: 0.3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .nav-links.active {
        left: 0;
    }

    .nav-links li {
        margin: 16px 0;
    }

    .dropdown-menu {
        position: static;
        width: 100%;
        background-color: #f9f9f9;
        display: none;
    }

    .dropdown.active .dropdown-menu {
        display: block;
    }
}


</style>