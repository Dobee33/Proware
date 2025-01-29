<?php
session_start();
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
                </div>
            </header>

            <div class="dashboard">
                <div class="stats-cards">
                    <div class="card">
                        <div class="card-content">
                            <h3>Total Items</h3>
                            <h2><?php echo isset($total_items) ? $total_items : '0'; ?></h2>
                        </div>
                        <i class="material-icons">inventory</i>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3>Pending Orders</h3>
                            <h2><?php echo isset($pending_orders) ? $pending_orders : '0'; ?></h2>
                        </div>
                        <i class="material-icons">shopping_cart</i>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <h3>Low Stock Items</h3>
                            <h2><?php echo isset($low_stock_items) ? $low_stock_items : '0'; ?></h2>
                        </div>
                        <i class="material-icons">warning</i>
                    </div>
                </div>

                <div class="recent-activities">
                    <h3>Recent Activities</h3>
                    <div class="activity-list">
                        <?php
                        // Add PHP logic here to fetch and display recent activities
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>