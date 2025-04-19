<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connection.php';

// Only update cart count if user is logged in
if (isset($_SESSION['user_id'])) {
    // Get total items in cart
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Update session cart count
    $_SESSION['cart_count'] = $total;
}
?> 