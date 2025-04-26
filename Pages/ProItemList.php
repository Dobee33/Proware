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

    <!-- Add this right after the header section -->
    

    <div class="container">
        <aside class="sidebar" id="sidebar" data-aos="fade-right">
            <span class="close-sidebar" id="closeSidebar">&times;</span>
            <div class="filter-header">
                <span class="filter-label">FILTER</span>
                <span class="category-title">Product categories</span>
            </div>

            <div class="category-list">
                <!-- Tertiary Uniform -->
                <div class="category-item">
                    <div class="main-category-header" data-category="tertiary-uniform">
                        <span>Tertiary Uniform</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="subcategories">
                        <!-- BSCM -->
                        <div class="course-category">
                            <div class="course-header">
                                <span>BSCM</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="course-items hidden">
                                <label><input type="checkbox" value="bscm-blazer"> Blazer</label>
                                <label><input type="checkbox" value="bscm-long-sleeve"> Long Sleeve Uniform</label>
                                <label><input type="checkbox" value="bscm-pants"> Pants</label>
                                <label><input type="checkbox" value="bscm-skirt"> Skirt</label>
                            </div>
                        </div>

                        <!-- BSTM -->
                        <div class="course-category">
                            <div class="course-header">
                                <span>BSTM</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="course-items hidden">
                                <label><input type="checkbox" value="bstm-baret"> Baret</label>
                                <label><input type="checkbox" value="bstm-white-blouse"> White Blouse</label>
                                <label><input type="checkbox" value="bstm-yellow-scarf"> Yellow Scarf</label>
                                <label><input type="checkbox" value="bstm-tourism-blazer"> Tourism Blazer</label>
                                <label><input type="checkbox" value="bstm-gray-skirt"> Gray Skirt</label>
                                <label><input type="checkbox" value="bstm-white-polo"> White Polo</label>
                                <label><input type="checkbox" value="bstm-yellow-necktie"> Yellow Necktie</label>
                                <label><input type="checkbox" value="bstm-gray-pants"> Gray Pants</label>
                            </div>
                        </div>

                        <!-- BSIT/CPE/CS -->
                        <div class="course-category">
                            <div class="course-header">
                                <span>BSIT/CPE/CS</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="course-items hidden">
                                <label><input type="checkbox" value="bsit-pants"> Pants</label>
                                <label><input type="checkbox" value="bsit-skirt"> Skirt</label>
                                <label><input type="checkbox" value="bsit-gray-polo"> Gray polo with 3/4 Sleeve</label>
                                <label><input type="checkbox" value="bsit-gray-blouse"> Gray blouse 3/4 sleeve</label>
                            </div>
                        </div>

                        <!-- BSBA -->
                        <div class="course-category">
                            <div class="course-header">
                                <span>BSBA</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="course-items hidden">
                                <label><input type="checkbox" value="bsba-red-scarf"> Red Scarf</label>
                                <label><input type="checkbox" value="bsba-blue-blouse"> Blue long sleeve blouse</label>
                                <label><input type="checkbox" value="bsba-blue-polo"> Blue long sleeve polo</label>
                                <label><input type="checkbox" value="bsba-blazer"> Blazer</label>
                                <label><input type="checkbox" value="bsba-red-necktie"> Red necktie</label>
                                <label><input type="checkbox" value="bsba-pants"> Pants</label>
                                <label><input type="checkbox" value="bsba-skirt"> Skirt</label>
                            </div>
                        </div>

                        <!-- BMMA -->
                        <div class="course-category">
                            <div class="course-header">
                                <span>BMMA</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="course-items hidden">
                                <label><input type="checkbox" value="bmma-blue-scarf"> Blue Scarf</label>
                                <label><input type="checkbox" value="bmma-blue-necktie"> Blue necktie</label>
                                <label><input type="checkbox" value="bmma-blue-blouse"> Blue long sleeve blouse</label>
                                <label><input type="checkbox" value="bmma-blue-polo"> Blue long sleeve polo</label>
                                <label><input type="checkbox" value="bmma-blazer"> Blazer</label>
                                <label><input type="checkbox" value="bmma-skirt"> Skirt</label>
                                <label><input type="checkbox" value="bmma-pants"> Pants</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SHS Uniform -->
                <div class="category-item">
                    <div class="main-category-header" data-category="shs-uniform">
                        <span>SHS Uniform</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="subcategories">
                        <div class="course-items hidden">
                            <label><input type="checkbox" value="shs-white-polo"> White Polo</label>
                            <label><input type="checkbox" value="shs-gray-vest"> Gray Vest</label>
                            <label><input type="checkbox" value="shs-necktie"> Necktie</label>
                            <label><input type="checkbox" value="shs-dark-blue-pants"> Dark blue pants</label>
                            <label><input type="checkbox" value="shs-white-blouse"> White Blouse</label>
                            <label><input type="checkbox" value="shs-dark-blue-skirt"> Dark blue pencil-cut skirt</label>
                        </div>
                    </div>
                </div>

                <!-- STI Accessories -->
                <div class="category-item">
                    <div class="main-category-header" data-category="sti-accessories">
                        <span>STI Accessories</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="subcategories">
                        <div class="course-items hidden">
                            <!-- Add accessories items here -->
                        </div>
                    </div>
                </div>

                <!-- STI Shirt -->
                <div class="category-item">
                    <div class="main-category-header" data-category="sti-shirt">
                        <span>STI Shirt</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="subcategories">
                        <div class="course-items hidden">
                            <!-- Add STI shirt items here -->
                        </div>
                    </div>
                </div>

                <!-- SHS PE -->
                <div class="category-item">
                    <div class="main-category-header" data-category="shs-pe">
                        <span>SHS PE</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="subcategories">
                        <div class="course-items hidden">
                            <!-- Add SHS PE items here -->
                        </div>
                    </div>
                </div>

                <!-- Tertiary PE -->
                <div class="category-item">
                    <div class="main-category-header" data-category="tertiary-pe">
                        <span>Tertiary PE</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="subcategories">
                        <div class="course-items hidden">
                            <!-- Add Tertiary PE items here -->
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <button class="filter-toggle" id="filterToggle">
        <i class="fas fa-filter"></i>
        <span>Filter</span>
    </button>

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
                    // Handle both full paths and filenames
                    $imagePath = $row['image_path'];
                    if (strpos($imagePath, 'uploads/') === false) {
                        // If it's just a filename, construct the full path
                        $itemImage = '../uploads/itemlist/' . $imagePath;
                    } else {
                        // If it's already a full path, just add the parent directory reference
                        $itemImage = '../' . $imagePath;
                    }
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
                ?>
                
                <?php
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
                        data-category="<?php echo strtolower(str_replace(' ', '-', $product['category'])); ?>"
                        data-sizes="<?php echo implode(',', $availableSizes); ?>"
                        data-prices="<?php echo implode(',', $prices); ?>" 
                        data-stocks="<?php echo implode(',', array_values($stocksBySize)); ?>"
                        data-item-codes="<?php echo implode(',', array_values($itemCodesBySize)); ?>"
                        data-stock="<?php echo $product['stock']; ?>"
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
                
                <!-- No results message -->
                <div id="no-results-message" class="no-results-message" style="display: none;">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your search terms or filters</p>
                </div>
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

    <script src="../Javascript/ProItemList.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterToggle = document.getElementById('filterToggle');
        const sidebar = document.getElementById('sidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        // Toggle sidebar on filter button click
        filterToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when close button is clicked
        closeSidebar.addEventListener('click', function() {
            sidebar.classList.remove('active');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !filterToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Prevent clicks inside sidebar from closing it
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Close sidebar on window resize if screen becomes large enough
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('active');
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get modal elements
        const modal = document.getElementById('sizeModal');
        const closeBtn = modal.querySelector('.close');
        
        // Function to open modal
        function openModal() {
            modal.classList.add('show');
        }
        
        // Function to close modal
        function closeModal() {
            modal.classList.remove('show');
        }
        
        // Add click event to all "ADD TO CART" buttons
        document.querySelectorAll('.cart').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                openModal();
            });
        });
        
        // Close modal when clicking the close button
        closeBtn.addEventListener('click', closeModal);
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        // Prevent modal from closing when clicking inside modal content
        modal.querySelector('.modal-content').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    </script>
</body>

</html>