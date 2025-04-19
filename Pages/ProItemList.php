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
                    <input type="number" id="quantity" placeholder="Enter quantity" min="1">
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
                    <input type="number" id="accessoryQuantity" placeholder="Enter quantity" min="1">
                    <button type="button" onclick="incrementAccessoryQuantity()">+</button>
                </div>
            </div>
            <button class="add-to-cart-btn" onclick="addAccessoryToCart()">Add to Cart</button>
        </div>
    </div>

    <?php include("../Includes/Footer.php"); ?>

    <script src="../Javascript/ProItemList.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</body>

</html>