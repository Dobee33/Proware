<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Reports</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <link rel="stylesheet" href="../PAMO CSS/reports.css">
    <script src="../PAMO JS/reports.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <i class="material-icons">search</i>
                    <input type="text" id="searchInput" placeholder="Search reports...">
                </div>
                <div class="header-actions">
                    <div class="date-filters">
                        <input type="date" id="startDate" placeholder="Start Date">
                        <input type="date" id="endDate" placeholder="End Date">
                        <button onclick="clearDates()" class="clear-date-btn">
                            <i class="material-icons">clear</i>
                            Clear
                        </button>
                    </div>
                    <button onclick="generateReport()" class="generate-report-btn">
                        <i class="material-icons">assessment</i>
                        Generate Report
                    </button>
                    <button onclick="exportToExcel()" class="export-btn">
                        <i class="material-icons">table_view</i>
                        Export to Excel
                    </button>
                </div>
            </header>

            <div class="reports-content">
                <div class="report-filters">
                    <h3>Report Filters</h3>
                    <select id="reportType" onchange="changeReportType()">
                        <option value="inventory">Inventory Report</option>
                        <option value="sales">Sales Report</option>
                        <option value="audit">Audit Trail</option>
                    </select>
                </div>

                <!-- Inventory Report Table -->
                <div id="inventoryReport" class="report-table">
                    <h3>Inventory Report</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Beginning Quantity</th>
                                <th>New Delivery</th>
                                <th>Actual Quantity</th>
                                <th>Damage</th>
                                <th>Sold Quantity</th>
                                <th>Status</th>
                                <th>Date Delivered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "proware");
                            $sql = "SELECT * FROM inventory ORDER BY created_at DESC";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$row['item_code']}</td>";
                                echo "<td>{$row['item_name']}</td>";
                                echo "<td>{$row['category']}</td>";
                                echo "<td>{$row['beginning_quantity']}</td>";
                                echo "<td>{$row['new_delivery']}</td>";
                                echo "<td>{$row['actual_quantity']}</td>";
                                echo "<td>{$row['damage']}</td>";
                                echo "<td>{$row['sold_quantity']}</td>";
                                echo "<td>{$row['status']}</td>";
                                echo "<td>{$row['created_at']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Sales Report Table -->
                <div id="salesReport" class="report-table" style="display: none;">
                    <h3>Sales Report</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity Sold</th>
                                <th>Price</th>
                                <th>Total Amount</th>
                                <th>Date Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT i.item_code, i.item_name, i.category, i.sold_quantity, i.price, 
                                   (i.sold_quantity * i.price) as total_amount, i.created_at 
                                   FROM inventory i 
                                   WHERE i.sold_quantity > 0 
                                   ORDER BY i.created_at DESC";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$row['item_code']}</td>";
                                echo "<td>{$row['item_name']}</td>";
                                echo "<td>{$row['category']}</td>";
                                echo "<td>{$row['sold_quantity']}</td>";
                                echo "<td>₱" . number_format($row['price'], 2) . "</td>";
                                echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";
                                echo "<td>{$row['created_at']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Audit Trail Table -->
                <div id="auditReport" class="report-table" style="display: none;">
                    <h3>Audit Trail</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Action Type</th>
                                <th>Item Code</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM activities ORDER BY timestamp DESC";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$row['timestamp']}</td>";
                                echo "<td>{$row['action_type']}</td>";
                                echo "<td>{$row['item_code']}</td>";
                                echo "<td>{$row['description']}</td>";
                                echo "</tr>";
                            }
                            mysqli_close($conn);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>