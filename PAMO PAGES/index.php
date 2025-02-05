<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "proware");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get total items (sum of actual_quantity)
$total_items_query = "SELECT SUM(actual_quantity) as total FROM inventory";
$total_result = mysqli_query($conn, $total_items_query);
$total_items = mysqli_fetch_assoc($total_result)['total'] ?? 0;

// Get pending orders count
$pending_orders_query = "SELECT COUNT(*) as pending FROM orders WHERE status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_orders_query);
$pending_orders = mysqli_fetch_assoc($pending_result)['pending'] ?? 0;

// Get low stock items count (items with actual_quantity <= 20)
$low_stock_query = "SELECT COUNT(*) as low_stock FROM inventory WHERE actual_quantity <= 20 AND actual_quantity > 0";
$low_stock_result = mysqli_query($conn, $low_stock_query);
$low_stock_items = mysqli_fetch_assoc($low_stock_result)['low_stock'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Dashboard</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <script src="../PAMO JS/script.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton+SC&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <i class="material-icons">search</i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="header-actions">
                    <i class="material-icons">notifications</i>
                    <i class="material-icons">account_circle</i>
                    <button onclick="logout()" class="logout-btn">
                        <i class="material-icons">logout</i>
                        <span>Logout</span>
                    </button>
                </div>
            </header>

            <div class="dashboard">
                <div class="stats-cards">
                    <div class="card">
                        <div class="card-content">
                            <h3>Total Items</h3>
                            <h2><?php echo number_format($total_items); ?></h2>
                            <p>Total inventory quantity</p>
                        </div>
                        <i class="material-icons">inventory</i>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3>Pending Orders</h3>
                            <h2><?php echo number_format($pending_orders); ?></h2>
                            <p>Awaiting processing</p>
                        </div>
                        <i class="material-icons">shopping_cart</i>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3>Low Stock Items</h3>
                            <h2><?php echo number_format($low_stock_items); ?></h2>
                            <p>Items need restock</p>
                        </div>
                        <i class="material-icons">warning</i>
                    </div>
                </div>

                <div class="recent-activities">
                    <h3>Recent Activities</h3>
                    <div class="activity-list">
                        <?php
                        // Fetch recent activities (last 10 activities)
                        $activities_query = "SELECT * FROM activities 
                                           ORDER BY timestamp DESC 
                                           LIMIT 10";
                        $activities_result = mysqli_query($conn, $activities_query);

                        if (mysqli_num_rows($activities_result) > 0) {
                            while ($activity = mysqli_fetch_assoc($activities_result)) {
                                $icon = '';
                                switch ($activity['action_type']) {
                                    case 'price_update':
                                        $icon = 'edit';
                                        break;
                                    case 'quantity_update':
                                        $icon = 'add_circle';
                                        break;
                                    case 'new_item':
                                        $icon = 'add_box';
                                        break;
                                    case 'sale':
                                        $icon = 'point_of_sale';
                                        break;
                                    case 'order_accepted':
                                        $icon = 'check_circle';
                                        break;
                                    default:
                                        $icon = 'info';
                                }
                                ?>
                                <div class="activity-item">
                                    <i class="material-icons"><?php echo $icon; ?></i>
                                    <div class="activity-details">
                                        <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                        <span class="activity-time">
                                            <?php echo date('M d, Y h:i A', strtotime($activity['timestamp'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p class='no-activities'>No recent activities</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .dashboard {
            padding: 20px;
            gap: 20px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-content h2 {
            font-size: 28px;
            margin: 10px 0;
            color: #333;
        }

        .card-content p {
            color: #666;
            font-size: 14px;
        }

        .card i {
            font-size: 40px;
            color: #4CAF50;
        }

        .recent-activities {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .activity-list {
            margin-top: 15px;
        }

        .activity-item {
            display: flex;
            align-items: start;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item i {
            margin-right: 15px;
            color: #4CAF50;
        }

        .activity-details {
            flex: 1;
        }

        .activity-details p {
            margin: 0;
            color: #333;
        }

        .activity-time {
            font-size: 12px;
            color: #666;
        }

        .no-activities {
            text-align: center;
            color: #666;
            padding: 20px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #0072BC;
        }

        .logout-btn i {
            font-size: 20px;
            color: white;
        }
    </style>

    <script>
        function logout() {
            // Redirect to logout.php
            window.location.href = '../Pages/login.php';
        }
    </script>
</body>

</html>