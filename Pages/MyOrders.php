<?php
session_start();
require_once '../Includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get filter parameter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Fetch user's orders with filter
$query = "SELECT * FROM pre_orders WHERE user_id = ?";
if ($status_filter !== 'all') {
    $query .= " AND status = ?";
}
$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if ($status_filter !== 'all') {
    $stmt->execute([$_SESSION['user_id'], $status_filter]);
} else {
    $stmt->execute([$_SESSION['user_id']]);
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../CSS/MyOrders.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="orders-page">
        <div class="orders-header">
            <div class="header-content">
                <h1>My Orders</h1>
                <div class="filter-section">
                    <span>Filter by Status:</span>
                    <select id="statusFilter" onchange="filterOrders(this.value)">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Orders</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="orders-content">
            <?php if (!empty($orders)): ?>
                <div class="orders-grid">
                    <?php foreach ($orders as $order): 
                        $items = json_decode($order['items'], true);
                        $total_amount = $order['total_amount'];
                    ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                    <div class="order-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?>
                                        <?php if ($order['status'] === 'completed' && isset($order['payment_date'])): ?>
                                            <br>
                                            <i class="fas fa-money-bill"></i>
                                            <span class="payment-date">Paid: <?php echo date('F d, Y h:i A', strtotime($order['payment_date'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="status-badge <?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>

                            <div class="order-details">
                                <?php foreach ($items as $item): 
                                    $clean_name = rtrim($item['item_name'], " SMLX234567");
                                ?>
                                    <div class="order-item">
                                        <div class="item-image">
                                            <?php
                                            $image_path = $item['image_path'] ?? 'default.jpg';
                                            // Ensure we're using just the filename
                                            $image_filename = basename($image_path);
                                            ?>
                                            <img src="../uploads/itemlist/<?php echo htmlspecialchars($image_filename); ?>" 
                                                 alt="<?php echo htmlspecialchars($clean_name); ?>">
                                        </div>
                                        <div class="item-details">
                                            <span class="item-name"><?php echo htmlspecialchars($clean_name); ?></span>
                                        </div>
                                        <div class="item-size"><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></div>
                                        <div class="item-quantity"><?php echo $item['quantity']; ?></div>
                                        <div class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="order-footer">
                                <div class="contact-info">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($order['phone']); ?>
                                </div>
                                <div class="total-amount">
                                    <strong>Total Amount:</strong>
                                    <span>₱<?php echo number_format($total_amount, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-box-open"></i>
                    <p>You haven't placed any orders yet</p>
                    <a href="ProItemList.php" class="shop-now-btn">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filterOrders(status) {
            window.location.href = `MyOrders.php${status !== 'all' ? '?status=' + status : ''}`;
        }
    </script>
</body>
</html> 