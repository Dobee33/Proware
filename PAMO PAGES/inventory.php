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
                    <select id="sizeFilter" onchange="applyFilters()">
                        <option value="">All Sizes</option>
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
                            $conn = mysqli_connect("localhost", "root", "", "proware");

                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Pagination settings
                            $records_per_page = 10;
                            $page = isset($_GET['page']) ? $_GET['page'] : 1;
                            $offset = ($page - 1) * $records_per_page;

                            // Search functionality
                            $search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
                            $where_clause = '';

                            if (!empty($search)) {
                                $where_clause = "WHERE item_name LIKE '%$search%' OR item_code LIKE '%$search%'";
                            }

                            // Get total number of records with search
                            $total_records_query = "SELECT COUNT(*) as count FROM inventory $where_clause";
                            $total_records_result = mysqli_query($conn, $total_records_query);
                            $total_records = mysqli_fetch_assoc($total_records_result)['count'];
                            $total_pages = ceil($total_records / $records_per_page);

                            // Modified query with search, LIMIT and OFFSET
                            $sql = "SELECT * FROM inventory $where_clause ORDER BY created_at DESC LIMIT $offset, $records_per_page";
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
                                echo "<td>" . (isset($row['sold_quantity']) ? $row['sold_quantity'] : '0') . "</td>";
                                echo "<td class='" . $statusClass . "'>" . $row['status'] . "</td>";
                                echo "</tr>";
                            }
                            mysqli_close($conn);
                            ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <?php if ($total_pages > 1): ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </div>
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

                <div class="input-group">
                    <label for="newItemDamage">Damage:</label>
                    <input type="number" id="newItemDamage" name="newItemDamage" min="0" value="0" required>
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
            margin-bottom: 20px;
        }

        .inventory-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #f5f5f5;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
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

        /* Filter Section Styling */
        .filters {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filters h3 {
            margin: 0;
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .filters select {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #333;
            font-size: 0.9rem;
            min-width: 160px;
            cursor: pointer;
            transition: all 0.3s ease;
            outline: none;
        }

        .filters select:hover {
            border-color: #4CAF50;
            background-color: #ffffff;
        }

        .filters select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
            background-color: #ffffff;
        }

        .filters select option {
            padding: 8px;
            background-color: #ffffff;
        }

        /* Add hover effect for options */
        .filters select option:hover {
            background-color: #4CAF50;
            color: #ffffff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .filters select {
                width: 100%;
            }
        }
    </style>
</body>

</html>