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
    <link rel="stylesheet" href="../CSS/sidebar_MyOrder.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include("../Includes/Header.php"); ?>

    <div class="page-wrapper">
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="order-navigation">
                    <a href="MyOrders.php" class="nav-tab active">
                        <i class="fas fa-box"></i> My Orders
                    </a>
                    <a href="MyCart.php" class="nav-tab">
                        <i class="fas fa-shopping-cart"></i> My Cart
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>My Orders</h1>
                <div class="filter-section">
                    <label for="statusFilter">Filter by Status:</label>
                    <select id="statusFilter" onchange="filterOrders(this.value)">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Orders</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Orders Section -->
            <div class="orders-section">
                <?php if (!empty($orders)): ?>
                    <div class="orders-grid">
                        <?php foreach ($orders as $order): 
                            $items = json_decode($order['items'], true);
                            $total_amount = $order['total_amount'];
                        ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <h3>Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                    <span class="status-badge <?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>

                                <div class="order-details">
                                    <div class="order-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?>
                                    </div>

                                    <div class="items-list">
                                        <h4>Ordered Items:</h4>
                                        <div class="items-header">
                                            <span class="item-name">Item</span>
                                            <span class="item-size">Size</span>
                                            <span class="item-quantity">Qty</span>
                                            <span class="item-price">Price</span>
                                        </div>
                                        <?php foreach ($items as $item): 
                                            // Remove size suffix from item name
                                            $clean_name = rtrim($item['item_name'], " SMLX234567");
                                        ?>
                                            <div class="item">
                                                <span class="item-name"><?php echo htmlspecialchars($clean_name); ?></span>
                                                <span class="item-size"><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></span>
                                                <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                                <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="order-footer">
                                        <div class="contact-info">
                                            <i class="fas fa-phone"></i>
                                            <?php echo htmlspecialchars($order['phone']); ?>
                                        </div>
                                        <div class="total-amount">
                                            <strong>Total:</strong> ₱<?php echo number_format($total_amount, 2); ?>
                                        </div>
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
    </div>

    <style>
        .page-wrapper {
            padding: 2rem;
            padding-top: 100px;
        }

        .main-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .page-header h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin: 0;
        }

        .filter-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .filter-section label {
            color: #555;
            font-weight: 500;
        }

        .filter-section select {
            padding: 0.5rem 1rem;
            border: 2px solid #eee;
            border-radius: 6px;
            font-size: 1rem;
            color: #333;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-section select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
        }

        .orders-grid {
            display: grid;
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
        }

        .order-header {
            background: #f8f9fa;
            padding: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .order-header h3 {
            color: #333;
            font-size: 1.2rem;
            margin: 0;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .order-details {
            padding: 1.5rem;
        }

        .order-date {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
        }

        .order-date i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .items-list {
            margin: 1rem 0;
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .items-list h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .items-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
            color: var(--primary-color);
        }

        .item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 500;
            color: #333;
        }

        .item-size {
            text-align: center;
            color: #555;
            font-weight: 500;
        }

        .item-quantity {
            text-align: center;
            color: #555;
            font-weight: 500;
        }

        .item-price {
            text-align: right;
            font-weight: 600;
            color: var(--primary-color);
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.2rem;
            border-top: 2px solid #eee;
        }

        .contact-info {
            color: #555;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .contact-info i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .total-amount {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .total-amount strong {
            color: #333;
            margin-right: 0.8rem;
        }

        .no-orders {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .no-orders i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .no-orders p {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .shop-now-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .shop-now-btn:hover {
            background: var(--primary-color-dark);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .order-header {
                flex-direction: column;
                gap: 0.8rem;
                align-items: flex-start;
            }

            .order-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .total-amount {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>

    <script>
        function filterOrders(status) {
            window.location.href = `MyOrders.php${status !== 'all' ? '?status=' + status : ''}`;
        }
    </script>
</body>
</html> 