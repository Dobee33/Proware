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
    <script src="../PAMO JS/inventory.js"></script>
    <script>
        // Check for low stock filter on page load
        document.addEventListener('DOMContentLoaded', function() {
            const applyLowStockFilter = sessionStorage.getItem('applyLowStockFilter');
            if (applyLowStockFilter === 'true') {
                // Set the status filter to Low Stock
                document.getElementById('statusFilter').value = 'Low Stock';
                // Apply the filters
                applyFilters();
                // Clear the session storage
                sessionStorage.removeItem('applyLowStockFilter');
            }
        });
    </script>
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
                    
                    <button onclick="handleEdit()" class="action-btn" id="editBtn" disabled>
                        <i class="material-icons">edit</i> Edit
                    </button>
                    
                    <button onclick="showAddQuantityModal()" class="action-btn">
                        <i class="material-icons">add_shopping_cart</i> Add Quantity
                    </button>
                    
                    <button onclick="showDeductQuantityModal()" class="action-btn">
                        <i class="material-icons">remove_shopping_cart</i> Sales Entry
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
                        <option value="SHS-PE">SHS-PE</option>
                        <option value="Tertiary-PE">Tertiary-PE</option>
                    </select>
                    <select id="sizeFilter" onchange="applyFilters()">
                        <option value="">All Sizes</option>
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

                    </select>
                    <select id="statusFilter" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                        <button onclick="clearAllFilters()" class="clear-filters-btn">
                            <i class="material-icons">clear</i> Clear Filters
                        </button>
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
                                <th>Date Delivered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "proware");

                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            $search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
                            $where_clause = '';

                            if (!empty($search)) {
                                $where_clause = "WHERE item_name LIKE '%$search%' OR item_code LIKE '%$search%'";
                            }

                            $sql = "SELECT * FROM inventory $where_clause ORDER BY created_at DESC";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = '';
                                if ($row['actual_quantity'] <= 0) {
                                    $status = 'Out of Stock';
                                    $statusClass = 'status-out-of-stock';
                                } else if ($row['actual_quantity'] <= 20) {
                                    $status = 'Low Stock';
                                    $statusClass = 'status-low-stock';
                                } else {
                                    $status = 'In Stock';
                                    $statusClass = 'status-in-stock';
                                }

                                echo "<tr data-item-code='" . $row['item_code'] . "' data-created-at='" . $row['created_at'] . "' data-category='" . strtolower($row['category']) . "' onclick='selectRow(this, \"" . $row['item_code'] . "\", " . $row['price'] . ")'>";
                                echo "<td>" . $row['item_code'] . "</td>";
                                echo "<td>" . $row['item_name'] . "</td>";
                                echo "<td>" . $row['category'] . "</td>";
                                echo "<td>" . (isset($row['actual_quantity']) ? $row['actual_quantity'] : '0') . "</td>";
                                echo "<td>" . (isset($row['new_delivery']) ? $row['new_delivery'] : '0') . "</td>";
                                echo "<td>" . (isset($row['beginning_quantity']) ? $row['beginning_quantity'] : '0') . "</td>";
                                echo "<td>" . (isset($row['damage']) ? $row['damage'] : '0') . "</td>";
                                echo "<td>" . $row['sizes'] . "</td>";
                                echo "<td>₱" . number_format($row['price'], 2) . "</td>";
                                echo "<td>" . (isset($row['sold_quantity']) ? $row['sold_quantity'] : '0') . "</td>";
                                echo "<td class='" . $statusClass . "'>" . $status . "</td>";
                                echo "<td>" . $row['created_at'] . "</td>";
                                echo "<!-- Item Code: " . $row['item_code'] . " -->";
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

    <div id="editItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Item</h2>
                <span class="close" onclick="closeModal('editItemModal')">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editItemId">
                <div class="input-group">
                    <label for="editItemCode">Item Code:</label>
                    <input type="text" id="editItemCode" disabled>
                </div>
                <div class="input-group">
                    <label for="editItemName">Item Name:</label>
                    <input type="text" id="editItemName" disabled>
                </div>
                <div class="input-group">
                    <label for="editCategory">Category:</label>
                    <input type="text" id="editCategory" disabled>
                </div>
                <div class="input-group">
                    <label for="editActualQuantity">Actual Quantity:</label>
                    <input type="number" id="editActualQuantity" disabled>
                </div>
                <div class="input-group">
                    <label for="editSize">Size:</label>
                    <input type="text" id="editSize" disabled>
                </div>
                <div class="input-group">
                    <label for="editPrice">Price:</label>
                    <input type="number" id="editPrice" disabled>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="showEditPriceModal()" class="save-btn">Edit Price</button>
                <button onclick="showEditImageModal()" class="save-btn">Edit Image</button>
                <button onclick="closeModal('editItemModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Item</h2>
                <span class="close" onclick="closeModal('addItemModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addItemForm" onsubmit="submitNewItem(event)" enctype="multipart/form-data">
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
                            <option value="SHS-PE">SHS-PE</option>
                            <option value="Tertiary-PE">Tertiary-PE</option>
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
                    <div class="input-group">
                        <label for="newItemDamage">Damage:</label>
                        <input type="number" id="newItemDamage" name="newItemDamage" min="0" value="0" required>
                    </div>
                    <div class="input-group">
                        <label for="newImage">Upload Image:</label>
                        <input type="file" id="newImage" name="newImage" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addItemForm" class="save-btn">Save</button>
                <button type="button" onclick="closeModal('addItemModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="editPriceModal" class="modal">
        <div class="modal-content">
            <h2>Edit Price</h2>
            <input type="hidden" id="priceItemId">
            <div class="input-group">
                <label for="newPrice">New Price:</label>
                <input type="number" id="newPrice" step="0.01" min="0" required>
            </div>
            <div class="modal-buttons">
                <button onclick="submitEditPrice()" class="save-btn">Save</button>
                <button onclick="closeModal('editPriceModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="editImageModal" class="modal">
        <div class="modal-content">
            <h2>Edit Image</h2>
            <input type="hidden" id="imageItemId">
            <div class="input-group">
                <label for="newImage">Upload New Image:</label>
                <input type="file" id="newImage" accept="image/*" required>
            </div>
            <div class="modal-buttons">
                <button onclick="submitEditImage()" class="save-btn">Save</button>
                <button onclick="closeModal('editImageModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="addQuantityModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Quantity</h2>
                <span class="close" onclick="closeModal('addQuantityModal')">&times;</span>
            </div>
            <div class="modal-body">
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addQuantityForm" class="save-btn">Save</button>
                <button onclick="closeModal('addQuantityModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="deductQuantityModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Sales Entry</h2>
                <span class="close" onclick="closeModal('deductQuantityModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="deductQuantityForm" onsubmit="submitDeductQuantity(event)">
                    <div class="input-group">
                        <label for="transactionNumber">Transaction Number:</label>
                        <input type="text" id="transactionNumber" name="transactionNumber" required>
                    </div>
                    <div class="input-group">
                        <label for="deductItemId">Item:</label>
                        <select id="deductItemId" name="deductItemId" required onchange="updateItemDetails()">
                            <option value="">Select Item</option>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "proware");
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            $sql = "SELECT item_code, item_name, category, price FROM inventory ORDER BY item_name";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['item_code'] . "' data-price='" . $row['price'] . "'>" . $row['item_name'] . " (" . $row['item_code'] . ") - " . $row['category'] . "</option>";
                            }
                            mysqli_close($conn);
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="size">Size:</label>
                        <select id="size" name="size" required>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="deductQuantityForm" class="save-btn">Save</button>
                <button onclick="closeModal('deductQuantityModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function showAddQuantityModal() {
            document.getElementById('addQuantityModal').style.display = 'block';
        }

        function showDeductQuantityModal() {
            document.getElementById('deductQuantityModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function submitAddQuantity(event) {
            event.preventDefault();
            
            const orderNumber = document.getElementById('orderNumber').value;
            const itemId = document.getElementById('itemId').value;
            const quantityToAdd = document.getElementById('quantityToAdd').value;
            
            if (!orderNumber || !itemId || !quantityToAdd) {
                alert('Please fill in all required fields');
                return;
            }
            
            const formData = new FormData();
            formData.append('orderNumber', orderNumber);
            formData.append('itemId', itemId);
            formData.append('quantityToAdd', quantityToAdd);
            
            fetch('../PAMO Inventory backend/process_add_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Quantity added successfully!');
                    closeModal('addQuantityModal');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            });
        }

        function updateItemDetails() {
            const itemSelect = document.getElementById('deductItemId');
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            
            if (price) {
                document.getElementById('pricePerItem').value = price;
                calculateTotal();
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
            const itemId = document.getElementById('deductItemId').value;
            const size = document.getElementById('size').value;
            const quantityToDeduct = document.getElementById('quantityToDeduct').value;
            const pricePerItem = document.getElementById('pricePerItem').value;
            const totalAmount = document.getElementById('totalAmount').value;
            
            if (!transactionNumber || !itemId || !size || !quantityToDeduct || !pricePerItem) {
                alert('Please fill in all required fields');
                return;
            }
            
            const formData = new FormData();
            formData.append('transactionNumber', transactionNumber);
            formData.append('itemId', itemId);
            formData.append('size', size);
            formData.append('quantityToDeduct', quantityToDeduct);
            formData.append('pricePerItem', pricePerItem);
            formData.append('totalAmount', totalAmount);
            
            fetch('../PAMO Inventory backend/process_deduct_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sale recorded successfully!');
                    closeModal('deductQuantityModal');
                    location.reload();
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

    <style>

    </style>
</body>

</html>