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
                <!--     <label class="checkbox-container"> -->
                <!--         <input type="checkbox" data-category="uniform" class="main-category"> Uniform -->
                <!--         <span class="checkmark"></span> -->
                <!--     </label> -->
                <!--     <div id="uniform-courses" class="subcategory-group hidden"> -->
                <!--         <label class="checkbox-container"> -->
                <!--             <input type="checkbox" data-department="shs"> SHS -->
                <!--             <span class="checkmark"></span> -->
                <!--         </label> -->
                <!--         ... other subcategories ... -->
                <!--     </div> -->
                <!--     ... other main categories ... -->
                <!-- </div> -->

                <!-- <div class="filter-group size-filter hidden"> -->
                <!--     <h3>Sizes</h3> -->
                <!--     <div class="size-options"> -->
                <!--         <label class="size-btn"> -->
                <!--             <input type="radio" name="size" value="S"> -->
                <!--             <span>S</span> -->
                <!--         </label> -->
                <!--         ... other sizes ... -->
                <!--     </div> -->
                <!-- </div> -->
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
                            'sizes' => $sizes
                        ];
                    } else {
                        $products[$baseItemCode]['sizes'] = array_unique(array_merge($products[$baseItemCode]['sizes'], $sizes));
                        $products[$baseItemCode]['prices'][] = $itemPrice;
                    }
                }

                foreach ($products as $product):
                    $availableSizes = $product['sizes'];
                    $prices = $product['prices'];
                    ?>
                    <div class="product-container" data-sizes="<?php echo implode(',', $availableSizes); ?>"
                        data-prices="<?php echo implode(',', $prices); ?>">
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


    </script>
</body>

</html>