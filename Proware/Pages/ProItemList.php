<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/ProItemList.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>PAMO - Product List</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/ProItemList.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton+SC&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                <h3>Department</h3>
                <label class="checkbox-container">
                    <input type="checkbox" data-department="bsit"> Information Technology
                    <span class="checkmark"></span>
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" data-department="bstm"> Tourism Management
                    <span class="checkmark"></span>
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" data-department="bscs"> Computer Science
                    <span class="checkmark"></span>
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" data-department="culinary"> Culinary Arts
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="filter-group">
                <h3>Category</h3>
                <label class="checkbox-container">
                    <input type="checkbox" data-category="uniform"> Uniforms
                    <span class="checkmark"></span>
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" data-category="shirt"> Department Shirts
                    <span class="checkmark"></span>
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" data-category="accessories"> Accessories
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="filter-group">
                <h3>Price Range</h3>
                <div class="price-range">
                    <input type="number" id="min-price" placeholder="Min" min="0">
                    <span>-</span>
                    <input type="number" id="max-price" placeholder="Max" min="0">
                </div>
            </div>

            <div class="filter-group">
                <h3>Size</h3>
                <div class="size-options">
                    <label class="size-btn">
                        <input type="checkbox" name="size" value="XS">
                        <span>XS</span>
                    </label>
                    <label class="size-btn">
                        <input type="checkbox" name="size" value="S">
                        <span>S</span>
                    </label>
                    <label class="size-btn">
                        <input type="checkbox" name="size" value="M">
                        <span>M</span>
                    </label>
                    <label class="size-btn">
                        <input type="checkbox" name="size" value="L">
                        <span>L</span>
                    </label>
                    <label class="size-btn">
                        <input type="checkbox" name="size" value="XL">
                        <span>XL</span>
                    </label>
                </div>
            </div>

            <div class="filter-group">
                <h3>Availability</h3>
                <label class="checkbox-container">
                    <input type="checkbox" data-stock="in-stock"> In Stock
                    <span class="checkmark"></span>
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" data-stock="pre-order"> Pre-order
                    <span class="checkmark"></span>
                </label>
            </div>

            <button class="apply-btn">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </aside>

        <main class="content">
            <div class="sort-container" data-aos="fade-down">
                <div class="results-count">
                    <span id="product-count">10</span> Products Found
                </div>
                <select id="sort-select" class="modern-select">
                    <option value="default">Sort by</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="name-asc">Name: A to Z</option>
                    <option value="name-desc">Name: Z to A</option>
                </select>
            </div>

            <div class="products-grid">
                <!-- Product 1 - IT Department Shirt -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="100"
                     data-department="bsit" 
                     data-category="shirt" 
                     data-price="350" 
                     data-size="M,L,XL"
                     data-stock="in-stock">
                    <div class="product-badge">New</div>
                    <div class="product-image">
                        <img src="../Images/STI-ICT.jpg" alt="IT Department Shirt">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSIT Collection</div>
                        <h3>IT Department Shirt</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSIT</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> M, L, XL</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱350.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (50)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="50">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 2 - Tourism Management Uniform -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="200"
                     data-department="bstm" 
                     data-category="uniform" 
                     data-price="1500" 
                     data-size="S,M,L"
                     data-stock="pre-order">
                    <div class="product-badge">Pre-order</div>
                    <div class="product-image">
                        <img src="../Images/STI-TM.jpg" alt="Tourism Management Uniform">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSTM Collection</div>
                        <h3>Tourism Management Uniform Set</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSTM</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> S, M, L</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱1,500.00</span>
                            <span class="stock-status pre-order">
                                <i class="fas fa-clock"></i> Pre-order
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="10">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="pre-order-btn" onclick="window.location.href='preorder.php'" data-status="pre-order">
                                <i class="fas fa-clipboard-list"></i>
                                Pre-order
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 3 - Engineering Department Shirt -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="300"
                     data-department="engineering" 
                     data-category="shirt" 
                     data-price="380" 
                     data-size="S,M,L,XL"
                     data-stock="in-stock">
                    <div class="product-image">
                        <img src="../Images/STI-BA.jpg" alt="Engineering Department Shirt">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSBA Collection</div>
                        <h3>Business Administration Department Shirt</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSBA</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> S, M, L, XL</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱380.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (30)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="30">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Product 4 - CS Department Shirt -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="400"
                     data-department="bscs" 
                     data-category="shirt" 
                     data-price="350" 
                     data-size="S,M,L,XL"
                     data-stock="in-stock">
                    <div class="product-image">
                        <img src="../Images/STI-BA.jpg" alt="CS Department Shirt">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSCS Collection</div>
                        <h3>CS Department Shirt</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSCS</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> S, M, L, XL</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱350.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (25)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="25">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 5 - Tourism Management PE Uniform -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="500"
                     data-department="bstm" 
                     data-category="uniform" 
                     data-price="850" 
                     data-size="XS,S,M,L"
                     data-stock="in-stock">
                    <div class="product-badge">Bestseller</div>
                    <div class="product-image">
                        <img src="../Images/STI-MMA.jpg" alt="Tourism PE Uniform">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSTM Collection</div>
                        <h3>Tourism PE Uniform</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSTM</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> XS, S, M, L</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱850.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (40)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="40">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 6 - Engineering Lab Coat -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="600"
                     data-department="engineering" 
                     data-category="uniform" 
                     data-price="650" 
                     data-size="S,M,L,XL"
                     data-stock="pre-order">
                    <div class="product-badge">Essential</div>
                    <div class="product-image">
                        <img src="../Images/STI-SH.jpg" alt="Engineering Lab Coat">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">Engineering Collection</div>
                        <h3>Engineering Lab Coat</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> Engineering</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> S, M, L, XL</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱650.00</span>
                            <span class="stock-status pre-order">
                                <i class="fas fa-clock"></i> Pre-order (15 days)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="20">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Pre-order
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 7 - IT Department Varsity Jacket -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="700"
                     data-department="bsit" 
                     data-category="accessories" 
                     data-price="1200" 
                     data-size="S,M,L,XL"
                     data-stock="in-stock">
                    <div class="product-badge">Limited Edition</div>
                    <div class="product-image">
                        <img src="../Images/STI-ICT.jpg" alt="IT Varsity Jacket">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSIT Collection</div>
                        <h3>IT Varsity Jacket</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSIT</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> S, M, L, XL</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱1,200.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (15)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="15">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 8 - CS Department Hoodie -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="800"
                     data-department="bscs" 
                     data-category="accessories" 
                     data-price="980" 
                     data-size="M,L,XL"
                     data-stock="in-stock">
                    <div class="product-image">
                        <img src="../Images/STI-ICT.jpg" alt="CS Hoodie">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSCS Collection</div>
                        <h3>CS Department Hoodie</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSCS</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> M, L, XL</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱980.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (20)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="20">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 9 - Tourism Management ID Lace -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="900"
                     data-department="bstm" 
                     data-category="accessories" 
                     data-price="150" 
                     data-size="ONE SIZE"
                     data-stock="in-stock">
                    <div class="product-badge">Essential</div>
                    <div class="product-image">
                        <img src="../Images/STI-TM.jpg" alt="Tourism ID Lace">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">BSTM Collection</div>
                        <h3>Tourism ID Lace</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> BSTM</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> One Size</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱150.00</span>
                            <span class="stock-status in-stock">
                                <i class="fas fa-check-circle"></i> In Stock (100)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="100">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 10 - Engineering Calculator -->
                <div class="product-card" data-aos="fade-up" data-aos-delay="1000"
                     data-department="engineering" 
                     data-category="accessories" 
                     data-price="2500" 
                     data-size="ONE SIZE"
                     data-stock="pre-order">
                    <div class="product-badge">Required</div>
                    <div class="product-image">
                        <img src="../Images/STI-BA.jpg" alt="Engineering Calculator">
                        <div class="product-overlay">
                            <button class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </button>
                            <button class="wishlist-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-category">Engineering Collection</div>
                        <h3>Engineering Calculator</h3>
                        <div class="product-meta">
                            <span class="department"><i class="fas fa-graduation-cap"></i> Engineering</span>
                            <span class="sizes"><i class="fas fa-tshirt"></i> One Size</span>
                        </div>
                        <div class="product-price">
                            <span class="price">₱2,500.00</span>
                            <span class="stock-status pre-order">
                                <i class="fas fa-clock"></i> Pre-order (7 days)
                            </span>
                        </div>
                        <div class="product-actions">
                            <div class="quantity">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" min="1" max="10">
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Pre-order
                            </button>
                        </div>
                    </div>
                </div>
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