<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Inventory</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <link rel="stylesheet" href="../PAMO CSS/inventory.css">
    <script src="../PAMO JS/inventory.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <i class="material-icons">search</i>
                    <input type="text" placeholder="Search inventory...">
                </div>
                <div class="header-actions">
                    <button class="add-item-btn">
                        <i class="material-icons">add</i>
                        Add New Item
                    </button>
                    <i class="material-icons">notifications</i>
                    <i class="material-icons">account_circle</i>
                </div>
            </header>

            <div class="inventory-content">
                <div class="filters">
                    <h3>Filters</h3>
                    <select>
                        <option value="">Category</option>
                        <option value="STI-Uniform">STI Uniform</option>
                        <option value="STI-Shirts">STI Shirts</option>
                        <option value="STI-Accessories">STI Accessories</option>
                    </select>
                    <select>
                        <option value="">Status</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                </div>

                <div class="inventory-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Add PHP logic here to fetch and display inventory items
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>