<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Sales Entry</title>
    <link rel="stylesheet" href="../PAMO CSS/styles.css">
    <link rel="stylesheet" href="../PAMO CSS/AddDeduct.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <h1>Sales Entry</h1>
                <div class="header-actions">
                    <a href="inventory.php" class="back-btn">
                        <i class="material-icons">arrow_back</i>
                        <span>Back to Inventory</span>
                    </a>
                </div>
            </header>

            <div class="content-section">
                <div class="form-container">
                    <h2>Record Sale</h2>
                    <form id="deductQuantityForm" onsubmit="submitDeductQuantity(event)">
                        <div class="input-group">
                            <label for="transactionNumber">Transaction Number:</label>
                            <input type="text" id="transactionNumber" name="transactionNumber" required>
                        </div>

                        <div class="input-group">
                            <label for="itemId">Item:</label>
                            <select id="itemId" name="itemId" required onchange="updateItemDetails()">
                                <option value="">Select Item</option>
                                <?php
                                $conn = mysqli_connect("localhost", "root", "", "proware");
                                if (!$conn) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }

                                $sql = "SELECT item_code, item_name, category, sizes, price, actual_quantity FROM inventory ORDER BY item_name";
                                $result = mysqli_query($conn, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['item_code'] . "' 
                                        data-sizes='" . $row['sizes'] . "' 
                                        data-price='" . $row['price'] . "' 
                                        data-stock='" . $row['actual_quantity'] . "'>" . 
                                        $row['item_name'] . " (" . $row['item_code'] . ") - " . $row['category'] . 
                                        "</option>";
                                }
                                mysqli_close($conn);
                                ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="size">Size:</label>
                            <select id="size" name="size" required>
                                <option value="">Select Size</option>
                                <option value="No size">XS</option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                                <option value="3XL">3XL</option>
                                <option value="4XL">4XL</option>
                                <option value="5XL">5XL</option>
                                <option value="6XL">6XL</option>
                                <option value="7XL">7XL</option>
                                <option value="One Size">One Size</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="quantityToDeduct">Quantity Sold:</label>
                            <input type="number" id="quantityToDeduct" name="quantityToDeduct" min="1" required onchange="calculateTotal()">
                        </div>

                        <div class="input-group">
                            <label for="pricePerItem">Price per Item:</label>
                            <input type="number" id="pricePerItem" name="pricePerItem" step="0.01" min="0" required onchange="calculateTotal()">
                        </div>

                        <div class="input-group">
                            <label for="totalAmount">Total Amount:</label>
                            <input type="number" id="totalAmount" name="totalAmount" step="0.01" min="0" readonly>
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
        function updateItemDetails() {
            const itemSelect = document.getElementById('itemId');
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            
            if (selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                const stock = selectedOption.getAttribute('data-stock');
                
                document.getElementById('pricePerItem').value = price;
                
                // Update size dropdown based on item
                const sizes = selectedOption.getAttribute('data-sizes');
                const sizeSelect = document.getElementById('size');
                
                // Reset size dropdown
                sizeSelect.innerHTML = '<option value="">Select Size</option>';
                
                // Add sizes based on item
                if (sizes === 'One Size') {
                    const option = document.createElement('option');
                    option.value = 'One Size';
                    option.textContent = 'One Size';
                    sizeSelect.appendChild(option);
                } else {
                    const sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL', '6XL', '7XL'];
                    sizeOptions.forEach(size => {
                        const option = document.createElement('option');
                        option.value = size;
                        option.textContent = size;
                        sizeSelect.appendChild(option);
                    });
                }
                
                // Update max quantity based on available stock
                document.getElementById('quantityToDeduct').max = stock;
                document.getElementById('quantityToDeduct').placeholder = `Max: ${stock}`;
            }
        }
        
        function calculateTotal() {
            const quantity = parseFloat(document.getElementById('quantityToDeduct').value) || 0;
            const price = parseFloat(document.getElementById('pricePerItem').value) || 0;
            const total = quantity * price;
            
            document.getElementById('totalAmount').value = total.toFixed(2);
        }
        
        function submitDeductQuantity(event) {
            event.preventDefault();
            
            const transactionNumber = document.getElementById('transactionNumber').value;
            const itemId = document.getElementById('itemId').value;
            const size = document.getElementById('size').value;
            const quantityToDeduct = document.getElementById('quantityToDeduct').value;
            const pricePerItem = document.getElementById('pricePerItem').value;
            const totalAmount = document.getElementById('totalAmount').value;
            
            if (!transactionNumber || !itemId || !size || !quantityToDeduct || !pricePerItem) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('transactionNumber', transactionNumber);
            formData.append('itemId', itemId);
            formData.append('size', size);
            formData.append('quantityToDeduct', quantityToDeduct);
            formData.append('pricePerItem', pricePerItem);
            formData.append('totalAmount', totalAmount);
            
            // Send request
            fetch('process_deduct_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sale recorded successfully!');
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