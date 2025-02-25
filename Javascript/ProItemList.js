document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    const productsGrid = document.querySelector('.products-grid');
    const filterInputs = document.querySelectorAll('.filter-group input');
    const clearFiltersBtn = document.querySelector('.clear-filters');
    const applyFiltersBtn = document.querySelector('.apply-btn');
    const sortSelect = document.getElementById('sort-select');
    const searchInput = document.getElementById('search');
    const productCount = document.getElementById('product-count');
    const quantityInputs = document.querySelectorAll('.quantity input');
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');

    // Mobile sidebar toggle
    const sidebarToggle = document.createElement('button');
    sidebarToggle.className = 'sidebar-toggle';
    sidebarToggle.innerHTML = '<i class="fas fa-filter"></i>';
    document.querySelector('.sort-container').prepend(sidebarToggle);

    sidebarToggle.addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    // Quantity buttons functionality
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const currentValue = parseInt(input.value);
            const max = parseInt(input.getAttribute('max'));
            
            if (this.classList.contains('plus') && currentValue < max) {
                input.value = currentValue + 1;
            } else if (this.classList.contains('minus') && currentValue > 1) {
                input.value = currentValue - 1;
            }
        });
    });

    // Wishlist functionality
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
        });
    });

    // Quick View Modal
    function createQuickViewModal() {
        const modal = document.createElement('div');
        modal.className = 'quick-view-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <button class="close-modal"><i class="fas fa-times"></i></button>
                <div class="modal-body"></div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }

    const quickViewModal = createQuickViewModal();

    document.querySelectorAll('.quick-view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const modalBody = quickViewModal.querySelector('.modal-body');
            modalBody.innerHTML = card.innerHTML;
            quickViewModal.classList.add('active');
        });
    });

    quickViewModal.querySelector('.close-modal').addEventListener('click', () => {
        quickViewModal.classList.remove('active');
    });

    // Get all checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart, .pre-order-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            const quantity = card.querySelector('.quantity input').value;
            const product = {
                name: card.querySelector('h3').textContent,
                price: parseFloat(card.dataset.price),
                quantity: parseInt(quantity)
            };
            
            // Add animation
            btn.classList.add('added');
            setTimeout(() => btn.classList.remove('added'), 1500);
            
            // Here you would typically send this data to your cart handling system
            console.log('Added to cart:', product);
        });
    });

    // Get all main category checkboxes
    const mainCategories = document.querySelectorAll('.main-category');
    const sizeFilter = document.querySelector('.size-filter');

    // Add click event listeners to main categories
    mainCategories.forEach(category => {
        category.addEventListener('change', function() {
            const categoryType = this.dataset.category;
            
            // Show/hide subcategories based on main category selection
            const subcategoryGroup = document.getElementById(`${categoryType}-${categoryType === 'sti-shirt' ? 'options' : categoryType === 'uniform' ? 'courses' : 'options'}`);
            if (subcategoryGroup) {
                subcategoryGroup.classList.toggle('hidden', !this.checked);
            }

            // Show/hide size filter for uniform and STI shirt categories
            if (categoryType === 'uniform' || categoryType === 'sti-shirt') {
                sizeFilter.classList.toggle('hidden', !this.checked);
            } else if (categoryType === 'accessories') {
                sizeFilter.classList.add('hidden');
            }

            // Uncheck other main categories
            mainCategories.forEach(otherCategory => {
                if (otherCategory !== this && this.checked) {
                    otherCategory.checked = false;
                    const otherCategoryType = otherCategory.dataset.category;
                    const otherSubcategoryGroup = document.getElementById(`${otherCategoryType}-${otherCategoryType === 'sti-shirt' ? 'options' : otherCategoryType === 'uniform' ? 'courses' : 'options'}`);
                    if (otherSubcategoryGroup) {
                        otherSubcategoryGroup.classList.add('hidden');
                    }
                }
            });
        });
    });

    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedSize = this.textContent;
            const card = this.closest('.product-container');
            const prices = card.dataset.prices.split(',');
            const sizes = card.dataset.sizes.split(',');

            const index = sizes.indexOf(selectedSize);
            if (index !== -1) {
                const price = prices[index];
                card.querySelector('.price-range').textContent = `Price: ₱${parseFloat(price).toFixed(2)}`;
                card.querySelector('.selected-size').textContent = `Selected Size: ${selectedSize}`;
            } else {
                card.querySelector('.price-range').textContent = 'Price not available';
            }
        });
    });
});