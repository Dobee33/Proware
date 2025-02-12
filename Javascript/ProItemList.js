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
    
    // Handle main category toggles
    const uniformCheckbox = document.querySelector('input[data-category="uniform"]');
    const stiShirtCheckbox = document.querySelector('input[data-category="sti-shirt"]');
    const accessoriesCheckbox = document.querySelector('input[data-category="accessories"]');
    
    if (uniformCheckbox) {
        uniformCheckbox.addEventListener('change', function() {
            const uniformCourses = document.getElementById('uniform-courses');
            if (uniformCourses) {
                uniformCourses.classList.toggle('hidden', !this.checked);
            }
            applyFilters();
        });
    }

    if (stiShirtCheckbox) {
        stiShirtCheckbox.addEventListener('change', function() {
            const stiShirtOptions = document.getElementById('sti-shirt-options');
            if (stiShirtOptions) {
                stiShirtOptions.classList.toggle('hidden', !this.checked);
            }
            applyFilters();
        });
    }

    if (accessoriesCheckbox) {
        accessoriesCheckbox.addEventListener('change', function() {
            const accessoriesOptions = document.getElementById('accessories-options');
            if (accessoriesOptions) {
                accessoriesOptions.classList.toggle('hidden', !this.checked);
            }
            applyFilters();
        });
    }

    // Add change event listener to each checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Show/hide size filter based on category selection
            const sizeFilter = document.querySelector('.size-filter');
            const hasSelectedCategory = document.querySelector('input[type="checkbox"]:checked');
            if (sizeFilter) {
                sizeFilter.classList.toggle('hidden', !hasSelectedCategory);
            }
            applyFilters();
        });
    });

    // Reset button functionality
    const resetButton = document.querySelector('.clear-filters');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            // Reset all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Hide all subcategories
            document.getElementById('uniform-courses')?.classList.add('hidden');
            document.getElementById('sti-shirt-options')?.classList.add('hidden');
            document.getElementById('accessories-options')?.classList.add('hidden');
            document.querySelector('.size-filter')?.classList.add('hidden');
            
            applyFilters();
        });
    }

    // Function to apply filters
    function applyFilters() {
        const selectedFilters = {
            categories: Array.from(document.querySelectorAll('input[data-category]:checked')).map(cb => cb.dataset.category),
            departments: Array.from(document.querySelectorAll('input[data-department]:checked')).map(cb => cb.dataset.department),
            shirts: Array.from(document.querySelectorAll('input[data-shirt]:checked')).map(cb => cb.dataset.shirt),
            accessories: Array.from(document.querySelectorAll('input[data-accessory]:checked')).map(cb => cb.dataset.accessory),
            sizes: Array.from(document.querySelectorAll('input[name="size"]:checked')).map(cb => cb.value)
        };

        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            let shouldShow = true;

            // If any filters are selected, product must match at least one selected filter
            if (Object.values(selectedFilters).some(arr => arr.length > 0)) {
                shouldShow = false;

                // Check category matches
                if (selectedFilters.categories.length > 0) {
                    shouldShow = shouldShow || selectedFilters.categories.includes(card.dataset.category);
                }

                // Check department matches
                if (selectedFilters.departments.length > 0) {
                    shouldShow = shouldShow || selectedFilters.departments.includes(card.dataset.department);
                }

                // Check shirt matches
                if (selectedFilters.shirts.length > 0) {
                    shouldShow = shouldShow || selectedFilters.shirts.includes(card.dataset.shirt);
                }

                // Check accessories matches
                if (selectedFilters.accessories.length > 0) {
                    shouldShow = shouldShow || selectedFilters.accessories.includes(card.dataset.accessory);
                }

                // Check size matches
                if (selectedFilters.sizes.length > 0) {
                    const cardSizes = card.dataset.size?.split(',') || [];
                    shouldShow = shouldShow || selectedFilters.sizes.some(size => cardSizes.includes(size));
                }
            }

            card.style.display = shouldShow ? 'block' : 'none';
        });

        // Update product count
        const visibleProducts = document.querySelectorAll('.product-card[style="display: block"]').length;
        const productCount = document.getElementById('product-count');
        if (productCount) {
            productCount.textContent = visibleProducts;
        }
    }

    // Sort functionality
    function sortProducts(method) {
        const products = Array.from(document.querySelectorAll('.product-card'));
        
        products.sort((a, b) => {
            switch(method) {
                case 'price-low':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-high':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'name-asc':
                    return a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent);
                case 'name-desc':
                    return b.querySelector('h3').textContent.localeCompare(a.querySelector('h3').textContent);
                default:
                    return 0;
            }
        });

        products.forEach(product => productsGrid.appendChild(product));
    }

    // Event listeners
    filterInputs.forEach(input => input.addEventListener('change', applyFilters));
    applyFiltersBtn.addEventListener('click', applyFilters);
    sortSelect.addEventListener('change', (e) => sortProducts(e.target.value));
    searchInput.addEventListener('input', applyFilters);

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

   

    applyFilters();
});