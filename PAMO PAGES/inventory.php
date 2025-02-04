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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <i class="material-icons">search</i>
                    <input type="text" id="searchInput" placeholder="Search by item name..." oninput="searchItems()">
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

            <div class="inventory-content">
                <div class="action-buttons-container">
                    <button onclick="showAddItemModal()" class="action-btn">
                        <i class="material-icons">add_circle</i> Add New Item
                    </button>
                    <button onclick="handleEditPrice()" class="action-btn" id="editPriceBtn" disabled>
                        <i class="material-icons">edit</i> Edit Price
                    </button>
                    <button onclick="handleAddQuantity()" class="action-btn" id="addQuantityBtn" disabled>
                        <i class="material-icons">add_circle</i> Add Quantity
                    </button>
                </div>

                <div class="filters">
                    <h3>Filters</h3>
                    <select id="categoryFilter" onchange="applyFilters()">
                        <option value="">All Categories</option>
                        <option value="Tertiary-Uniform">Tertiary-Uniform</option>
                        <option value="SHS-Uniform">SHS-Uniform</option>
                        <option value="STI-Shirts">STI-Shirts</option>
                        <option value="STI-Accessories">STI-Accessories</option>
                    </select>
                    <select id="sizeFilter">
                        <option value="">All Sizes</option>
                    </select>
                    <select id="statusFilter" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>

                </div>

                <div class="inventory-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Actual Quantity</th>
                                <th>New Delivery</th>
                                <th>Beginning Quantity</th>
                                <th>Damage</th>
                                <th>Sizes</th>
                                <th>Price</th>
                                <th>Sold Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Database connection
                            $conn = mysqli_connect("localhost", "root", "", "proware");

                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Fetch inventory items
                            $sql = "SELECT * FROM inventory";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = '';
                                switch (strtolower($row['status'])) {
                                    case 'in stock':
                                        $statusClass = 'status-in-stock';
                                        break;
                                    case 'low stock':
                                        $statusClass = 'status-low-stock';
                                        break;
                                    case 'out of stock':
                                        $statusClass = 'status-out-of-stock';
                                        break;
                                }

                                echo "<tr onclick='selectRow(this, \"" . $row['item_code'] . "\", " . $row['price'] . ")'>";
                                echo "<td>" . $row['item_code'] . "</td>";
                                echo "<td>" . $row['item_name'] . "</td>";
                                echo "<td>" . $row['category'] . "</td>";
                                echo "<td>" . (isset($row['actual_quantity']) ? $row['actual_quantity'] : '0') . "</td>";
                                echo "<td>" . (isset($row['new_delivery']) ? $row['new_delivery'] : '0') . "</td>";
                                echo "<td>" . (isset($row['beginning_quantity']) ? $row['beginning_quantity'] : '0') . "</td>";
                                echo "<td>" . (isset($row['damage']) ? $row['damage'] : '0') . "</td>";
                                echo "<td>" . $row['sizes'] . "</td>";
                                echo "<td>₱" . number_format($row['price'], 2) . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "<td class='" . $statusClass . "'>" . $row['status'] . "</td>";
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

    <div id="editPriceModal" class="modal">
        <div class="modal-content">
            <h2>Edit Price</h2>
            <input type="hidden" id="itemId">
            <div class="input-group">
                <label for="newPrice">New Price:</label>
                <input type="number" id="newPrice" step="0.01" min="0" placeholder="Enter new price">
            </div>
            <div class="modal-buttons">
                <button onclick="updatePrice()" class="save-btn">Save</button>
                <button onclick="closeModal('editPriceModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="addQuantityModal" class="modal">
        <div class="modal-content">
            <h2>Add Quantity</h2>
            <input type="hidden" id="quantityItemId">
            <input type="number" id="quantityToAdd" placeholder="Enter quantity to add">
            <div class="modal-buttons">
                <button onclick="updateQuantity()">Save</button>
                <button onclick="closeModal('addQuantityModal')">Cancel</button>
            </div>
        </div>
    </div>

    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <h2>Add New Item</h2>
            <form id="addItemForm" onsubmit="submitNewItem(event)">
                <div class="input-group">
                    <label for="newItemCode">Item Code:</label>
                    <input type="text" id="newItemCode" name="newItemCode" required>
                </div>

                <div class="input-group">
                    <label for="newCategory">Category:</label>
                    <select id="newCategory" name="newCategory" required>
                        <option value="">Select Category</option>
                        <option value="Tertiary-Uniform">Tertiary-Uniform</option>
                        <option value="SHS-Uniform">SHS-Uniform</option>
                        <option value="STI-Shirts">STI-Shirts</option>
                        <option value="STI-Accessories">STI-Accessories</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="newItemName">Item Name:</label>
                    <input type="text" id="newItemName" name="newItemName" required>
                </div>

                <div class="input-group">
                    <label for="newSize">Size:</label>
                    <select id="newSize" name="newSize" required>
                        <option value="">Select Size</option>
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
                    <label for="newItemPrice">Price:</label>
                    <input type="number" id="newItemPrice" name="newItemPrice" min="0" step="0.01" required>
                </div>

                <div class="input-group">
                    <label for="newItemQuantity">Quantity:</label>
                    <input type="number" id="newItemQuantity" name="newItemQuantity" min="0" required>
                </div>

                <div class="modal-buttons">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" onclick="closeModal('addItemModal')" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .action-buttons-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .action-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .action-btn i {
            font-size: 20px;
        }

        .inventory-table tbody tr {
            cursor: pointer;
        }

        .inventory-table tbody tr.selected {
            background-color: #e3f2fd;
        }

        .inventory-table {
            max-height: 500px;
            /* Adjust this value based on your needs */
            overflow-y: auto;
            position: relative;
        }

        .inventory-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table thead {
            position: sticky;
            top: 0;
            background-color: #f5f5f5;
            /* Match your design's background color */
            z-index: 1;
        }

        .inventory-table th {
            border-bottom: 2px solid #ddd;
            /* Add a border to separate header from content */
        }

        /* Add shadow effect to indicate scrollable content */
        .inventory-table::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;

            pointer-events: none;
        }

        .status-in-stock {
            color: #4CAF50;
            font-weight: bold;
            background-color: rgba(76, 175, 80, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
        }

        .status-low-stock {
            color: #FFA500;
            font-weight: bold;
            background-color: rgba(255, 165, 0, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
        }

        .status-out-of-stock {
            color: #FF0000;
            font-weight: bold;
            background-color: rgba(255, 0, 0, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
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
        let selectedItemCode = null;
        let selectedPrice = null;

        function selectRow(row, itemCode, price) {
            // Check if clicking the same row (unselect)
            if (selectedItemCode === itemCode) {
                // Unselect the row
                row.classList.remove('selected');
                selectedItemCode = null;
                selectedPrice = null;

                // Disable buttons
                document.getElementById('editPriceBtn').disabled = true;
                document.getElementById('addQuantityBtn').disabled = true;
                return;
            }

            // Remove selected class from all rows
            document.querySelectorAll('.inventory-table tbody tr').forEach(tr => {
                tr.classList.remove('selected');
            });

            // Add selected class to clicked row
            row.classList.add('selected');

            // Store selected item details
            selectedItemCode = itemCode;
            selectedPrice = price;

            // Enable buttons
            document.getElementById('editPriceBtn').disabled = false;
            document.getElementById('addQuantityBtn').disabled = false;
        }

        function handleEditPrice() {
            if (!selectedItemCode) {
                alert('Please select an item first');
                return;
            }
            editPrice(selectedItemCode, selectedPrice);
        }

        function handleAddQuantity() {
            if (!selectedItemCode) {
                alert('Please select an item first');
                return;
            }
            addQuantity(selectedItemCode);
        }

        function editPrice(itemCode, currentPrice) {
            console.log('Edit Price clicked:', itemCode, currentPrice); // Debug log
            document.getElementById('itemId').value = itemCode;
            document.getElementById('newPrice').value = currentPrice;
            document.getElementById('editPriceModal').style.display = 'block';
        }

        function addQuantity(itemCode) {
            document.getElementById('quantityItemId').value = itemCode;
            document.getElementById('quantityToAdd').value = '';
            document.getElementById('addQuantityModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function updatePrice() {
            const itemCode = document.getElementById('itemId').value;
            const newPrice = document.getElementById('newPrice').value;

            console.log('Updating price:', itemCode, newPrice); // Debug log

            if (!newPrice || newPrice <= 0) {
                alert('Please enter a valid price');
                return;
            }

            // Send AJAX request to update price
            fetch('update_price.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_code=${encodeURIComponent(itemCode)}&price=${encodeURIComponent(newPrice)}`
            })
                .then(response => {
                    console.log('Response received'); // Debug log
                    return response.json();
                })
                .then(data => {
                    console.log('Data:', data); // Debug log
                    if (data.success) {
                        alert('Price updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating price: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Debug log
                    alert('Error updating price: ' + error);
                });

            closeModal('editPriceModal');
        }

        function updateQuantity() {
            const itemCode = document.getElementById('quantityItemId').value;
            const quantityToAdd = document.getElementById('quantityToAdd').value;

            // Send AJAX request to update quantity
            fetch('update_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_code=${itemCode}&quantity=${quantityToAdd}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Quantity updated successfully!');
                        // Update the status immediately without reloading
                        const row = document.querySelector(`tr[data-item-code="${itemCode}"]`);
                        if (row) {
                            updateStockStatus(row);
                        }
                        location.reload(); // Keep this for now to ensure all data is fresh
                    } else {
                        alert('Error updating quantity: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error updating quantity: ' + error);
                });

            closeModal('addQuantityModal');
        }

        function showAddItemModal() {
            document.getElementById('addItemModal').style.display = 'flex';
            document.getElementById('addItemForm').reset();
        }

        function submitNewItem(event) {
            event.preventDefault();

            // Get form values and validate
            const formData = {
                item_code: document.getElementById('newItemCode').value.trim(),
                category: document.getElementById('newCategory').value.trim(),
                item_name: document.getElementById('newItemName').value.trim(),
                sizes: document.getElementById('newSize').value.trim(),
                price: parseFloat(document.getElementById('newItemPrice').value),
                quantity: parseInt(document.getElementById('newItemQuantity').value),
                actual_quantity: parseInt(document.getElementById('newItemQuantity').value), // Set initial actual quantity
                beginning_quantity: parseInt(document.getElementById('newItemQuantity').value), // Set initial beginning quantity
                new_delivery: 0, // Initialize new delivery as 0
                damage: 0, // Initialize damage as 0
                status: 'In Stock' // Set initial status
            };

            // Debug log
            console.log('Sending data:', formData);

            // Validation
            if (!formData.item_code || !formData.category || !formData.item_name ||
                !formData.sizes || isNaN(formData.price) || isNaN(formData.quantity)) {
                alert('Please fill in all required fields with valid values');
                return;
            }

            fetch('add_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(async response => {
                    const text = await response.text();
                    console.log('Raw server response:', text);

                    try {
                        // Try to parse the response as JSON
                        const data = JSON.parse(text);
                        if (data.success) {
                            alert('Item added successfully!');
                            location.reload();
                        } else {
                            throw new Error(data.message || 'Unknown error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        console.error('Response text:', text);
                        throw new Error('Server response was not valid JSON: ' + text);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                });
        }

        // Add this new function to handle size field visibility
        document.getElementById('newCategory').addEventListener('change', function () {
            const sizeGroup = document.querySelector('.input-group:has(#newSize)');
            if (this.value === 'STI-Accessories') {
                sizeGroup.style.display = 'none';
                document.getElementById('newSize').value = 'One Size';
                document.getElementById('newSize').removeAttribute('required');
            } else {
                sizeGroup.style.display = 'block';
                document.getElementById('newSize').setAttribute('required', 'required');
            }
        });

        function searchItems() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const tableRows = document.querySelectorAll('.inventory-table tbody tr');

            tableRows.forEach(row => {
                const itemName = row.querySelector('td:nth-child(2)').textContent.toLowerCase(); // Item Name
                const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Category

                // Check if search term matches either item name or category
                if (itemName.includes(searchTerm) || category.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function updateStockStatus(row) {
            const actualQuantity = parseInt(row.querySelector('td:nth-child(4)').textContent);
            const statusCell = row.querySelector('td:nth-child(11)');

            let status, statusClass;

            if (actualQuantity <= 0) {
                status = 'Out of Stock';
                statusClass = 'status-out-of-stock';
            } else if (actualQuantity <= 20) {
                status = 'Low Stock';
                statusClass = 'status-low-stock';
            } else {
                status = 'In Stock';
                statusClass = 'status-in-stock';
            }

            // Remove any existing status classes
            statusCell.classList.remove('status-in-stock', 'status-low-stock', 'status-out-of-stock');
            // Add new status class
            statusCell.classList.add(statusClass);
            statusCell.textContent = status;
        }

        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            const sizeFilter = document.getElementById('sizeFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;

            const tableRows = document.querySelectorAll('.inventory-table tbody tr');

            tableRows.forEach(row => {
                const itemName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Category column
                const size = row.querySelector('td:nth-child(8)').textContent;
                const status = row.querySelector('td:nth-child(11)').textContent.trim();

                const matchesSearch = searchTerm === '' ||
                    itemName.includes(searchTerm) ||
                    category.includes(searchTerm);
                const matchesCategory = categoryFilter === '' || category === categoryFilter;
                const matchesSize = sizeFilter === '' || size === sizeFilter;
                const matchesStatus = statusFilter.toLowerCase() === '' || status.toLowerCase() === statusFilter.toLowerCase();

                if (matchesSearch && matchesCategory && matchesSize && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Populate size filter dropdown dynamically
        function populateFilters() {
            const sizeFilter = document.getElementById('sizeFilter');
            const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL', '6XL', '7XL'];

            // Clear existing options except the first one
            sizeFilter.innerHTML = '<option value="">All Sizes</option>';

            // Add size options
            sizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                sizeFilter.appendChild(option);
            });
        }

        // Call populateFilters when page loads
        document.addEventListener('DOMContentLoaded', populateFilters);

        // Add event listeners for all filters
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('categoryFilter').addEventListener('change', applyFilters);
        document.getElementById('sizeFilter').addEventListener('change', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);

        // Call applyFilters on page load
        document.addEventListener('DOMContentLoaded', function () {
            applyFilters();
        });

        function logout() {
            // Redirect to logout.php
            window.location.href = '../Pages/login.php';
        }
    </script>
</body>

</html>