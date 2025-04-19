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

    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            offset: 100,
            once: true
        });
    }

    // Handle category checkboxes
    const categories = document.querySelectorAll(".category-checkbox");

    // Ensure all subcategories are hidden when page loads
    document.querySelectorAll(".subcategory").forEach(sub => {
        sub.style.display = "none";
    });

    categories.forEach(category => {
        category.addEventListener("change", function () {
            let subcategoryDiv = document.getElementById(this.id + "-sub");
            subcategoryDiv.style.display = this.checked ? "block" : "none";
        });
    });

    // Mobile sidebar toggle
    if (document.querySelector('.sort-container')) {
        const sidebarToggle = document.createElement('button');
        sidebarToggle.className = 'sidebar-toggle';
        sidebarToggle.innerHTML = '<i class="fas fa-filter"></i>';
        document.querySelector('.sort-container').prepend(sidebarToggle);

        sidebarToggle.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }

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

    // Modal functionality
    const modal = document.getElementById('sizeModal');
    const closeBtn = document.getElementsByClassName('close')[0];
    let currentProduct = null;

    // Add event listeners for the close buttons
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Update the add to cart functionality - ONLY trigger on cart icon click
    document.querySelectorAll('.cart').forEach(btn => {
        // Clear any existing event listeners
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        // Add the click event listener
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleAddToCart(this);
        });
    });

    // Remove Quick View Modal functionality as it's interfering with UI
    document.querySelectorAll('.quick-view-btn').forEach(btn => {
        // Disable the quick view functionality
        btn.style.display = 'none';
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

    // Initial cart popup update
    updateCartPopup();

    // Make sure all close buttons for modals work correctly
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Close the accessory modal
    const accessoryModalClose = document.querySelector('#accessoryModal .close');
    if (accessoryModalClose) {
        accessoryModalClose.addEventListener('click', closeAccessoryModal);
    }
});

// Handle cart interaction - ONLY processing cart icon clicks
function handleAddToCart(element) {
    // Verify this is a cart element
    if (!element.classList.contains('cart')) {
        return;
    }

    const productContainer = element.closest('.product-container');
    if (!productContainer) {
        console.error('Product container not found');
        return;
    }

    const category = productContainer.dataset.category;
    
    if (category && (category.toLowerCase().includes('accessories') || category.toLowerCase().includes('sti-accessories'))) {
        showAccessoryModal(element);
    } else {
        showSizeModal(element);
    }
}

function showAccessoryModal(element) {
    const productContainer = element.closest('.product-container');
    const price = productContainer.dataset.prices.split(',')[0];
    const stock = productContainer.dataset.stock;
    
    currentProduct = {
        itemCode: productContainer.dataset.itemCode,
        name: productContainer.dataset.itemName,
        price: price,
        stock: stock,
        image: productContainer.querySelector('img').src,
        category: productContainer.dataset.category
    };

    // Update modal content
    document.getElementById('accessoryModalImage').src = currentProduct.image;
    document.getElementById('accessoryModalName').textContent = currentProduct.name;
    document.getElementById('accessoryModalPrice').textContent = `Price: ₱${parseFloat(currentProduct.price).toFixed(2)}`;
    document.getElementById('accessoryModalStock').textContent = `Stock: ${currentProduct.stock}`;

    // Set max quantity
    const accessoryQuantityInput = document.getElementById('accessoryQuantity');
    accessoryQuantityInput.max = currentProduct.stock;
    accessoryQuantityInput.value = ''; // Set empty value initially
    accessoryQuantityInput.placeholder = "0"; // Add placeholder
    
    // Add input validation
    accessoryQuantityInput.addEventListener('input', function() {
        validateAccessoryQuantity(this);
    });
    
    accessoryQuantityInput.addEventListener('blur', function() {
        validateAccessoryQuantity(this, true);
    });
    
    // Clear previous event listeners
    const oldInput = accessoryQuantityInput.cloneNode(true);
    accessoryQuantityInput.parentNode.replaceChild(oldInput, accessoryQuantityInput);
    
    // Add event listeners to the new input
    const newInput = document.getElementById('accessoryQuantity');
    newInput.addEventListener('input', function() {
        validateAccessoryQuantity(this);
    });
    
    newInput.addEventListener('blur', function() {
        validateAccessoryQuantity(this, true);
    });
    
    // Show the modal
    document.getElementById('accessoryModal').style.display = 'block';
}

function validateAccessoryQuantity(input, enforceMax = false) {
    // Only validate non-empty values
    if (input.value === '') {
        return; // Allow empty value during input
    }
    
    // Make sure it's a positive integer
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Make sure it's at least 1 if there's a value
    if (input.value !== '' && parseInt(input.value) < 1) {
        input.value = 1;
    }
    
    // Check against max stock
    if (enforceMax && input.value !== '') {
        const maxStock = parseInt(currentProduct.stock);
        if (parseInt(input.value) > maxStock) {
            input.value = maxStock;
            alert(`Maximum available stock is ${maxStock}.`);
        }
    }
}

function closeAccessoryModal() {
    document.getElementById('accessoryModal').style.display = 'none';
    document.getElementById('accessoryQuantity').value = '';
}

function incrementAccessoryQuantity() {
    const input = document.getElementById('accessoryQuantity');
    const max = parseInt(currentProduct.stock);
    const currentValue = input.value === '' ? 0 : parseInt(input.value);
    
    if (currentValue < max) {
        input.value = currentValue + 1;
    } else {
        input.value = max; // Ensure it doesn't exceed max
    }
}

function decrementAccessoryQuantity() {
    const input = document.getElementById('accessoryQuantity');
    const currentValue = input.value === '' ? 0 : parseInt(input.value);
    
    if (currentValue > 1) {
        input.value = currentValue - 1;
    } else if (currentValue === 0) {
        input.value = ''; // Keep it empty if it was empty
    } else {
        input.value = 1; // Minimum is 1 if it had a value
    }
}

function addAccessoryToCart() {
    const quantityInput = document.getElementById('accessoryQuantity');
    
    // Validate that quantity is not empty
    if (quantityInput.value === '') {
        alert('Please enter a quantity');
        return;
    }
    
    const quantity = parseInt(quantityInput.value);
    const availableStock = parseInt(currentProduct.stock);
    
    // Validate quantity against stock
    if (quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    if (quantity > availableStock) {
        alert(`Sorry, only ${availableStock} items are available in stock.`);
        quantityInput.value = availableStock; // Set to maximum available
        return;
    }
    
    addToCart(null, {
        itemCode: currentProduct.itemCode,
        quantity: quantity,
        size: 'One Size'
    });

    closeAccessoryModal();
}

function showSizeModal(element) {
    const productContainer = element.closest('.product-container');
    const category = productContainer.dataset.category;
    
    currentProduct = {
        itemCode: productContainer.dataset.itemCode,
        name: productContainer.dataset.itemName,
        sizes: productContainer.dataset.sizes.split(','),
        prices: productContainer.dataset.prices.split(','),
        stocks: productContainer.dataset.stocks.split(','),
        itemCodes: productContainer.dataset.itemCodes ? productContainer.dataset.itemCodes.split(',') : [],
        image: productContainer.querySelector('img').src,
        category: category,
        stock: productContainer.dataset.stock
    };

    // Update modal content
    document.getElementById('modalProductImage').src = currentProduct.image;
    document.getElementById('modalProductName').textContent = currentProduct.name;
    document.getElementById('modalProductPrice').textContent = `Price Range: ₱${Math.min(...currentProduct.prices.map(Number)).toFixed(2)} - ₱${Math.max(...currentProduct.prices.map(Number)).toFixed(2)}`;
    document.getElementById('modalProductStock').textContent = `Total Stock: ${currentProduct.stock}`;

    // Generate size options
    const sizeOptionsContainer = document.querySelector('.size-options');
    sizeOptionsContainer.innerHTML = '';
    
    currentProduct.sizes.forEach((size, index) => {
        const sizeBtn = document.createElement('div');
        sizeBtn.className = 'size-option';
        sizeBtn.textContent = size;
        
        // Add stock and individual item code as data attributes
        const stock = currentProduct.stocks[index] || 0;
        const itemCode = currentProduct.itemCodes[index] || currentProduct.itemCode;
        
        sizeBtn.dataset.stock = stock;
        sizeBtn.dataset.itemCode = itemCode;
        sizeBtn.dataset.price = currentProduct.prices[index];
        
        // Add available class if stock > 0
        if (parseInt(stock) > 0) {
            sizeBtn.classList.add('available');
        } else {
            sizeBtn.classList.add('unavailable');
        }
        
        sizeBtn.onclick = () => selectSize(sizeBtn);
        sizeOptionsContainer.appendChild(sizeBtn);
    });
    
    // Get the quantity input
    const quantityInput = document.getElementById('quantity');
    quantityInput.value = ''; // Set empty value initially
    quantityInput.placeholder = "Enter quantity"; // Add placeholder
    
    // Clear previous event listeners
    const oldInput = quantityInput.cloneNode(true);
    quantityInput.parentNode.replaceChild(oldInput, quantityInput);
    
    // Add event listeners to the new input
    const newInput = document.getElementById('quantity');
    newInput.addEventListener('input', function() {
        validateQuantityInput(this);
    });
    
    newInput.addEventListener('blur', function() {
        validateQuantityInput(this, true);
    });

    document.getElementById('sizeModal').style.display = 'block';
}

function validateQuantityInput(input, enforceMax = false) {
    // Only validate non-empty values
    if (input.value === '') {
        return; // Allow empty value during input
    }
    
    // Make sure it's a positive integer
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Make sure it's at least 1 if there's a value
    if (input.value !== '' && parseInt(input.value) < 1) {
        input.value = 1;
    }
    
    // If a size is selected, check against max stock
    const selectedSize = document.querySelector('.size-option.selected');
    if (selectedSize && enforceMax && input.value !== '') {
        const maxStock = parseInt(selectedSize.dataset.stock);
        if (parseInt(input.value) > maxStock) {
            input.value = maxStock;
            alert(`Maximum available stock for this size is ${maxStock}.`);
        }
    }
}

function selectSize(element) {
    // Only allow selection if size is available
    if (element.classList.contains('unavailable')) {
        return;
    }
    
    document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('selected'));
    element.classList.add('selected');
    
    // Update stock display for the selected size
    const stock = element.dataset.stock;
    const price = element.dataset.price;
    
    document.getElementById('modalProductStock').textContent = `Stock: ${stock}`;
    document.getElementById('modalProductPrice').textContent = `Price: ₱${parseFloat(price).toFixed(2)}`;
    
    // Update max quantity
    const quantityInput = document.getElementById('quantity');
    const maxStock = parseInt(stock);
    quantityInput.max = maxStock;
    
    // Adjust quantity if it exceeds the available stock for the selected size
    const currentQty = parseInt(quantityInput.value);
    if (currentQty > maxStock) {
        quantityInput.value = maxStock;
        alert(`Quantity has been adjusted to ${maxStock}, the maximum available stock for this size.`);
    }
}

function incrementQuantity() {
    const input = document.getElementById('quantity');
    const selectedSize = document.querySelector('.size-option.selected');
    
    if (!selectedSize) {
        return; // Don't increment if no size is selected
    }
    
    const max = parseInt(selectedSize.dataset.stock);
    const currentValue = input.value === '' ? 0 : parseInt(input.value);
    
    if (currentValue < max) {
        input.value = currentValue + 1;
    } else {
        input.value = max; // Ensure it doesn't exceed max
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = input.value === '' ? 0 : parseInt(input.value);
    
    if (currentValue > 1) {
        input.value = currentValue - 1;
    } else if (currentValue === 0) {
        input.value = ''; // Keep it empty if it was empty
    } else {
        input.value = 1; // Minimum is 1 if it had a value
    }
}

function addToCartWithSize() {
    const selectedSize = document.querySelector('.size-option.selected');
    if (!selectedSize && !currentProduct.category?.toLowerCase().includes('accessories')) {
        alert('Please select a size');
        return;
    }

    const quantityInput = document.getElementById('quantity');
    
    // Validate that quantity is not empty
    if (quantityInput.value === '') {
        alert('Please enter a quantity');
        return;
    }
    
    const quantity = parseInt(quantityInput.value);
    const size = currentProduct.category?.toLowerCase().includes('accessories') ? 'One Size' : selectedSize.textContent;
    
    // Get the specific item code for the selected size
    const itemCode = selectedSize ? selectedSize.dataset.itemCode : currentProduct.itemCode;
    
    // Get the stock for validation
    const availableStock = selectedSize ? parseInt(selectedSize.dataset.stock) : parseInt(currentProduct.stock);
    
    // Validate quantity against stock (final check)
    if (quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    if (quantity > availableStock) {
        alert(`Sorry, only ${availableStock} items are available in stock for size ${size}.`);
        quantityInput.value = availableStock; // Set to maximum available
        return;
    }

    // Add to cart with size and quantity
    addToCart(null, {
        itemCode: itemCode,
        size: size,
        quantity: quantity
    });

    // Close modal
    const modal = document.getElementById('sizeModal');
    modal.style.display = 'none';
    // Reset quantity
    document.getElementById('quantity').value = '';
}

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
            // Data coming from the size selection modal or direct accessory add
            itemCode = customData.itemCode;
            quantity = customData.quantity;
            size = customData.size;
            console.log('Using custom data:', customData); // Debug log
        } else {
            // Direct add to cart (for accessories)
            const productContainer = element.closest('.product-container');
            const category = productContainer.dataset.category;
            itemCode = productContainer.dataset.itemCode;
            quantity = 1;
            size = (category && (category.toLowerCase().includes('accessories') || category.toLowerCase().includes('sti-accessories'))) ? 'One Size' : null;
            console.log('Direct add data:', { itemCode, quantity, size, category }); // Debug log
        }

        // Create the form data
        const formData = new URLSearchParams();
        formData.append('action', 'add');
        formData.append('item_code', itemCode);
        formData.append('quantity', quantity);
        formData.append('size', size || ''); // Ensure size is always sent, even if empty string

        console.log('Sending to server:', formData.toString()); // Debug log

        const response = await fetch('../Includes/cart_operations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });

        const data = await response.json();
        console.log('Server response:', data); // Debug log
        
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