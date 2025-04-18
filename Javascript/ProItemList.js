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
    
    // Update the add to cart functionality
    document.querySelectorAll('.cart').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const productContainer = this.closest('.product-container');
            const itemName = productContainer.querySelector('.head p').textContent;
            const itemCode = productContainer.dataset.itemCode;
            const quantity = 1; // Default quantity, can be modified if you have quantity input

            try {
                const response = await fetch('../Includes/cart_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add&item_code=${itemCode}&quantity=${quantity}`
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update cart count in header
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                    
                    // Show success message
                    alert('Item added to cart successfully!');
                    
                    // Update cart popup content
                    updateCartPopup();
                } else {
                    alert(data.message || 'Error adding item to cart');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error adding item to cart');
            }
        });
    });

    // Function to update cart popup content
    async function updateCartPopup() {
        try {
            const response = await fetch('../Includes/cart_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart'
            });

            const data = await response.json();
            
            if (data.success) {
                const cartItems = document.querySelector('.cart-items');
                if (cartItems) {
                    if (data.cart_items && data.cart_items.length > 0) {
                        cartItems.innerHTML = data.cart_items.map(item => `
                            <div class="cart-item">
                                <img src="${item.image_path}" alt="${item.item_name}">
                                <div class="cart-item-details">
                                    <div class="cart-item-name">${item.item_name}</div>
                                    <div class="cart-item-info">
                                        ${item.size ? `<div class="cart-item-size">Size: ${item.size}</div>` : ''}
                                        <div class="cart-item-price">₱${item.price} × ${item.quantity}</div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        cartItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Initial cart popup update
    document.addEventListener('DOMContentLoaded', function() {
        updateCartPopup();
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

document.addEventListener("DOMContentLoaded", function() {
    const categories = document.querySelectorAll(".category-checkbox");

    // Ensure all subcategories are hidden when page loads
    document.querySelectorAll(".subcategory").forEach(sub => {
        sub.style.display = "none";
    });

    categories.forEach(category => {
        category.addEventListener("change", function() {
            let subcategoryDiv = document.getElementById(this.id + "-sub");
            subcategoryDiv.style.display = this.checked ? "block" : "none";
        });
    });
});

function createFlyingElement(startElement) {
    const flyingElement = document.createElement('div');
    flyingElement.className = 'cart-item-flying';
    flyingElement.innerHTML = '<i class="fa fa-shopping-bag"></i>';
    
    // Get start position (from the clicked button)
    const startRect = startElement.getBoundingClientRect();
    
    // Get end position (cart icon)
    const cartIcon = document.querySelector('.cart-icon');
    const endRect = cartIcon.getBoundingClientRect();
    
    // Set initial position
    flyingElement.style.top = `${startRect.top}px`;
    flyingElement.style.left = `${startRect.left}px`;
    flyingElement.style.opacity = '1';
    
    document.body.appendChild(flyingElement);
    
    // Trigger animation
    requestAnimationFrame(() => {
        flyingElement.style.transform = `translate(${endRect.left - startRect.left}px, ${endRect.top - startRect.top}px) scale(0.5)`;
        flyingElement.style.opacity = '0';
    });
    
    // Remove element after animation
    setTimeout(() => {
        document.body.removeChild(flyingElement);
        // Add shake animation to cart icon
        cartIcon.classList.add('cart-shake');
        setTimeout(() => cartIcon.classList.remove('cart-shake'), 500);
    }, 800);
}

async function addToCart(element, customData = null) {
    try {
        let itemCode, quantity, size;
        
        if (customData) {
            // Data coming from the size selection modal
            itemCode = customData.itemCode;
            quantity = customData.quantity;
            size = customData.size;
        } else {
            // Direct add to cart (for accessories)
            const productContainer = element.closest('.product-container');
            itemCode = productContainer.dataset.itemCode;
            quantity = 1;
            size = null;
        }

        const response = await fetch('../Includes/cart_operations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&item_code=${itemCode}&quantity=${quantity}${size ? `&size=${size}` : ''}`
        });

        const data = await response.json();
        
        if (data.success) {
            // Create flying animation from the clicked button to cart icon
            if (element) {
                createFlyingElement(element);
            }
            
            // Update cart count in header
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            
            // Show success message
            alert('Item added to cart successfully!');
            
            // Update cart popup content
            updateCartPopup();
        } else {
            alert(data.message || 'Error adding item to cart');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding item to cart');
    }
}

function updateCartPopup() {
    fetch('../Includes/cart_operations.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_cart'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartItems = document.querySelector('.cart-items');
            if (cartItems) {
                if (data.cart_items && data.cart_items.length > 0) {
                    cartItems.innerHTML = data.cart_items.map(item => `
                        <div class="cart-item">
                            <img src="${item.image_path}" alt="${item.item_name}">
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.item_name}</div>
                                <div class="cart-item-info">
                                    ${item.size ? `<div class="cart-item-size">Size: ${item.size}</div>` : ''}
                                    <div class="cart-item-price">₱${item.price} × ${item.quantity}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    cartItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    updateCartPopup();
});

function showSizeModal(element) {
    const productContainer = element.closest('.product-container');
    const category = productContainer.dataset.category;
    
    // If it's an accessory, add to cart directly without showing the modal
    if (category.toLowerCase() === 'accessories') {
        addToCart(element);
        return;
    }

    // Get all item data including stocks for each size
    const itemData = [];
    const sizes = productContainer.dataset.sizes.split(',');
    const prices = productContainer.dataset.prices.split(',');
    const stocks = productContainer.dataset.stocks ? productContainer.dataset.stocks.split(',') : [];

    sizes.forEach((size, index) => {
        itemData.push({
            size: size,
            price: prices[index],
            stock: stocks[index] || 0
        });
    });

    currentProduct = {
        itemCode: productContainer.dataset.itemCode,
        name: productContainer.dataset.itemName,
        itemData: itemData,
        image: productContainer.querySelector('img').src
    };

    // Update modal content
    document.getElementById('modalProductImage').src = currentProduct.image;
    document.getElementById('modalProductName').textContent = currentProduct.name;
    
    // Show initial price range
    const allPrices = currentProduct.itemData.map(item => Number(item.price));
    const minPrice = Math.min(...allPrices);
    const maxPrice = Math.max(...allPrices);
    document.getElementById('modalProductPrice').textContent = 
        minPrice === maxPrice 
            ? `Price: ₱${minPrice.toFixed(2)}` 
            : `Price Range: ₱${minPrice.toFixed(2)} - ₱${maxPrice.toFixed(2)}`;

    // Show total stock
    const totalStock = currentProduct.itemData.reduce((sum, item) => sum + Number(item.stock), 0);
    document.getElementById('modalProductStock').textContent = `Total Stock: ${totalStock}`;

    // Generate size options
    const sizeOptionsContainer = document.querySelector('.size-options');
    sizeOptionsContainer.innerHTML = '';
    
    // All possible sizes
    const allSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL', '6XL', '7XL'];
    
    allSizes.forEach(size => {
        const itemInfo = currentProduct.itemData.find(item => item.size === size);
        const sizeBtn = document.createElement('div');
        sizeBtn.className = 'size-option';
        sizeBtn.textContent = size;
        
        // Check if size is available
        const isAvailable = itemInfo && Number(itemInfo.stock) > 0;
        sizeBtn.classList.add(isAvailable ? 'available' : 'unavailable');
        
        if (isAvailable) {
            // Store price and stock data in the button's dataset
            sizeBtn.dataset.price = itemInfo.price;
            sizeBtn.dataset.stock = itemInfo.stock;
            sizeBtn.onclick = () => selectSize(sizeBtn);
        }
        
        sizeOptionsContainer.appendChild(sizeBtn);
    });

    // Disable Add to Cart button by default
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    addToCartBtn.disabled = true;

    modal.style.display = 'block';
}

function selectSize(element) {
    // Only allow selection/deselection if the size is available
    if (!element.classList.contains('available')) {
        return;
    }
    
    // If clicking on already selected size, deselect it
    if (element.classList.contains('selected')) {
        element.classList.remove('selected');
        // Reset price and stock display
        const allPrices = currentProduct.itemData.map(item => Number(item.price));
        const minPrice = Math.min(...allPrices);
        const maxPrice = Math.max(...allPrices);
        document.getElementById('modalProductPrice').textContent = 
            minPrice === maxPrice 
                ? `Price: ₱${minPrice.toFixed(2)}` 
                : `Price Range: ₱${minPrice.toFixed(2)} - ₱${maxPrice.toFixed(2)}`;
        
        const totalStock = currentProduct.itemData.reduce((sum, item) => sum + Number(item.stock), 0);
        document.getElementById('modalProductStock').textContent = `Total Stock: ${totalStock}`;
        
        // Disable add to cart button
        const addToCartBtn = document.querySelector('.add-to-cart-btn');
        addToCartBtn.disabled = true;
        return;
    }
    
    // Otherwise, proceed with selection
    document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('selected'));
    element.classList.add('selected');
    
    // Update price and stock display for selected size
    const price = Number(element.dataset.price);
    const stock = Number(element.dataset.stock);
    
    document.getElementById('modalProductPrice').textContent = `Price: ₱${price.toFixed(2)}`;
    document.getElementById('modalProductStock').textContent = `Stock: ${stock}`;
    document.getElementById('modalProductStock').className = 
        stock < 10 ? 'stock-display low-stock' : 'stock-display';
    
    // Update quantity input max value
    const quantityInput = document.getElementById('quantity');
    quantityInput.max = stock;
    quantityInput.value = Math.min(Number(quantityInput.value), stock);
    
    // Enable add to cart button
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    addToCartBtn.disabled = false;
}

function addToCartWithSize() {
    const selectedSize = document.querySelector('.size-option.selected');
    if (!selectedSize || !selectedSize.classList.contains('available')) {
        alert('Please select an available size');
        return;
    }

    const quantity = document.getElementById('quantity').value;
    const size = selectedSize.textContent;

    // Add to cart with size and quantity
    addToCart(null, {
        itemCode: currentProduct.itemCode,
        size: size,
        quantity: quantity
    });

    // Close modal
    modal.style.display = 'none';
    // Reset quantity
    document.getElementById('quantity').value = 1;
    // Reset size selection
    document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('selected'));
}