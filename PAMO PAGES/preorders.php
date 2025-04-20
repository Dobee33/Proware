<?php
session_start();
require_once '../Includes/connection.php';

// Get status from URL parameter or filter dropdown
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query based on status
$query = "
    SELECT po.*, a.first_name, a.last_name, a.email, a.program_or_position
    FROM pre_orders po
    JOIN account a ON po.user_id = a.id
";

if ($status) {
    $query .= " WHERE po.status = :status";
}

$query .= " ORDER BY po.created_at DESC";

$stmt = $conn->prepare($query);

if ($status) {
    $stmt->bindParam(':status', $status);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Pre Orders</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <link rel="stylesheet" href="../PAMO CSS/preorders.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <i class="material-icons">search</i>
                    <input type="text" id="searchInput" placeholder="Search pre-orders...">
                </div>
                <div class="header-actions">
                    <div class="filter-dropdown">
                        <select id="statusFilter" onchange="filterByStatus(this.value)">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    <div class="notification-icon">
                        <i class="material-icons">notifications</i>
                        <span class="notification-badge" id="notificationCount">0</span>
                    </div>
                </div>
            </header>

            <div class="preorders-content">
                <div class="orders-grid">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): 
                            $order_items = json_decode($order['items'], true);
                            $total_amount = 0;
                            foreach ($order_items as $item) {
                                $total_amount += $item['price'] * $item['quantity'];
                            }
                        ?>
                            <div class="order-card" data-status="<?php echo $order['status']; ?>">
                                <div class="order-header">
                                    <h3>Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                    <span class="status-badge <?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="order-details">
                                    <div class="customer-info">
                                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                        <p><strong>Course/Strand:</strong> <?php echo htmlspecialchars($order['program_or_position']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                    </div>
                                    
                                    <div class="items-list">
                                        <h4>Ordered Items:</h4>
                                        <div class="items-table">
                                            <div class="table-header">
                                                <span class="item-name">Item</span>
                                                <span class="item-size">Size</span>
                                                <span class="item-quantity">Qty</span>
                                                <span class="item-price">Price</span>
                                            </div>
                                            <?php foreach ($order_items as $item): 
                                                // Remove size suffix from item name
                                                $clean_name = rtrim($item['item_name'], " SMLX234567");
                                            ?>
                                                <div class="table-row">
                                                    <span class="item-name"><?php echo htmlspecialchars($clean_name); ?></span>
                                                    <span class="item-size"><?php echo htmlspecialchars($item['size'] ?? 'N/A'); ?></span>
                                                    <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                                                    <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="order-footer">
                                        <div class="total-amount">
                                            <strong>Total:</strong> ₱<?php echo number_format($total_amount, 2); ?>
                                        </div>
                                        <div class="order-date">
                                            <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?>
                                            <?php if ($order['status'] === 'completed' && isset($order['payment_date'])): ?>
                                                <br>
                                                <span class="payment-date">Paid: <?php echo date('F d, Y h:i A', strtotime($order['payment_date'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($order['status'] === 'pending'): ?>
                                    <div class="order-actions">
                                        <button class="accept-btn" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'approved')">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                        <button class="reject-btn" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'rejected')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                <?php elseif ($order['status'] === 'approved'): ?>
                                    <div class="order-actions">
                                        <button class="complete-btn" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                            <i class="fas fa-check-double"></i> Mark as Completed (After Payment)
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-orders">
                            <i class="material-icons">shopping_cart</i>
                            <p>No pre-orders found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Update order status
        function updateOrderStatus(orderId, status) {
            console.log('Updating order:', orderId, 'to status:', status);
            
            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating order status: ' + data.message);
                    console.error('Error details:', data.debug);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order status. Check console for details.');
            });
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.order-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Filter by status
        function filterByStatus(status) {
            window.location.href = `preorders.php?status=${status}`;
        }

        // Update notification count
        function updateNotificationCount() {
            const pendingOrders = document.querySelectorAll('.order-card[data-status="pending"]').length;
            document.getElementById('notificationCount').textContent = pendingOrders;
        }

        // Initial notification count update
        updateNotificationCount();
    </script>

    <style>
        /* Add this to your existing styles */
        .items-list {
            margin: 1.5rem 0;
            background: #f8f9fa;
            padding: 1.2rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .items-list h4 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #ddd;
        }

        .items-table {
            width: 100%;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 2px solid #0056b3;
            font-weight: 600;
            color: #0056b3;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .table-row:last-child {
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
            color: #0056b3;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
        }

        .order-header {
            background: #f8f9fa;
            padding: 1.2rem;
            border-bottom: 1px solid #eee;
        }

        .order-details {
            padding: 1.5rem;
        }

        .customer-info {
            margin-bottom: 1.5rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .customer-info p {
            margin: 0.5rem 0;
            color: #333;
        }

        .customer-info strong {
            color: #0056b3;
            min-width: 120px;
            display: inline-block;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .total-amount {
            font-size: 1.1rem;
            color: #333;
        }

        .total-amount strong {
            color: #0056b3;
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-actions {
            padding: 1rem;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .accept-btn, .reject-btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .accept-btn {
            background: #28a745;
            color: white;
        }

        .accept-btn:hover {
            background: #218838;
        }

        .reject-btn {
            background: #dc3545;
            color: white;
        }

        .reject-btn:hover {
            background: #c82333;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</body>

</html>