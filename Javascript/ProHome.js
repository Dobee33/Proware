

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