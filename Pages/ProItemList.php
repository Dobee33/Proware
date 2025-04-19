<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/ProItemList.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <title>Product List</title>
</head>

<body>
    <?php
    include("../Includes/Header.php");
    ?>
    <section class="header">
        <div class="header-content">
            <h1 data-aos="fade-up">All Products - PAMO</h1>
            <p data-aos="fade-up" data-aos-delay="100">Explore our full range of items, all in one place!</p>
            <div class="search-container" data-aos="fade-up" data-aos-delay="200">
                <input type="text" id="search" placeholder="Search products...">
                <button type="button" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </section>

    <div class="container">
        <aside class="sidebar" data-aos="fade-right">
            <div class="filter-header">
                <h2>Filters</h2>
                <button class="clear-filters">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>

            <div class="filter-group">
                <h3>Categories</h3>
                <input type="checkbox" id="tertiary" class="category-checkbox">
                <label for="tertiary" style = "font-size: 30px; font-weight: 500;">Tertiary</label>
                <div class="subcategory" id="tertiary-sub">
                    <label><input type="checkbox"> BS in Information Technology (BSIT)</label><br>
                    <label><input type="checkbox"> BS in Computer Science (BSCS)</label><br>
                    <label><input type="checkbox"> BS in Business Administration (BSBA)</label><br>
                    <label><input type="checkbox"> BS in Tourism Management (BSTM)</label><br>
                    <label><input type="checkbox"> Bachelor of Multimedia Arts (BMMA)</label><br>
                    <label><input type="checkbox"> BS in Computer Engineering (BSCpE)</label><br>
                </div>
            </div>

            <div class="category">
                <input type="checkbox" id="senior-high" class="category-checkbox">
                <label for="senior-high" style = "font-size: 30px; font-weight: 500;">Senior High School</label>
                <div class="subcategory" id="senior-high-sub">
                    <label><input type="checkbox"> SHS Uniform</label><br>
                    <label><input type="checkbox"> Senior High School PE</label><br>
                </div>
            </div>

            <div class="category">
                <input type="checkbox" id="accessories" class="category-checkbox">
                <label for="accessories" style = "font-size: 30px; font-weight: 500;">Accessories</label>
                <div class="subcategory" id="accessories-sub">
                    <label><input type="checkbox"> Pins</label><br>
                    <label><input type="checkbox"> Lace</label><br>
                    <label><input type="checkbox"> Aquaflasks</label><br>
                </div>
            </div>

            <div class="category">
                <input type="checkbox" id="shirts" class="category-checkbox">
                <label for="shirts" style = "font-size: 30px; font-weight: 500;">Shirts</label>
                <div class="subcategory" id="shirts-sub">
                    <label><input type="checkbox"> Anniversary Shirts</label><br>
                    <label><input type="checkbox"> Proware Shirts</label><br>
                    <label><input type="checkbox"> Org Shirts</label><br>
                </div>
            </div>
        </aside>

        <main class="content">
            <div class="products-grid">
                <?php
                $conn = mysqli_connect("localhost", "root", "", "proware");
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                $sql = "SELECT * FROM inventory ORDER BY created_at DESC";
                $result = mysqli_query($conn, $sql);

                $products = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $itemCode = $row['item_code'];
                    $itemName = $row['item_name'];
                    $itemImage = '../uploads/itemlist/' . $row['image_path'];
                    $itemPrice = $row['price'];
                    $itemCategory = $row['category'];
                    $sizes = explode(',', $row['sizes']);
                    $baseItemCode = strtok($itemCode, '-');

                    if (!isset($products[$baseItemCode])) {
                        $products[$baseItemCode] = [
                            'name' => $itemName,
                            'image' => $itemImage,
                            'prices' => [$itemPrice],
                            'category' => $itemCategory,
                            'sizes' => $sizes,
                            'stock' => $row['actual_quantity'],
                            'variants' => [
                                [
                                    'item_code' => $itemCode,
                                    'size' => isset($sizes[0]) ? $sizes[0] : '',
                                    'price' => $itemPrice,
                                    'stock' => $row['actual_quantity']
                                ]
                            ]
                        ];
                    } else {
                        $products[$baseItemCode]['sizes'] = array_unique(array_merge($products[$baseItemCode]['sizes'], $sizes));
                        $products[$baseItemCode]['prices'][] = $itemPrice;
                        $products[$baseItemCode]['stock'] += $row['actual_quantity'];
                        $products[$baseItemCode]['variants'][] = [
                            'item_code' => $itemCode,
                            'size' => isset($sizes[0]) ? $sizes[0] : '',
                            'price' => $itemPrice,
                            'stock' => $row['actual_quantity']
                        ];
                    }
                }

                foreach ($products as $baseItemCode => $product):
                    $availableSizes = $product['sizes'];
                    $prices = $product['prices'];
                    
                    // Create stocksBySize array with item_code for each size
                    $stocksBySize = [];
                    $itemCodesBySize = [];
                    foreach ($product['variants'] as $variant) {
                        $size = $variant['size'];
                        $stocksBySize[$size] = $variant['stock'];
                        $itemCodesBySize[$size] = $variant['item_code'];
                    }
                    
                    ?>
                    <div class="product-container" 
                        data-sizes="<?php echo implode(',', $availableSizes); ?>"
                        data-prices="<?php echo implode(',', $prices); ?>" 
                        data-stocks="<?php echo implode(',', array_values($stocksBySize)); ?>"
                        data-item-codes="<?php echo implode(',', array_values($itemCodesBySize)); ?>"
                        data-stock="<?php echo $product['stock']; ?>"
                        data-category="<?php echo htmlspecialchars($product['category']); ?>"
                        data-item-code="<?php echo htmlspecialchars($baseItemCode); ?>"
                        data-item-name="<?php echo htmlspecialchars($product['name']); ?>">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <div class="product-overlay">
                            <div class="items"></div>
                            <div class="items head">
                                <p><?php echo $product['name']; ?></p>
                                <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <hr>
                            </div>
                            <div class="items price">
                                <p class="price-range">Price: ₱<?php echo number_format(min($prices), 2); ?> -
                                    ₱<?php echo number_format(max($prices), 2); ?></p>
                            </div>
                            <div class="items stock">
                                <p>Stock: <?php echo $product['stock']; ?></p>
                            </div>
                            <div class="items cart" onclick="handleAddToCart(this)" data-item-code="<?php echo htmlspecialchars($baseItemCode); ?>">
                                <i class="fa fa-shopping-cart"></i>
                                <span>ADD TO CART</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Size Selection Modal -->
    <div id="sizeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Select Size</h2>
            <div class="product-info">
                <img id="modalProductImage" src="" alt="Product Image">
                <div class="product-details">
                    <h3 id="modalProductName"></h3>
                    <p id="modalProductPrice" class="price-display">Price Range: --</p>
                    <p id="modalProductStock" class="stock-display">Stock: --</p>
                </div>
            </div>
            <div class="size-options">
            </div>
            <div class="quantity-selector">
                <label for="quantity">Quantity:</label>
                <div class="quantity-controls">
                    <button type="button" onclick="decrementQuantity()">-</button>
                    <input type="number" id="quantity" value="1" min="1">
                    <button type="button" onclick="incrementQuantity()">+</button>
                </div>
            </div>
            <button class="add-to-cart-btn" onclick="addToCartWithSize()">Add to Cart</button>
        </div>
    </div>

    <!-- Accessories Quantity Modal -->
    <div id="accessoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAccessoryModal()">&times;</span>
            <h2>Select Quantity</h2>
            <div class="product-info">
                <img id="accessoryModalImage" src="" alt="Product Image">
                <div class="product-details">
                    <h3 id="accessoryModalName"></h3>
                    <p id="accessoryModalPrice" class="price-display">Price: --</p>
                    <p id="accessoryModalStock" class="stock-display">Stock: --</p>
                </div>
            </div>
            <div class="quantity-selector">
                <label for="accessoryQuantity">Quantity:</label>
                <div class="quantity-controls">
                    <button type="button" onclick="decrementAccessoryQuantity()">-</button>
                    <input type="number" id="accessoryQuantity" value="1" min="1">
                    <button type="button" onclick="incrementAccessoryQuantity()">+</button>
                </div>
            </div>
            <button class="add-to-cart-btn" onclick="addAccessoryToCart()">Add to Cart</button>
        </div>
    </div>

    <?php include("../Includes/Footer.php"); ?>

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            position: relative;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
        }

        .product-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .product-info img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }

        .size-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .size-option {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 60px;
            text-align: center;
        }

        .size-option.available {
            opacity: 1;
            background-color: #fff;
            border-color: #007bff;
            color: #007bff;
        }

        .size-option.unavailable {
            opacity: 1;
            background-color: #f5f5f5;
            cursor: not-allowed;
            border-color: #ddd;
            color: #999;
        }

        .size-option.selected {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
            opacity: 1;
        }

        .size-option.available:hover {
            background-color: #e6f2ff;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quantity-controls button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background: #f0f0f0;
            cursor: pointer;
        }

        .quantity-controls input {
            width: 50px;
            text-align: center;
            padding: 5px;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart-btn:hover {
            background-color: #0056b3;
        }

        .add-to-cart-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .price-display, .stock-display {
            font-size: 1.1em;
            margin: 5px 0;
        }

        .price-display {
            color: #007bff;
            font-weight: bold;
        }

        .stock-display {
            color: #28a745;
        }

        .low-stock {
            color: #dc3545;
        }

        /* Add specific styles for accessory modal */
        #accessoryModal .quantity-selector {
            margin: 20px 0;
        }

        #accessoryModal .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        #accessoryModal .quantity-controls button {
            padding: 5px 15px;
            font-size: 18px;
            border: 1px solid #ddd;
            background: #f8f8f8;
            cursor: pointer;
            border-radius: 4px;
        }

        #accessoryModal .quantity-controls input {
            width: 60px;
            text-align: center;
            padding: 5px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #accessoryModal .add-to-cart-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        #accessoryModal .add-to-cart-btn:hover {
            background-color: #0056b3;
        }
    </style>

    <script src="../Javascript/ProItemList.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            offset: 100,
            once: true
        });
        document.addEventListener("DOMContentLoaded", function () {
            const categories = document.querySelectorAll(".category-checkbox");

            // Ensure all subcategories are hidden when page loads
            document.querySelectorAll(".subcategory").forEach(sub => {
                sub.style.display = "none";
            });

            categories.forEach(category => {
                category.addEventListener("change", function () {
                    let subcategoryDiv = document.getElementById(this.id + "-sub");
                    subcategoryDiv.style.display = this.checked ? "block" : "none";
                });
            });
        });

        // Modal functionality
        const modal = document.getElementById('sizeModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        let currentProduct = null;

        function handleAddToCart(element) {
            const productContainer = element.closest('.product-container');
            const category = productContainer.dataset.category;
            
            if (category && (category.toLowerCase().includes('accessories') || category.toLowerCase().includes('sti-accessories'))) {
                showAccessoryModal(element);
            } else {
                showSizeModal(element);
            }
        }

        function showAccessoryModal(element) {
            const productContainer = element.closest('.product-container');
            const price = productContainer.dataset.prices.split(',')[0];
            const stock = productContainer.dataset.stock;
            
            currentProduct = {
                itemCode: productContainer.dataset.itemCode,
                name: productContainer.dataset.itemName,
                price: price,
                stock: stock,
                image: productContainer.querySelector('img').src,
                category: productContainer.dataset.category
            };

            // Update modal content
            document.getElementById('accessoryModalImage').src = currentProduct.image;
            document.getElementById('accessoryModalName').textContent = currentProduct.name;
            document.getElementById('accessoryModalPrice').textContent = `Price: ₱${parseFloat(currentProduct.price).toFixed(2)}`;
            document.getElementById('accessoryModalStock').textContent = `Stock: ${currentProduct.stock}`;

            // Set max quantity
            document.getElementById('accessoryQuantity').max = currentProduct.stock;
            
            // Show the modal
            document.getElementById('accessoryModal').style.display = 'block';
        }

        function closeAccessoryModal() {
            document.getElementById('accessoryModal').style.display = 'none';
            document.getElementById('accessoryQuantity').value = 1;
        }

        function incrementAccessoryQuantity() {
            const input = document.getElementById('accessoryQuantity');
            const max = parseInt(input.max);
            const currentValue = parseInt(input.value);
            if (currentValue < max) {
                input.value = currentValue + 1;
            }
        }

        function decrementAccessoryQuantity() {
            const input = document.getElementById('accessoryQuantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function addAccessoryToCart() {
            const quantity = document.getElementById('accessoryQuantity').value;
            
            addToCart(null, {
                itemCode: currentProduct.itemCode,
                quantity: quantity,
                size: 'One Size'
            });

            closeAccessoryModal();
        }

        function showSizeModal(element) {
            const productContainer = element.closest('.product-container');
            const category = productContainer.dataset.category;
            
            currentProduct = {
                itemCode: productContainer.dataset.itemCode,
                name: productContainer.dataset.itemName,
                sizes: productContainer.dataset.sizes.split(','),
                prices: productContainer.dataset.prices.split(','),
                stocks: productContainer.dataset.stocks.split(','),
                itemCodes: productContainer.dataset.itemCodes ? productContainer.dataset.itemCodes.split(',') : [],
                image: productContainer.querySelector('img').src,
                category: category
            };

            // Update modal content
            document.getElementById('modalProductImage').src = currentProduct.image;
            document.getElementById('modalProductName').textContent = currentProduct.name;
            document.getElementById('modalProductPrice').textContent = `Price Range: ₱${Math.min(...currentProduct.prices.map(Number)).toFixed(2)} - ₱${Math.max(...currentProduct.prices.map(Number)).toFixed(2)}`;
            document.getElementById('modalProductStock').textContent = `Total Stock: ${currentProduct.stock}`;

            // Generate size options
            const sizeOptionsContainer = document.querySelector('.size-options');
            sizeOptionsContainer.innerHTML = '';
            
            currentProduct.sizes.forEach((size, index) => {
                const sizeBtn = document.createElement('div');
                sizeBtn.className = 'size-option';
                sizeBtn.textContent = size;
                
                // Add stock and individual item code as data attributes
                const stock = currentProduct.stocks[index] || 0;
                const itemCode = currentProduct.itemCodes[index] || currentProduct.itemCode;
                
                sizeBtn.dataset.stock = stock;
                sizeBtn.dataset.itemCode = itemCode;
                sizeBtn.dataset.price = currentProduct.prices[index];
                
                // Add available class if stock > 0
                if (parseInt(stock) > 0) {
                    sizeBtn.classList.add('available');
                } else {
                    sizeBtn.classList.add('unavailable');
                }
                
                sizeBtn.onclick = () => selectSize(sizeBtn);
                sizeOptionsContainer.appendChild(sizeBtn);
            });

            document.getElementById('sizeModal').style.display = 'block';
        }

        function selectSize(element) {
            // Only allow selection if size is available
            if (element.classList.contains('unavailable')) {
                return;
            }
            
            document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('selected'));
            element.classList.add('selected');
            
            // Update stock display for the selected size
            const stock = element.dataset.stock;
            const price = element.dataset.price;
            
            document.getElementById('modalProductStock').textContent = `Stock: ${stock}`;
            document.getElementById('modalProductPrice').textContent = `Price: ₱${parseFloat(price).toFixed(2)}`;
            
            // Update max quantity
            document.getElementById('quantity').max = stock;
            document.getElementById('quantity').value = 1;
        }

        function incrementQuantity() {
            const input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function addToCartWithSize() {
            const selectedSize = document.querySelector('.size-option.selected');
            if (!selectedSize && !currentProduct.category?.toLowerCase().includes('accessories')) {
                alert('Please select a size');
                return;
            }

            const quantity = document.getElementById('quantity').value;
            const size = currentProduct.category?.toLowerCase().includes('accessories') ? 'One Size' : selectedSize.textContent;
            
            // Get the specific item code for the selected size
            const itemCode = selectedSize ? selectedSize.dataset.itemCode : currentProduct.itemCode;

            // Add to cart with size and quantity
            addToCart(null, {
                itemCode: itemCode,
                size: size,
                quantity: quantity
            });

            // Close modal
            modal.style.display = 'none';
            // Reset quantity
            document.getElementById('quantity').value = 1;
        }

        // Add event listeners for the close buttons
        document.querySelectorAll('.modal .close').forEach(closeBtn => {
            closeBtn.onclick = function() {
                this.closest('.modal').style.display = 'none';
            }
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>

</html>