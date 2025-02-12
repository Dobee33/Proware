<?php
session_start();

include '../includes/connection.php';

$total_items_query = "SELECT SUM(actual_quantity) as total FROM inventory";
$total_result = $conn->query($total_items_query);
$total_items = $total_result->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$pending_orders_query = "SELECT COUNT(*) as pending FROM orders WHERE status = 'Pending'";
$pending_result = $conn->query($pending_orders_query);
$pending_orders = $pending_result->fetch(PDO::FETCH_ASSOC)['pending'] ?? 0;

$low_stock_query = "SELECT COUNT(*) as low_stock 
                    FROM inventory 
                    WHERE actual_quantity <= 20 
                    AND actual_quantity > 0";
$low_stock_result = $conn->query($low_stock_query);
$low_stock_items = $low_stock_result->fetch(PDO::FETCH_ASSOC)['low_stock'] ?? 0;

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
    <link rel="stylesheet" href="../PAMO CSS/dashboard.css">
    <script src="../PAMO JS/dashboard.js"></script>
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
                    <div class="card" onclick="window.location.href='inventory.php'">
                        <div class="card-content">
                            <h3>Total Items</h3>
                            <h2><?php echo number_format($total_items); ?></h2>
                            <p>Total inventory quantity</p>
                        </div>
                        <i class="material-icons">inventory</i>
                    </div>
                    <div class="card" onclick="window.location.href='preorders.php'">
                        <div class="card-content">
                            <h3>Pending Orders</h3>
                            <h2><?php echo number_format($pending_orders); ?></h2>
                            <p>Awaiting processing</p>
                        </div>
                        <i class="material-icons">shopping_cart</i>
                    </div>
                    <div class="card" onclick="redirectToLowStock()">
                        <div class="card-content">
                            <h3>Low Stock Items</h3>
                            <h2><?php echo number_format($low_stock_items); ?></h2>
                            <p>Items need restock</p>
                        </div>
                        <i class="material-icons">warning</i>
                    </div>
                </div>

                <div class="recent-activities">
                    <div class="activities-header">
                        <h3>Recent Activities</h3>
                        <button onclick="clearActivities()" class="clear-btn">
                            <i class="material-icons">clear_all</i>
                            Clear Activities
                        </button>
                    </div>
                    <div class="activity-list">
                        <?php
                        $activities_query = "SELECT * FROM activities 
                        WHERE DATE(timestamp) = CURDATE()
                        ORDER BY timestamp DESC";
                        $activities_result = $conn->query($activities_query);

                        if ($activities_result->rowCount() > 0) {
                            while ($activity = $activities_result->fetch(PDO::FETCH_ASSOC)) {
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
                                    case 'edit_image':
                                        $icon = 'image';
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
        .card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</body>

</html>