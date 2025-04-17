<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Add Quantity</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <link rel="stylesheet" href="../PAMO CSS/AddDeduct.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <h1>New Deliveries</h1>
                <div class="header-actions">
                    <a href="inventory.php" class="back-btn">
                        <i class="material-icons">arrow_back</i>
                        <span>Back to Inventory</span>
                    </a>
                </div>
            </header>

            <div class="content-section">
                <div class="form-container">
                    <form id="addQuantityForm" onsubmit="submitAddQuantity(event)">
                        <div class="input-group">
                            <label for="orderNumber">Order Number:</label>
                            <input type="text" id="orderNumber" name="orderNumber" required>
                        </div>

                        <div class="input-group">
                            <label for="itemId">Item:</label>
                            <select id="itemId" name="itemId" required>
                                <option value="">Select Item</option>
                                <?php
                                $conn = mysqli_connect("localhost", "root", "", "proware");
                                if (!$conn) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }

                                $sql = "SELECT item_code, item_name, category FROM inventory ORDER BY item_name";
                                $result = mysqli_query($conn, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['item_code'] . "'>" . $row['item_name'] . " (" . $row['item_code'] . ") - " . $row['category'] . "</option>";
                                }
                                mysqli_close($conn);
                                ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="quantityToAdd">Number of Items to Add:</label>
                            <input type="number" id="quantityToAdd" name="quantityToAdd" min="1" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Submit</button>
                            <button type="button" onclick="window.location.href='inventory.php'" class="btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function submitAddQuantity(event) {
            event.preventDefault();
            
            const orderNumber = document.getElementById('orderNumber').value;
            const itemId = document.getElementById('itemId').value;
            const quantityToAdd = document.getElementById('quantityToAdd').value;
            
            if (!orderNumber || !itemId || !quantityToAdd) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('orderNumber', orderNumber);
            formData.append('itemId', itemId);
            formData.append('quantityToAdd', quantityToAdd);
            
            // Send request
            fetch('process_add_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Quantity added successfully!');
                    window.location.href = 'inventory.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            });
        }
    </script>
</body>

</html> 