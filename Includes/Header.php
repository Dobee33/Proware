<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart count
require_once dirname(__FILE__) . '/init_cart.php';

$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
?>

<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="logo-container">
        <div class="logo">
            <a href="home.php" aria-label="Home">
                <img src="../Images/STI-LOGO.png" alt="STI Logo" class="logo-image">
            </a>
        </div>

        <!-- Middle - Navigation Links -->
        <ul class="nav-links">
            <li><a href="home.php"
                    class="<?php echo ($current_page == 'home.php') ? 'active' : ''; ?>">Homepage</a></li>
            <li><a href="ProItemList.php"
                    class="<?php echo ($current_page == 'ProItemList.php') ? 'active' : ''; ?>">Item List</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="MyCart.php"
                        class="<?php echo ($current_page == 'MyCart.php') ? 'active' : ''; ?>">My Cart</a></li>
            <?php else: ?>
                <li><a href="javascript:void(0)" onclick="redirectToLogin('MyCart.php')"
                        class="<?php echo ($current_page == 'MyCart.php') ? 'active' : ''; ?>">My Cart</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="ProPreOrder.php"
                        class="<?php echo ($current_page == 'ProPreOrder.php') ? 'active' : ''; ?>">Pre Order</a></li>
            <?php else: ?>
                <li><a href="javascript:void(0)" onclick="redirectToLogin('ProPreOrder.php')"
                        class="<?php echo ($current_page == 'ProPreOrder.php') ? 'active' : ''; ?>">Pre Order</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="MyOrders.php"
                        class="<?php echo ($current_page == 'MyOrders.php') ? 'active' : ''; ?>">My Orders</a></li>
            <?php else: ?>
                <li><a href="javascript:void(0)" onclick="redirectToLogin('MyOrders.php')"
                        class="<?php echo ($current_page == 'MyOrders.php') ? 'active' : ''; ?>">My Orders</a></li>
            <?php endif; ?>
            <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a></li>
            <li><a href="faq.php" class="<?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>">FAQ</a></li>
        </ul>
    </div>

    <!-- Hamburger Menu for Mobile -->
    <div class="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="icons">
            <div class="icon cart-icon">
                <a href="MyCart.php" class="fas fa-shopping-cart">
                    <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="cart-count"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
                <!-- Cart Popup -->
                <div class="cart-popup">
                    <div class="cart-header">
                        <h3>Shopping Cart</h3>
                        <span class="cart-count"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0'; ?> items</span>
                    </div>
                    <div class="cart-items">
                        <p class="empty-cart-message">Your cart is empty</p>
                    </div>
                    <div class="cart-footer">
                        <div class="cart-buttons">
                            <a href="MyCart.php" class="checkout-btn">View Cart</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="icon notification-icon">
                <a href="#" class="fas fa-bell">
                    <span class="notification-count">0</span>
                </a>
                <!-- Notification Popup -->
                <div class="notification-popup">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                        
                    </div>
                    <div class="notification-items">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="icon">
                    <a href="profile.php" class="fas fa-user"></a>
                </div>
                <div class="icon">
                    <a href="login.php" class="fas fa-sign-out-alt" title="Sign Out" onclick="signOut(event)"></a>
                </div>
            <?php else: ?>
                <div class="login">
                    <div class="icon">
                        <a href="login.php" class="login-button">Login</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="login">
            <div class="icon">
                <a href="login.php" class="login-button">Log in</a>
            </div>
        </div>
    <?php endif; ?>
</nav>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Cart Styles -->
<link rel="stylesheet" href="../CSS/cart.css">

<script>
    function checkLoginStatus(destination) {
        <?php if (!isset($_SESSION['user_id'])): ?>
            window.location.href = `login.php?redirect=${encodeURIComponent(destination)}`;
        <?php else: ?>
            window.location.href = destination;
        <?php endif; ?>
    }

    // Add sign out function with confirmation
    function signOut(event) {
        event.preventDefault();
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = 'logout.php';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    });

    // Function to fade out the welcome message after 5 seconds
    window.onload = function () {
        const welcomeMessage = document.getElementById('welcomeMessage');
        if (welcomeMessage) {
            setTimeout(() => {
                welcomeMessage.style.transition = "opacity 1s ease-out";
                welcomeMessage.style.opacity = 0;
            }, 5000);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const cartIcon = document.querySelector('.cart-icon');
        const cartPopup = document.querySelector('.cart-popup');
        const notificationIcon = document.querySelector('.notification-icon');
        const notificationPopup = document.querySelector('.notification-popup');

        // Function to format time ago
        function formatTimeAgo(timestamp) {
            const currentTime = Math.floor(Date.now() / 1000);
            const timeDifference = currentTime - timestamp;
            
            if (timeDifference < 60) {
                return 'Just now';
            } else if (timeDifference < 3600) {
                const minutes = Math.floor(timeDifference / 60);
                return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            } else if (timeDifference < 86400) {
                const hours = Math.floor(timeDifference / 3600);
                return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            } else if (timeDifference < 604800) {
                const days = Math.floor(timeDifference / 86400);
                return `${days} day${days > 1 ? 's' : ''} ago`;
            } else {
                return new Date(timestamp * 1000).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: true
                });
            }
        }

        // Function to update notification times
        function updateNotificationTimes() {
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach(item => {
                const timestamp = parseInt(item.dataset.timestamp);
                const timeElement = item.querySelector('.notification-time');
                if (timestamp && timeElement) {
                    timeElement.textContent = formatTimeAgo(timestamp);
                }
            });
        }

        // Function to update cart popup content
        async function updateCartPopup() {
            try {
                const response = await fetch('../Includes/cart_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=get_cart'
                });

                const data = await response.json();
                console.log('Cart data:', data); // Debug log
                
                if (data.success) {
                    const cartItems = document.querySelector('.cart-items');
                    const cartCountSpan = document.querySelector('.cart-header .cart-count');
                    
                    if (cartItems) {
                        if (data.cart_items && data.cart_items.length > 0) {
                            cartItems.innerHTML = data.cart_items.map(item => `
                                <div class="cart-item">
                                    <img src="../uploads/itemlist/${item.image_path}" alt="${item.item_name}">
                                    <div class="cart-item-details">
                                        <div class="cart-item-name">${item.item_name}</div>
                                        <div class="cart-item-price">₱${item.price} × ${item.quantity}</div>
                                    </div>
                                </div>
                            `).join('');
                            
                            // Update cart count in header
                            if (cartCountSpan) {
                                cartCountSpan.textContent = `${data.cart_items.length} items`;
                            }
                        } else {
                            cartItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                            if (cartCountSpan) {
                                cartCountSpan.textContent = '0 items';
                            }
                        }
                    }
                } else {
                    console.error('Cart data error:', data.message); // Debug log
                }
            } catch (error) {
                console.error('Cart fetch error:', error); // Debug log
                const cartItems = document.querySelector('.cart-items');
                if (cartItems) {
                    cartItems.innerHTML = '<p class="empty-cart-message">Error loading cart items</p>';
                }
            }
        }

        // Function to update notification popup content
        async function updateNotificationPopup() {
            try {
                const response = await fetch('../Includes/notification_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=get_notifications'
                });

                const data = await response.json();
                
                if (data.success) {
                    const notificationItems = document.querySelector('.notification-items');
                    const notificationCountSpan = document.querySelector('.notification-header .notification-count');
                    const iconNotificationCount = document.querySelector('.notification-icon .notification-count');
                    const clearButton = document.querySelector('.clear-notifications');
                    
                    if (notificationItems) {
                        if (data.notifications && data.notifications.length > 0) {
                            notificationItems.innerHTML = data.notifications.map(notification => {
                                const timestamp = Math.floor(new Date(notification.created_at).getTime() / 1000);
                                return `
                                    <div class="notification-item ${notification.is_read ? '' : 'unread'}" 
                                         onclick="markNotificationAsRead(${notification.id})"
                                         data-timestamp="${timestamp}">
                                        <p class="notification-message">${notification.message}</p>
                                        <span class="notification-status ${notification.type}">
                                            ${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}
                                        </span>
                                        <p class="notification-time">${formatTimeAgo(timestamp)}</p>
                                    </div>
                                `;
                            }).join('');
                            
                            if (notificationCountSpan) {
                                notificationCountSpan.textContent = data.count;
                            }
                            if (iconNotificationCount) {
                                iconNotificationCount.textContent = data.count;
                                iconNotificationCount.style.display = data.count > 0 ? 'block' : 'none';
                            }
                            if (clearButton) {
                                clearButton.style.display = 'block';
                            }
                        } else {
                            notificationItems.innerHTML = '<p class="empty-notification-message">No notifications</p>';
                            if (notificationCountSpan) {
                                notificationCountSpan.textContent = '0';
                            }
                            if (iconNotificationCount) {
                                iconNotificationCount.style.display = 'none';
                            }
                            if (clearButton) {
                                clearButton.style.display = 'none';
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                const notificationItems = document.querySelector('.notification-items');
                if (notificationItems) {
                    notificationItems.innerHTML = '<p class="empty-notification-message">Error loading notifications</p>';
                }
            }
        }

        // Function to mark notification as read
        window.markNotificationAsRead = async function(notificationId) {
            try {
                const response = await fetch('../Includes/notification_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=mark_as_read&notification_id=${notificationId}`
                });

                const data = await response.json();
                if (data.success) {
                    updateNotificationPopup();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        };

        // Function to clear all notifications
        async function clearAllNotifications() {
            if (!confirm('Are you sure you want to clear all notifications?')) {
                return;
            }

            try {
                const response = await fetch('../Includes/notification_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_all'
                });

                const data = await response.json();
                if (data.success) {
                    updateNotificationPopup();
                } else {
                    alert('Failed to clear notifications: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to clear notifications');
            }
        }

        // Cart icon click event
        if (cartIcon) {
            cartIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                cartPopup.classList.toggle('show');
                
                // Hide notification popup when cart is shown
                if (notificationPopup) {
                    notificationPopup.classList.remove('show');
                }
            });
        }

        // Notification icon click event
        if (notificationIcon) {
            notificationIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                notificationPopup.classList.toggle('show');
                
                // Update notifications when popup is shown
                if (notificationPopup.classList.contains('show')) {
                    updateNotificationPopup();
                }
                
                // Hide cart popup when notifications are shown
                if (cartPopup) {
                    cartPopup.classList.remove('show');
                }
            });
        }

        // Add clear button to notification header
        const notificationHeader = document.querySelector('.notification-header');
        if (notificationHeader) {
            const clearButton = document.createElement('button');
            clearButton.className = 'clear-notifications';
            clearButton.textContent = 'Clear All';
            clearButton.onclick = clearAllNotifications;
            notificationHeader.appendChild(clearButton);
        }

        // Close popups when clicking outside
        document.addEventListener('click', function(e) {
            if (cartPopup && !cartIcon.contains(e.target) && !cartPopup.contains(e.target)) {
                cartPopup.classList.remove('show');
            }
            if (notificationPopup && !notificationIcon.contains(e.target) && !notificationPopup.contains(e.target)) {
                notificationPopup.classList.remove('show');
            }
        });

        // Prevent clicks inside popups from closing them
        if (cartPopup) {
            cartPopup.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        if (notificationPopup) {
            notificationPopup.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Initial updates
        if (cartIcon) updateCartPopup();
        if (notificationIcon) updateNotificationPopup();

        // Refresh notifications every 30 seconds
        setInterval(updateNotificationPopup, 30000);
        
        // Update notification times every minute
        setInterval(updateNotificationTimes, 60000);
    });

    function getNotificationIcon(type) {
        switch (type) {
            case 'order_approved':
                return '<i class="fas fa-check-circle" style="color: #28a745;"></i>';
            case 'order_rejected':
                return '<i class="fas fa-times-circle" style="color: #dc3545;"></i>';
            case 'order_processing':
                return '<i class="fas fa-clock" style="color: #ffc107;"></i>';
            case 'order_ready':
                return '<i class="fas fa-box" style="color: #17a2b8;"></i>';
            case 'order_completed':
                return '<i class="fas fa-check-double" style="color: #28a745;"></i>';
            default:
                return '<i class="fas fa-bell" style="color: #007bff;"></i>';
        }
    }

    function redirectToLogin(destination) {
        window.location.href = `login.php?redirect=${encodeURIComponent(destination)}`;
    }
</script>

<!-- Include cart functionality -->
<script src="../Javascript/cart.js"></script>

<style>
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 0.5rem 0.5rem 0.5rem 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }

    .logo-container {
        display: flex;
        align-items: center;
        padding-left: 30px;
        width: 100%;
        justify-content: space-between;
    }

    .logo {
        margin-right: 20px;
        /* Adjust spacing as needed */
    }

    .nav-links {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        justify-content: center;
        flex: 1;
    }

    .nav-links li {
        margin: 0 10px;
        /* Adjust spacing between links */
    }

    .nav-links a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 100;
        padding: 0.5rem 1rem;
        font-family: var(--primary-font-family);
        font-size: 16px; /* Reduced font size */
        transition: .3s ease-out;
        letter-spacing: 0.3px;
    }

    .nav-links>li:hover {
        transform: scale(1.1);
    }

    .icons {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex: 0 0 auto;
        padding-right: 30px;
    }

    .icon {
        position: relative;
        padding: 6px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .icon a {
        color: var(--primary-color);
        font-size: 1.2rem; /* Increased icon size */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px; /* Increased width */
        height: 36px; /* Increased height */
        text-decoration: none;
        transition: all 0.3s ease;
    }

    /* Hover effects for icons */
    .icon:hover {
        transform: translateY(-7px);
    }

    /* Add hover effect for glassy look */
    .nav-links a:hover {
        color: darkblue;
    }

    /* Cart count badge */
    .cart-count {
        position: absolute;
        top: -5px;
        right: -20px;
        background-color: #ff4444;
        color: white;
        font-size: 0.65rem;
        font-weight: bold;
        padding: 1px 4px;
        border-radius: 8px;
        min-width: 12px;
        height: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1002; /* Ensure the badge is always on top */
    }

    /* Active state for icons */
    .icon.active {
        background: var(--primary-color);
    }

    .icon.active a {
        color: black;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .icon:active {
        animation: iconPulse 0.3s ease;
    }

    .hamburger {
        display: none;
        cursor: pointer;
        margin-right: 30px;
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
    .nav-links.active li:nth-child(1) {
        animation-delay: 0.1s;
    }

    .nav-links.active li:nth-child(2) {
        animation-delay: 0.2s;
    }

    .nav-links.active li:nth-child(3) {
        animation-delay: 0.3s;
    }

    .nav-links.active li:nth-child(4) {
        animation-delay: 0.4s;
    }

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
        display: none;
        z-index: 1001;
        margin-top: 10px;
    }

    .cart-popup.show {
        display: block;
    }

    .checkout-btn {
        display: block;
        width: calc(100% - 2rem);
        padding: 0.8rem;
        background-color: var(--primary-color);
        color: white !important;
        text-align: center;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    .checkout-btn:hover {
        background-color: #00008B;
        color: white !important;
        text-decoration: none;
    }

    /* Add active state for cart icon */
    .cart-icon.active {
        background: var(--primary-color);
    }

    .cart-icon.active a {
        color: black;
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
    }

    .cart-buttons {
        display: flex;
        gap: 10px;
    }

    .view-cart-btn, .checkout-btn {
        flex: 1;
        padding: 0.8rem;
        text-align: center;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .view-cart-btn {
        background-color: #f8f9fa;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
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
        background-color: #00008B; /* Dark blue color */
        color: white;
    }

    .view-cart-btn:hover {
        background-color: #e9ecef;
    }

    /* Responsive adjustments */
    @media screen and (max-width: 768px) {
        .cart-popup {
            position: fixed;
            top: auto;
            bottom: 80px;
            /* Adjust based on your bottom icons bar height */
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
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        padding: 1.5rem;
        display: none;
        z-index: 1001;
        margin-top: 15px;
        border: 1px solid #e9ecef;
        max-width: calc(100vw - 40px);
        box-sizing: border-box;
    }

    .notification-popup.show {
        display: block;
        animation: slideIn 0.3s ease;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #f0f2f5;
        width: 100%;
    }

    .notification-header h3 {
        font-size: 1.2rem;
        margin: 0;
        color: var(--primary-color);
        font-weight: 600;
    }

    .clear-notifications {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 0.9rem;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .clear-notifications:hover {
        background-color: #fff5f5;
    }

    .clear-notifications:disabled {
        color: #6c757d;
        cursor: not-allowed;
    }

    .notification-items {
        max-height: 400px;
        overflow-y: auto;
        padding: 0.5rem 0;
        width: 100%;
    }

    .cart-items::-webkit-scrollbar, .notification-items::-webkit-scrollbar {
        width: 6px;
    }

    .cart-items::-webkit-scrollbar-track, .notification-items::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .cart-items::-webkit-scrollbar-thumb, .notification-items::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .cart-items::-webkit-scrollbar-thumb:hover, .notification-items::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #f0f2f5;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 0.8rem;
        width: 100%;
        box-sizing: border-box;
        position: relative;
        background-color: #ffffff;
    }

    .notification-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .notification-item.unread {
        background-color: #e3f2fd;
        border: 2px solid #1976d2;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(25, 118, 210, 0.1);
        margin-left: -10px;
        margin-right: 5px;
        padding-left: 15px;
    }

    .notification-item.unread::before {
        content: 'NEW';
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #ff4444;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-3px);
        }
    }

    .notification-item.unread .notification-message {
        color: #1a1a1a;
        font-weight: 700;
        font-size: 1rem;
    }

    .notification-item.unread .notification-time {
        color: #1976d2;
        font-weight: 600;
    }

    .notification-message {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-size: 0.95rem;
        line-height: 1.5;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .notification-time {
        color: #6c757d;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .notification-status {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .notification-item.unread .notification-status {
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-status.completed {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .notification-status.approved {
        background-color: #e3f2fd;
        color: #1565c0;
    }

    .notification-status.processing {
        background-color: #fff3e0;
        color: #ef6c00;
    }

    .notification-status.rejected {
        background-color: #ffebee;
        color: #c62828;
    }

    .empty-notification-message {
        text-align: center;
        color: #6c757d;
        padding: 2rem 0;
        font-size: 0.95rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .notification-count {
        position: absolute;
        top: -5px;
        right: 10px;
        background-color: #ff4444;
        color: white;
        font-size: 0.65rem;
        font-weight: 600;
        padding: 1px 4px;
        border-radius: 8px;
        min-width: 12px;
        height: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1002;
    }

    @media screen and (max-width: 768px) {
        .notification-popup {
            position: fixed;
            top: auto;
            bottom: 70px;
            right: 10px;
            width: calc(100vw - 20px);
            max-width: 320px;
            margin: 0;
        }
    }

    .logo-image {
        width: 50px;
        height: 50px;
    }

    .nav-links a.active {
        color: #092d45;
        text-decoration: none;
    }

    .view-cart-btn {
        display: block;
        width: 100%;
        padding: 0.8rem;
        background-color: var(--primary-color);
        color: white;
        text-align: center;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        margin-top: 1rem;
    }

    .view-cart-btn:hover {
        background-color: #005a94;
    }

    /* Notification item styles */
    .notification-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .notification-item:hover {
        background-color: #f5f5f5;
    }

    .notification-item.approved {
        border-left: 4px solid #28a745;
    }

    .notification-item.rejected {
        border-left: 4px solid #dc3545;
    }

    .notification-item.read {
        opacity: 0.7;
        background-color: #f8f9fa;
    }

    .notification-order {
        margin: 4px 0;
        color: #666;
        font-size: 0.85rem;
    }

    .notification-time {
        display: block;
        font-size: 0.8rem;
        color: #888;
        margin-top: 5px;
    }

    /* Animation for new notifications */
    @keyframes notificationPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .notification-icon .notification-count:not(:empty) {
        animation: notificationPulse 1s infinite;
    }

    /* Shared Popup Styles */
    .cart-popup, .notification-popup {
        position: absolute;
        top: 100%;
        right: 0;
        width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        padding: 1.5rem;
        display: none;
        z-index: 1001;
        margin-top: 15px;
        border: 1px solid #e9ecef;
        max-width: calc(100vw - 40px);
        box-sizing: border-box;
    }

    .cart-popup.show, .notification-popup.show {
        display: block;
        animation: slideIn 0.3s ease;
    }

    /* Header Styles */
    .cart-header, .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #f0f2f5;
        width: 100%;
    }

    .cart-header h3, .notification-header h3 {
        font-size: 1.2rem;
        margin: 0;
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Items Container */
    .cart-items, .notification-items {
        max-height: 400px;
        overflow-y: auto;
        padding: 0.5rem 0;
        width: 100%;
    }

    .cart-items::-webkit-scrollbar, .notification-items::-webkit-scrollbar {
        width: 6px;
    }

    .cart-items::-webkit-scrollbar-track, .notification-items::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .cart-items::-webkit-scrollbar-thumb, .notification-items::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .cart-items::-webkit-scrollbar-thumb:hover, .notification-items::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Notification Item Styles */
    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #f0f2f5;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        width: 100%;
        box-sizing: border-box;
    }

    .notification-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .notification-item.unread {
        background-color: #f0f7ff;
        border-left: 4px solid var(--primary-color);
    }

    .notification-message {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-size: 0.95rem;
        line-height: 1.5;
        font-weight: 500;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .notification-time {
        color: #6c757d;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .notification-time::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: #6c757d;
        border-radius: 50%;
    }

    .notification-status {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .notification-status.approved {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .notification-status.rejected {
        background-color: #ffebee;
        color: #c62828;
    }

    .notification-status.processing {
        background-color: #fff3e0;
        color: #ef6c00;
    }

    .notification-status.ready {
        background-color: #e3f2fd;
        color: #1565c0;
    }

    .notification-status.completed {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    /* Empty State Messages */
    .empty-cart-message, .empty-notification-message {
        text-align: center;
        color: #6c757d;
        padding: 2rem 0;
        font-size: 0.95rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 1rem 0;
    }

    /* Count Badges */
    .cart-count, .notification-count {
        position: absolute;
        top: -8px;
        right: -20px;
        background-color: #ff4444;
        color: white;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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

    /* Responsive Design */
    @media screen and (max-width: 768px) {
        .cart-popup, .notification-popup {
            position: fixed;
            top: auto;
            bottom: 70px;
            right: 10px;
            width: calc(100vw - 20px);
            max-width: 320px;
            margin: 0;
        }
    }

    /* Login Button Styles */
    .login-button {
        display: inline-block;
        background-color: #0078d4;
        color: white !important;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0, 120, 212, 0.3);
        transition: all 0.3s ease;
        white-space: nowrap;
        border: none;
        line-height: 1;
    }
    
    .login-button:hover {
        background-color: darkblue;
        color: yellow !important;
        box-shadow: 0 6px 20px rgba(0, 120, 212, 0.2);
        text-decoration: none;
    }
    


    .login {
        margin-right: 20px;
    }

    .login .icon {
        padding: 0;
    }
    
    @media screen and (max-width: 768px) {
        .login-button {
            padding: 8px 20px;
            font-size: 14px;
        }
    }
</style>