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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../PAMO JS/inventory.js"></script>
    <script src="../PAMO JS/backend/addItem.js"></script>
    <script src="../PAMO JS/backend/editItem.js"></script>
    <script src="../PAMO JS/backend/addQuantity.js"></script>
    <script src="../PAMO JS/backend/deductQuantity.js"></script>
    <script src="../PAMO JS/backend/addItemSize.js"></script>
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
                    <button onclick="handleEdit()" class="action-btn" id="editBtn" disabled>
                        <i class="material-icons">edit</i> Edit
                    </button>
                    <button onclick="showAddItemModal()" class="action-btn">
                        <i class="material-icons">add_circle</i> New Product
                    </button>
                    <button onclick="showAddItemSizeModal()" class="action-btn">
                        <i class="material-icons">add_box</i> Add Item Size
                    </button>
                    <button onclick="showAddQuantityModal()" class="action-btn">
                        <i class="material-icons">local_shipping</i> New Delivery
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
                        <option value="STI-Jacket">STI Jacket</option>
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
                                <th>Sizes</th>
                                <th>Price</th>
                                <th>Status</th>
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
                                echo "<td>" . $row['sizes'] . "</td>";
                                echo "<td>₱" . number_format($row['price'], 2) . "</td>";
                                echo "<td class='" . $statusClass . "'>" . $status . "</td>";
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
                <h2>Add New Product</h2>
                <span class="close" onclick="closeModal('addItemModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addItemForm" onsubmit="submitNewItem(event)" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="deliveryOrderNumber">Delivery Order #:</label>
                        <input type="text" id="deliveryOrderNumber" name="deliveryOrderNumber" required>
                    </div>
                    <div class="input-group">
                        <label for="newProductItemCode">Item Code:</label>
                        <input type="text" id="newProductItemCode" name="newItemCode" required>
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
                            <option value="STI-Jacket">STI Jacket</option>
                        </select>
                    </div>
                    <div class="input-group" id="shirtTypeGroup" style="display:none;">
                        <label for="shirtTypeSelect">Shirt Type:</label>
                        <select id="shirtTypeSelect" name="shirt_type" style="width:100%;">
                            <option value="">Select Shirt Type</option>
                            <option value="Anniversary Shirt">Anniversary Shirt</option>
                            <option value="T SHIRT WASHDAY">T SHIRT WASHDAY</option>
                            <option value="NSTP Shirt">NSTP Shirt</option>
                        </select>
                    </div>
                    <div class="input-group" id="courseGroup" style="display:none;">
                        <label for="courseSelect">Course:</label>
                        <select id="courseSelect" name="course_id[]" multiple style="width:100%;">
                            <option value="">Select Course</option>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "proware");
                            $result = mysqli_query($conn, "SELECT id, course_name FROM course ORDER BY course_name ASC");
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['course_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="newItemName">Product Name:</label>
                        <input type="text" id="newItemName" name="newItemName" required>
                    </div>
                    <div class="input-group">
                        <label for="newSize">Size:</label>
                        <select id="newProductSize" name="newSize" required>
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
                        <label for="newItemQuantity">Initial Stock:</label>
                        <input type="number" id="newItemQuantity" name="newItemQuantity" min="0" required>
                    </div>
                    <div class="input-group">
                        <label for="newItemDamage">Damaged Items:</label>
                        <input type="number" id="newItemDamage" name="newItemDamage" min="0" value="0" required>
                    </div>
                    <div class="input-group">
                        <label for="newImage">Product Image:</label>
                        <input type="file" id="newImage" name="newImage" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addItemForm" class="save-btn">Add Product</button>
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
                <label for="editNewImage">Upload New Image:</label>
                <input type="file" id="editNewImage" accept="image/*" required>
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
                <h2>New Delivery</h2>
                <span class="close" onclick="closeModal('addQuantityModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addQuantityForm" onsubmit="submitAddQuantity(event)">
                    <div class="order-section">
                        <div class="input-group">
                            <label for="orderNumber">Delivery Order #:</label>
                            <input type="text" id="orderNumber" name="orderNumber" required>
                        </div>
                    </div>
                    
                    <div id="deliveryItems">
                        <div class="delivery-item">
                            <div class="item-close">&times;</div>
                            <div class="item-content">
                                <div class="input-group">
                                    <label for="itemId">Product:</label>
                                    <select name="itemId[]" required>
                                        <option value="">Select Product</option>
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
                                    <label for="quantityToAdd">Delivery Quantity:</label>
                                    <input type="number" name="quantityToAdd[]" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="add-item-btn" onclick="addDeliveryItem()">
                        <i class="material-icons">add_circle</i> Add Another Item
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addQuantityForm" class="save-btn">Record Delivery</button>
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
                    <div class="order-section form-row">
                        <div class="input-group">
                            <label for="transactionNumber">Transaction Number:</label>
                            <input type="text" id="transactionNumber" name="transactionNumber" required>
                        </div>
                        <div class="input-group">
                            <label for="roleCategory">Role:</label>
                            <select id="roleCategory" name="roleCategory" required>
                                <option value="">Select Role</option>
                                <option value="EMPLOYEE">EMPLOYEE</option>
                                <option value="COLLEGE STUDENT">COLLEGE STUDENT</option>
                                <option value="SHS">SHS</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="studentName">Name:</label>
                            <select id="studentName" name="studentName" required>
                                <option value="">Select Name</option>
                                <!-- Options will be populated by JS -->
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="studentIdNumber">ID Number:</label>
                            <input type="text" id="studentIdNumber" name="studentIdNumber" readonly required>
                        </div>
                    </div>
                    <div id="salesItems">
                        <div class="sales-item form-row">
                            <div class="input-group">
                                <label for="itemId">Product:</label>
                                <select name="itemId[]" required>
                                    <option value="">Select Product</option>
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
                                <select name="size[]" required>
                                    <option value="">Select Size</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="quantityToDeduct">Quantity Sold:</label>
                                <input type="number" name="quantityToDeduct[]" min="1" required onchange="calculateItemTotal(this)">
                            </div>
                            <div class="input-group">
                                <label for="pricePerItem">Price per Item:</label>
                                <input type="number" name="pricePerItem[]" step="0.01" min="0" required readonly>
                            </div>
                            <div class="input-group">
                                <label for="itemTotal">SubTotal:</label>
                                <input type="number" name="itemTotal[]" step="0.01" min="0" readonly>
                            </div>
                            <div class="item-close">&times;</div>
                        </div>
                    </div>
                    <button type="button" class="add-item-btn" onclick="addSalesItem()">
                        <i class="material-icons">add_circle</i> Add Another Item
                    </button>
                    <div class="total-section">
                        <div class="input-group">
                            <label for="totalAmount">Total Amount:</label>
                            <input type="number" id="totalAmount" name="totalAmount" step="0.01" min="0" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="deductQuantityForm" class="save-btn">Save</button>
                <button onclick="closeModal('deductQuantityModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="addItemSizeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Item Size</h2>
                <span class="close" onclick="closeModal('addItemSizeModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addItemSizeForm" onsubmit="submitNewItemSize(event)">
                    <div class="input-group">
                        <label for="deliveryOrderNumber">Delivery Order #:</label>
                        <input type="text" id="deliveryOrderNumber" name="deliveryOrderNumber" required>
                    </div>
                    <div class="input-group">
                        <label for="existingItem">Select Item:</label>
                        <select id="existingItem" name="existingItem" required onchange="updateItemCodePrefix()">
                            <option value="">Select Item</option>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "proware");
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Get unique items based on their prefix (before the dash)
                            $sql = "SELECT DISTINCT 
                                    SUBSTRING_INDEX(item_code, '-', 1) as prefix,
                                    item_name,
                                    category
                                    FROM inventory 
                                    ORDER BY item_name";
                            $result = mysqli_query($conn, $sql);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['prefix'] . "' data-name='" . $row['item_name'] . "' data-category='" . $row['category'] . "'>" . 
                                     $row['item_name'] . " (" . $row['prefix'] . ")</option>";
                            }
                            mysqli_close($conn);
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="newItemCode">Item Code:</label>
                        <input type="text" id="newItemCode" name="newItemCode" required>
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
                        <label for="newQuantity">Initial Stock:</label>
                        <input type="number" id="newQuantity" name="newQuantity" min="0" required>
                    </div>
                    <div class="input-group">
                        <label for="newDamage">Damaged Items:</label>
                        <input type="number" id="newDamage" name="newDamage" min="0" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addItemSizeForm" class="save-btn">Add Size</button>
                <button onclick="closeModal('addItemSizeModal')" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="salesReceiptModal" class="modal">
        <div class="modal-content" id="salesReceiptContent">
            <div class="modal-header">
                <h2>Sales Receipt</h2>
                <span class="close" onclick="closeModal('salesReceiptModal')">&times;</span>
            </div>
            <div class="modal-body" id="salesReceiptBody">
                <!-- Receipt content will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" onclick="printSalesReceipt()" class="save-btn">Print</button>
                <button type="button" onclick="closeModal('salesReceiptModal')" class="cancel-btn">Close</button>
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

        function updateItemCodePrefix() {
            const select = document.getElementById('existingItem');
            const selectedOption = select.options[select.selectedIndex];
            const prefix = selectedOption.value;
            const itemCodeField = document.getElementById('newItemCode');
            // Only set the prefix if the field is empty or if the prefix changed
            if (!itemCodeField.value || !itemCodeField.value.startsWith(prefix + '-')) {
                itemCodeField.value = prefix + '-';
            }
        }
    </script>

    <style>
    .order-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #eee;
    }

    .delivery-item {
        position: relative;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 15px;
    }

    .item-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
        color: #dc3545;
        font-weight: bold;
        display: none;
    }

    .delivery-item:not(:first-child) .item-close {
        display: block;
    }

    .item-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: flex-start;
    }

    .input-group {
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }

    .input-group input,
    .input-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        height: 38px;
        font-size: 14px;
    }

    .input-group select {
        background-color: white;
        cursor: pointer;
    }

    .add-item-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 15px 0;
    }

    .add-item-btn:hover {
        background: #218838;
    }

    .modal-footer {
        border-top: 1px solid #ddd;
        padding-top: 15px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .save-btn, .cancel-btn {
        padding: 8px 20px;
        border-radius: 4px;
        cursor: pointer;
        border: none;
    }

    .save-btn {
        background: #007bff;
        color: white;
    }

    .cancel-btn {
        background: #dc3545;
        color: white;
    }

    .save-btn:hover {
        background: #0056b3;
    }

    .cancel-btn:hover {
        background: #c82333;
    }
    </style>
</body>

</html>