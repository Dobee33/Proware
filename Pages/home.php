<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="../CSS/ProHome.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>STI College Lucena - Campus Store</title>
</head>

<body>
    <?php
    include("../Includes/Header.php");
    ?>
    
    <!-- Hero Section -->
    <section class="Hero">
        <div class="hero-slideshow">
            <div class="hero-slide"
                style="background-image:url('../Images/ACS ALL.jpg')">
            </div>
            <div class="hero-slide"
                style="background-image:url('../Images/college1.jpg')">
            </div>
            <div class="hero-slide"
                style="background-image:url('../Images/SHS2.jpg')">
            </div>
            <div class="hero-slide"
                style="background-image:url('../Images/SHS_cover_photo.jpg')">
            </div>
            <div class="hero-slide"
                style="background-image:url('../Images/college2.jpg')">
            </div>
        </div>
        <div class="hero-content">
            <h1>GEAR UP</h1>
            <p>Your one-stop shop for all STI College Lucena essentials</p>
            <a href="ProItemList.php"><button class="shop-now-button">Shop Now</button></a>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="New-Arrivals">
        <div class="section-header">
            <h2>New Arrivals</h2>
            <p class="section-subtitle">Check out our latest products</p>
        </div>
        
        <div class="new-arrivals-container">
            <!-- New Arrival 1 -->
            <div class="new-arrival-card" data-aos="fade-up">
                <div class="new-arrival-image">
                    <img src="../Images/new1.png" alt="STI GRIT Collection" draggable="false" />
                    <div class="new-arrival-overlay">
                    </div>
                </div>
            </div>
            
            <!-- New Arrival 2 -->
            <div class="new-arrival-card" data-aos="fade-up" data-aos-delay="100">
                <div class="new-arrival-image">
                    <img src="../Images/new3.png" alt="ICT Program Uniform" draggable="false" />
                    <div class="new-arrival-overlay">
                    </div>
                </div>
            </div>
            
            <!-- New Arrival 3 -->
            <div class="new-arrival-card" data-aos="fade-up" data-aos-delay="200">
                <div class="new-arrival-image">
                    <img src="../Images/new2.png" alt="Hospitality Management Uniform" draggable="false" />
                    <div class="new-arrival-overlay">
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <!-- Tagline Section -->
    <section class="tagline">
        <!-- Best Sellers Section -->
      <section class="Best-Sellers">
        <div class="section-header">
            <h2>Best Sellers</h2>
        </div>
        <div class="best-sellers-grid">
            <div class="best-seller-card" data-aos="fade-up">
                <div class="best-seller-badge">TOP SELLER</div>
                <div class="product-image">
                    <img src="../Images/STI-TM.jpg" alt="STI TM Uniform" draggable="false" />
                    <div class="product-overlay">
                        <button class="add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>STI TM Uniform</h3>
                    <p class="product-description">Professional uniform for Tourism Management students</p>
                    <p class="product-price">₱599.00</p>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span>(42)</span>
                    </div>
                </div>
                <div class="best-seller-stock">In Stock</div>
            </div>
            <div class="best-seller-card" data-aos="fade-up" data-aos-delay="100">
                <div class="best-seller-tag">NEW</div>
                <div class="product-image">
                    <img src="../Images/STI-SH.jpg" alt="STI Senior High Uniform" draggable="false" />
                    <div class="product-overlay">
                        <button class="add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>STI Senior High Uniform</h3>
                    <p class="product-description">Comfortable and stylish uniform for Senior High students</p>
                    <p class="product-price">₱599.00</p>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(28)</span>
                    </div>
                </div>
                <div class="best-seller-stock low">Low Stock</div>
            </div>
            <div class="best-seller-card" data-aos="fade-up" data-aos-delay="200">
                <div class="best-seller-badge">POPULAR</div>
                <div class="product-image">
                    <img src="../Images/STI-MMA.jpg" alt="STI MMA Uniform" draggable="false" />
                    <div class="product-overlay">
                        <button class="add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>STI MMA Uniform</h3>
                    <p class="product-description">Professional attire for Multimedia Arts students</p>
                    <p class="product-price">₱599.00</p>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <span>(56)</span>
                    </div>
                </div>
                <div class="best-seller-stock">In Stock</div>
            </div>
            <div class="best-seller-card" data-aos="fade-up" data-aos-delay="300">
                <div class="best-seller-discount">-15%</div>
                <div class="product-image">
                    <img src="../Images/STI-ICT.jpg" alt="STI ICT Uniform" draggable="false" />
                    <div class="product-overlay">
                        <button class="add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>STI ICT Uniform</h3>
                    <p class="product-description">Modern uniform for Information Technology students</p>
                    <p class="product-price">₱509.00</p>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(35)</span>
                    </div>
                </div>
                <div class="best-seller-stock">In Stock</div>
            </div>
            <div class="best-seller-card" data-aos="fade-up" data-aos-delay="400">
                <div class="best-seller-badge">TRENDING</div>
                <div class="product-image">
                    <img src="../Images/STI-HM.jpg" alt="STI HM Uniform" draggable="false" />
                    <div class="product-overlay">
                        <button class="add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3>STI HM Uniform</h3>
                    <p class="product-description">Elegant uniform for Hospitality Management students</p>
                    <p class="product-price">₱599.00</p>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span>(48)</span>
                    </div>
                </div>
                <div class="best-seller-stock out">Out of Stock</div>
            </div>
        </div>
    </section>
        <div class="tag">
            <h1>Be future-ready. Be STI.</h1>
            <p>Explore our wide range of products and check stock availability right from your device.</p>
        </div>
 
    </section>
 <!-- Featured Products Section -->
 <section class="Featured-Products">
    <div class="section-header">
        <h2>Featured Products</h2>
        <p class="section-subtitle">Discover our most popular items</p>
    </div>
    
    <div class="featured-carousel-container">
        <div class="featured-carousel">
            <div class="featured-carousel-track">
                <!-- Product 1 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS ARTS SCIENCE.jpg" alt="STI TM Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>Arts and Science Pin</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>(42)</span>
                            </div>
                            <p class="product-price">₱000.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS BMMA.jpg" alt="STI Senior High Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>BMMA PIN</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span>(28)</span>
                            </div>
                            <p class="product-price"><span class="original-price">₱000.00</span> ₱000.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS CM.jpg" alt="STI MMA Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>Culinary Pin</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span>(56)</span>
                            </div>
                            <p class="product-price">₱000.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS IT.jpg" alt="STI ICT Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>ICT Pin</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span>(35)</span>
                            </div>
                            <p class="product-price">₱599.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 5 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge trending">Trending</div>
                        <div class="product-image">
                            <img src="../Images/ACS TM.jpg" alt="STI HM Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>STI HM Uniform</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>(48)</span>
                            </div>
                            <p class="product-price">₱599.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Duplicate items for seamless scrolling -->
              <!-- Product 1 -->
              <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS ARTS SCIENCE.jpg" alt="STI TM Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>Arts and Science Pin</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>(42)</span>
                            </div>
                            <p class="product-price">₱000.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS BMMA.jpg" alt="STI Senior High Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>BMMA PIN</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span>(28)</span>
                            </div>
                            <p class="product-price"><span class="original-price">₱000.00</span> ₱000.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS CM.jpg" alt="STI MMA Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>Culinary Pin</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span>(56)</span>
                            </div>
                            <p class="product-price">₱000.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge">Accessories</div>
                        <div class="product-image">
                            <img src="../Images/ACS IT.jpg" alt="STI ICT Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>ICT Pin</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span>(35)</span>
                            </div>
                            <p class="product-price">₱599.00</p>
                        </div>
                    </div>
                </div>
                
                <!-- Product 5 -->
                <div class="featured-carousel-item">
                    <div class="product-card">
                        <div class="product-badge trending">Trending</div>
                        <div class="product-image">
                            <img src="../Images/ACS TM.jpg" alt="STI HM Uniform" draggable="false" />
                            <div class="product-overlay">
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3>STI HM Uniform</h3>
                            <div class="product-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>(48)</span>
                            </div>
                            <p class="product-price">₱000.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Display Section -->
    <section class="Display">
        <div class="container">
            <div class="section-header">
                <h2>Welcome to STI</h2>
                <p>Discover our latest collection</p>
            </div>
            
            <div class="sti-frames">
                <div id="letter-s" class="frame"></div>
                <div id="letter-s1" class="frame"></div>
                <div id="letter-s2" class="frame"></div>
                <div id="letter-s3" class="frame"></div>
            </div>
            
            <div class="display-content">
                <div class="category-main" data-aos="fade-right">
                    <div class="category-image">
                        <img src="../Images/uniform.png" alt="Uniforms" draggable="false" />
                    </div>
                    <div class="category-content">
                        <h3>Uniforms</h3>
                        <p>Discover our range of tech accessories for your daily needs</p>
                        <a href="ProItemList.php" class="category-link">View Uniforms<i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="category-grid">
                    <div class="category-item" data-aos="fade-up">
                        <div class="category-image">
                            <img src="../Images/STI GRIT SIT.png" alt="Stationery" draggable="false" />
                        </div>
                        <div class="category-content">
                            <h3>Anniversary Shirts</h3>
                            <a href="ProItemList.php" class="category-link">View Anniversary Shirts</a>
                        </div>
                    </div>
                    <div class="category-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="category-image">
                            <img src="../Images/ACS TM.jpg" alt="STI Merchandise" draggable="false" />
                        </div>
                        <div class="category-content">
                            <h3>Merchandise</h3>
                            <a href="ProItemList.php" class="category-link">View Merchandise</a>
                        </div>
                    </div>
                    <div class="category-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="category-image">
                            <img src="../Images/pen.jpg" alt="Books" draggable="false" />
                        </div>
                        <div class="category-content">
                            <h3>School Supplies</h3>
                            <a href="ProItemList.php" class="category-link">View School Supplies</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    include("../Includes/footer.php");
    ?>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script>
        // Hero background slideshow
        let heroSlideIndex = 0;
        const heroSlides = document.querySelectorAll('.hero-slide');

        function showHeroSlides() {
            heroSlides.forEach(slide => slide.classList.remove('active'));
            heroSlides[heroSlideIndex].classList.add('active');
            heroSlideIndex = (heroSlideIndex + 1) % heroSlides.length;
        }

        // Initialize first slide
        heroSlides[0].classList.add('active');
        // Change slide every 5 seconds
        setInterval(showHeroSlides, 5000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.querySelector('.featured-carousel-track');
            const slides = Array.from(track.children);
            const nextButton = document.querySelector('.next-btn');
            const prevButton = document.querySelector('.prev-btn');
            const dotsNav = document.querySelector('.carousel-dots');
            let currentIndex = 0;
            
            // Create dots
            slides.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.classList.add('carousel-dot');
                if (index === 0) dot.classList.add('active');
                dotsNav.appendChild(dot);
            });
            
            const dots = Array.from(dotsNav.children);
            
            // Show first slide
            slides[0].classList.add('active');
            
            // Update dots and slides
            const updateCarousel = (currentSlide, targetSlide, targetIndex) => {
                // Update slides
                currentSlide.classList.remove('active');
                targetSlide.classList.add('active');
                
                // Update dots
                const currentDot = dotsNav.querySelector('.active');
                currentDot.classList.remove('active');
                dots[targetIndex].classList.add('active');
                
                currentIndex = targetIndex;
            };
            
            // Next button click
            nextButton.addEventListener('click', () => {
                const currentSlide = track.querySelector('.active');
                const nextIndex = (currentIndex + 1) % slides.length;
                const nextSlide = slides[nextIndex];
                updateCarousel(currentSlide, nextSlide, nextIndex);
            });
            
            // Previous button click
            prevButton.addEventListener('click', () => {
                const currentSlide = track.querySelector('.active');
                const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
                const prevSlide = slides[prevIndex];
                updateCarousel(currentSlide, prevSlide, prevIndex);
            });
            
            // Dot click
            dotsNav.addEventListener('click', e => {
                const targetDot = e.target.closest('button');
                if (!targetDot) return;
                
                const currentSlide = track.querySelector('.active');
                const targetIndex = dots.findIndex(dot => dot === targetDot);
                const targetSlide = slides[targetIndex];
                
                updateCarousel(currentSlide, targetSlide, targetIndex);
            });
            
            // Auto advance
            setInterval(() => {
                const currentSlide = track.querySelector('.active');
                const nextIndex = (currentIndex + 1) % slides.length;
                const nextSlide = slides[nextIndex];
                updateCarousel(currentSlide, nextSlide, nextIndex);
            }, 3000); // Change slide every 3 seconds
        });
    </script>
    <script>
        const hamburger = document.querySelector(".hamburger");
        const navLinks = document.querySelector(".nav-links");
        const dropdowns = document.querySelectorAll(".dropdown");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navLinks.classList.toggle("active");
        });

        dropdowns.forEach(dropdown => {
            dropdown.addEventListener("click", () => {
                dropdown.classList.toggle("active");
            });
        });

        // Close menu when clicking a link
        document.querySelectorAll(".nav-links a").forEach(n => n.addEventListener("click", () => {
            hamburger.classList.remove("active");
            navLinks.classList.remove("active");
        }));
    </script>
</body>

</html>