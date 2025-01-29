<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Pre Orders</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <link rel="stylesheet" href="../PAMO CSS/preorders.css">
    <script src="../PAMO JS/preorders.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <i class="material-icons">search</i>
                    <input type="text" placeholder="Search pre-orders...">
                </div>
                <div class="header-actions">
                    <button class="new-order-btn">
                        <i class="material-icons">add</i>
                        New Pre-Order
                    </button>
                    <i class="material-icons">notifications</i>
                    <i class="material-icons">account_circle</i>
                </div>
            </header>

            <div class="preorders-content">
                <div class="filters">
                    <h3>Filters</h3>
                    <select>
                        <option value="">Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select>
                        <option value="">Date Range</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>

                <div class="preorders-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Add PHP logic here to fetch and display pre-orders
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>