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
            <!-- Additional best seller cards here -->
            <!-- ... Rest of the best seller cards ... -->
        </div>
    </section>
    
    <!-- Tagline Section -->
    <section class="tagline">
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
                    <!-- Featured product items here -->
                    <!-- ... Rest of the featured products ... -->
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
                <!-- Display content here -->
                <!-- ... Rest of the display content ... -->
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
        // Featured carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.querySelector('.featured-carousel-track');
            if (!track) return;

            let isDown = false;
            let startX;
            let scrollLeft;

            track.addEventListener('mousedown', (e) => {
                isDown = true;
                track.classList.add('active');
                startX = e.pageX - track.offsetLeft;
                scrollLeft = track.scrollLeft;
            });

            track.addEventListener('mouseleave', () => {
                isDown = false;
                track.classList.remove('active');
            });

            track.addEventListener('mouseup', () => {
                isDown = false;
                track.classList.remove('active');
            });

            track.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 2;
                track.scrollLeft = scrollLeft - walk;
            });
        });
    </script>
</body>

</html> 