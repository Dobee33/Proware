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
                    <input type="text" placeholder="Search reports...">
                </div>
                <div class="header-actions">
                    <button class="generate-report-btn">
                        <i class="material-icons">description</i>
                        Generate Report
                    </button>
                    <i class="material-icons">notifications</i>
                    <i class="material-icons">account_circle</i>
                </div>
            </header>

            <div class="reports-content">
                <div class="report-filters">
                    <h3>Report Filters</h3>
                    <select>
                        <option value="">Report Type</option>
                        <option value="inventory">Inventory Report</option>
                        <option value="sales">Sales Report</option>
                        <option value="orders">Orders Report</option>
                    </select>
                    <select>
                        <option value="">Time Period</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <div class="reports-grid">
                    <div class="report-card">
                        <h3>Inventory Summary</h3>
                        <div class="report-stats">
                            <?php
                            // Add PHP logic for inventory statistics
                            ?>
                        </div>
                    </div>

                    <div class="report-card">
                        <h3>Sales Analytics</h3>
                        <div class="report-chart">
                            <!-- Add chart/graph here -->
                        </div>
                    </div>

                    <div class="report-card">
                        <h3>Recent Reports</h3>
                        <div class="recent-reports-list">
                            <?php
                            // Add PHP logic to display recent reports
                            ?>
                        </div>
                    </div>

                    <div class="report-card">
                        <h3>Performance Metrics</h3>
                        <div class="metrics-container">
                            <?php
                            // Add PHP logic for performance metrics
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>