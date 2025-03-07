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
    <title>PAMO - Product List</title>
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
                <label for="tertiary"><strong>Tertiary</strong></label>
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
                <label for="senior-high"><strong>Senior High School</strong></label>
                <div class="subcategory" id="senior-high-sub">
                    <label><input type="checkbox"> SHS Uniform</label><br>
                    <label><input type="checkbox"> Senior High School PE</label><br>
                </div>
            </div>

            <div class="category">
                <input type="checkbox" id="accessories" class="category-checkbox">
                <label for="accessories"><strong>Accessories</strong></label>
                <div class="subcategory" id="accessories-sub">
                    <label><input type="checkbox"> Pins</label><br>
                    <label><input type="checkbox"> Lace</label><br>
                    <label><input type="checkbox"> Aquaflasks</label><br>
                </div>
            </div>

            <div class="category">
                <input type="checkbox" id="shirts" class="category-checkbox">
                <label for="shirts"><strong>Shirts</strong></label>
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
                            'stock' => $row['actual_quantity']
                        ];
                    } else {
                        $products[$baseItemCode]['sizes'] = array_unique(array_merge($products[$baseItemCode]['sizes'], $sizes));
                        $products[$baseItemCode]['prices'][] = $itemPrice;
                        $products[$baseItemCode]['stock'] += $row['actual_quantity'];
                    }
                }

                foreach ($products as $product):
                    $availableSizes = $product['sizes'];
                    $prices = $product['prices'];
                    ?>
                    <div class="product-container" data-sizes="<?php echo implode(',', $availableSizes); ?>"
                        data-prices="<?php echo implode(',', $prices); ?>" data-stock="<?php echo $product['stock']; ?>">
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
                            <div class="items sizes">
                                <span>Sizes:</span>
                                <div class="size-options">
                                    <?php
                                    $allSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL', '6XL', '7XL'];
                                    foreach ($allSizes as $size):
                                        $isAvailable = in_array($size, $availableSizes);
                                        ?>
                                        <button class="size-btn" style="opacity: <?php echo $isAvailable ? '1' : '0.5'; ?>;">
                                            <?php echo $size; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                            <div class="items stock">
                                <p>Stock: <?php echo $product['stock']; ?></p>
                            </div>
                            <div class="items cart" onclick="toggleStockDisplay(this)">
                                <i class="fa fa-shopping-cart"></i>
                                <span>ADD TO CART</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <?php include("../Includes/Footer.php"); ?>

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

    </script>
</body>

</html>