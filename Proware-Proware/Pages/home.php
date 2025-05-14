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
    <title>Homepage</title>
</head>

<body>
    <?php
    include("../Includes/Header.php");
    include("../Includes/connection.php");
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="welcome-message" id="welcomeMessage">
                    Welcome, <?php echo htmlspecialchars($_SESSION['last_name']); ?>
                    (<?php echo htmlspecialchars($_SESSION['role_category']); ?>)
                </div>
            <?php endif; ?>
            <h1>GEAR UP</h1>
            <p>Your one-stop shop for all STI College Lucena essentials</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="ProItemList.php"><button class="shop-now-button">Order Now</button></a>
            <?php else: ?>
                <a href="login.php?redirect=ProItemList.php"><button class="shop-now-button">Order Now</button></a>
            <?php endif; ?>
        </div>
    </section>

    <section class="tagline">
        <div class="tag">
            <h1>Be future-ready. Be STI.</h1>
            <p>Explore our wide range of products and check stock availability right from your device.</p>
        </div>
        <div class="sti-frames">
            <div id="letter-s" class="frame"></div>
            <div id="letter-s1" class="frame"></div>
            <div id="letter-s2" class="frame"></div>
            <div id="letter-s3" class="frame"></div>
        </div>
    </section>

    <!-- Display Section -->
    <section class="Display">
        <div class="container">
            <div class="section-header">
                <h2>Welcome to STI</h2>
                <p>Discover our latest collection</p>
            </div>
            <div class="display-content">
                <?php
                $sql = "SELECT image_path, title FROM homepage_content WHERE section='display'";
                $result = $conn->query($sql);
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="display-item" data-aos="fade-up">';
                    echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="New-Arrivals">
        <div class="section-header">
            <h2>Item Categories</h2>
            <p class="section-subtitle">Check out our latest products</p>
        </div>
        <div class="new-arrivals-container">
            <?php
            $sql = "SELECT image_path, title FROM homepage_content WHERE section='new_arrival'";
            $result = $conn->query($sql);
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="new-arrival-card" data-aos="fade-up">';
                echo '<div class="new-arrival-image">';
                echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['title']) . '" draggable="false" />';
                echo '<div class="new-arrival-overlay"></div>';
                echo '</div></div>';
            }
            ?>
        </div>
        <div class="new-arrivals-order-btn-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="ProItemList.php"><button class="shop-now-button">Order Now</button></a>
            <?php else: ?>
                <a href="login.php?redirect=ProItemList.php"><button class="shop-now-button">Order Now</button></a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Best Sellers Section -->
    <section class="Best-Sellers">
        <div class="section-header">
            <h2>Items Available to Request for Pre-Order</h2>
            <p class="section-subtitle">These are items you can request in advance. If enough students request an item, PAMO will consider stocking it!</p>
        </div>
        <div class="best-sellers-grid">
            <div class="best-sellers-track">
                <!-- Original Cards -->
                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="0">
                    <div class="product-image">
                        <img src="../Images/STI-TM.jpg" alt="STI TM Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="product-image">
                        <img src="../Images/STI-ICT.jpg" alt="STI ICT Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="product-image">
                        <img src="../Images/STI-HM.jpg" alt="STI HM Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="product-image">
                        <img src="../Images/STI-CM.jpg" alt="STI CM Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <!-- Cloned Cards for Seamless Loop -->
                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="product-image">
                        <img src="../Images/STI-TM.jpg" alt="STI TM Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="500">
                    <div class="product-image">
                        <img src="../Images/STI-ICT.jpg" alt="STI ICT Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="600">
                    <div class="product-image">
                        <img src="../Images/STI-HM.jpg" alt="STI HM Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>

                <div class="best-seller-card" data-aos="zoom-in" data-aos-delay="700">
                    <div class="product-image">
                        <img src="../Images/STI-CM.jpg" alt="STI CM Uniform" draggable="false" />
                        <div class="product-overlay">
                            <button class="add-to-cart-btn">Pre Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Showcase Section -->
    <section class="video-showcase">
        <video class="video-background" autoplay loop muted playsinline>
            <source src="../Images/last section.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay"></div>
        <div class="video-content">
            <div class="text-content">
                <h2>Experience Our Collection</h2>
                <p>Discover the perfect blend of style and professionalism with our exclusive STI uniforms and merchandise.</p>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="ProItemList.php" class="video-btn">Order Now</a>
            <?php else: ?>
                <a href="login.php?redirect=ProItemList.php" class="video-btn">Order Now</a>
            <?php endif; ?>
        </div>
    </section>

    <?php
    include("../Includes/footer.php");
    ?>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: false,
            mirror: true
        });
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