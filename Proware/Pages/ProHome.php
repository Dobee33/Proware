<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../Includes/header.css">
    <link rel="stylesheet" href="../CSS/global.css">
    <link rel="stylesheet" href="../CSS/ProHome.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton+SC&family=Smooch+Sans:wght@100..900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <title>Homepage</title>
</head>

<body>
    <?php
    include("../Includes/Header.php");
    ?>
    <section class="Hero">
        <div class="hero-slideshow">
            <div class="hero-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../Images/STI-LOGIN.jpg')"></div>
            <div class="hero-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../Images/ACS ALL.jpg')"></div>
            <div class="hero-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../Images/ACS TM.jpg')"></div>
        </div>
        <div class="hero-content">
            <h1>Unlock your future <br> with Pre-Order <br> Savings</h1>
            <p>At PAMO, we streamline your Purchasing and Asset Management needs with ease. <br> Explore our inventory
                and discover the resources available <br> right at your fingertips.</p>
            <a href="ProItemList.php"><button class="pre-order-button">Pre Order</button></a>
        </div>
    </section>

    <section class="Explore">
        <div class="Explore-description">
            <h1>Explore Our Categories</h1>
            <p>Discover high-quality products tailored for your needs. Browse our Uniforms, stylish Accessories, and
                comfortable School T-Shirts to find everything you need in one place!</p>
        </div>
        <div class="container">
            <div class="card" class="code code--small code--left aos-init aos-animate" data-aos="zoom-in-down">
                <div class="product-image">
                    <img src="../Images/ACS IT.jpg" alt="STI Uniform" draggable="false" />
                </div>
                <div class="product-info">
                    <h2>STI Uniform</h2>
                    <p>Comfortable and professional attire for STI students, for daily use.</p>
                </div>
                <div class="btn">
                    <a href="ProItemList.php"><button class="buy-btn">Pre Order</button></a>
                </div>
            </div>
            <div class="card" class="code code--small code--left aos-init aos-animate" data-aos="zoom-in">
                <div class="product-image">
                    <img src="../Images/ACS ARTS SCIENCE.jpg" alt="STI Shirt" draggable="false" />
                </div>
                <div class="product-info">
                    <h2>STI Shirt</h2>
                    <p>Trendy and versatile shirt with the STI logo, ideal for events and casual wear.</p>
                </div>
                <div class="btn">
                    <a href="ProItemList.php"><button class="buy-btn">Pre Order</button></a>
                </div>
            </div>
            <div class="card" class="code code--small code--right aos-init aos-animate" data-aos="zoom-in-left">
                <div class="product-image">
                    <img src="../Images/ACS TM.jpg" alt="STI Accessories" draggable="false" />
                </div>
                <div class="product-info">
                    <h2>Accessories</h2>
                    <p>Stylish lanyards, pins, and bags to showcase school pride and functionality.</p>
                </div>
                <div class="btn">
                    <a href="ProItemList.php"><button class="buy-btn">Pre Order</button></a>
                </div>
            </div>
        </div>
    </section>

    <secion class="tagline">
        <div class="tag">
            <h1>Be future-ready. Be STI.</h1>
            <p>Explore our wide range of products and check stock availability right from your device.</p>
        </div>
    </secion>

    <section class="Featured">
        <div class="featured-description">
            <h1 data-aos="fade-up" data-aos-offset="30" data-aos-delay="50" data-aos-duration="1000"
                data-aos-easing="ease-out" data-aos-mirror="true" data-aos-once="false"
                data-aos-anchor-placement="top-center">Explore Our Featured <br>
                Products for <br> Purchase</h1>
            <p>Explore a wide selection of essential items, updated <br> regularly to meet your needs.</p>
            <a href="ProItemList.php"><button class="pre-order-button">Pre Order</button></a>
        </div>
        <div class="slideshow">
            <img src="../Images/ACS ALL.jpg" alt="Featured Product 1">
            <img src="../Images/ACS TM.jpg" alt="Featured Product 2">
            <img src="../Images/ACS ENG.jpg" alt="Featured Product 3">
        </div>
    </section>

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.slideshow img');

        function showSlides() {
            slides.forEach((slide, index) => {
                slide.style.display = index === slideIndex ? 'block' : 'none';
            });
            slideIndex = (slideIndex + 1) % slides.length;
        }

        setInterval(showSlides, 2000);
        showSlides();
    </script>

    <section class="about">
        <div class="last">
            <h1>About</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                deserunt mollit anim id est laborum.</p>
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