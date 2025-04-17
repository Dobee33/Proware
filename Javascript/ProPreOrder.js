document.addEventListener('DOMContentLoaded', function() {
    // Quantity control functionality
    document.querySelectorAll('.qty-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.qty-input');
            const currentValue = parseInt(input.value);
            
            if (this.classList.contains('minus')) {
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                    updateCartItem(input.dataset.itemId, input.value);
                }
            } else {
                input.value = currentValue + 1;
                updateCartItem(input.dataset.itemId, input.value);
            }
            updateTotalAmount();
        });
    });

    // Input change handler
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            updateCartItem(this.dataset.itemId, this.value);
            updateTotalAmount();
        });
    });

    // Toggle checkout button functionality
    document.querySelectorAll('.toggle-checkout-btn').forEach(button => {
        button.addEventListener('click', function() {
            const container = this.parentElement;
            const itemId = this.dataset.itemId;
            const isIncluded = this.classList.contains('check');
            
            // Update active state
            container.querySelectorAll('.toggle-checkout-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update data attribute
            container.querySelectorAll('.toggle-checkout-btn').forEach(btn => {
                btn.dataset.included = btn.classList.contains('check') ? isIncluded : !isIncluded;
            });
            
            updateCheckoutStatus(itemId, isIncluded);
            updateTotalAmount();
        });
    });

    // Initialize active states
    document.querySelectorAll('.toggle-container').forEach(container => {
        const checkBtn = container.querySelector('.check');
        const xBtn = container.querySelector('.x');
        
        if (checkBtn.dataset.included === 'true') {
            checkBtn.classList.add('active');
        } else {
            xBtn.classList.add('active');
        }
    });

    // Function to update cart item
    async function updateCartItem(itemId, quantity) {
        try {
            const response = await fetch('../Includes/cart_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&item_id=${itemId}&quantity=${quantity}`
            });
            
            const data = await response.json();
            if (data.success) {
                updateTotalAmount();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Function to update checkout status
    async function updateCheckoutStatus(itemId, includeInCheckout) {
        try {
            const response = await fetch('../Includes/cart_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=toggle_checkout&item_id=${itemId}&include=${includeInCheckout}`
            });
            
            const data = await response.json();
            if (data.success) {
                updateTotalAmount();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Function to update total amount based on included items
    function updateTotalAmount() {
        let total = 0;
        const includedItems = [];
        
        document.querySelectorAll('tr[data-item-id]').forEach(row => {
            const checkBtn = row.querySelector('.toggle-checkout-btn.check');
            if (checkBtn && checkBtn.classList.contains('active')) {
                const price = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace('₱', ''));
                const quantity = parseInt(row.querySelector('.qty-input').value);
                const itemId = row.dataset.itemId;
                total += price * quantity;
                includedItems.push(itemId);
            }
        });

        // Update the total amount display
        const totalElement = document.querySelector('.total-table td:last-child');
        if (totalElement) {
            totalElement.textContent = `₱${total.toFixed(2)}`;
        }

        // Update the hidden input for checkout
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            const includedItemsInput = checkoutForm.querySelector('#includedItems');
            if (includedItemsInput) {
                includedItemsInput.value = JSON.stringify(includedItems);
            }
            
            const totalAmountInput = checkoutForm.querySelector('input[name="total_amount"]');
            if (totalAmountInput) {
                totalAmountInput.value = total;
            }
        }
    }

    // Handle form submission
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const includedItems = [];
            document.querySelectorAll('tr[data-item-id]').forEach(row => {
                const checkBtn = row.querySelector('.toggle-checkout-btn.check');
                if (checkBtn && checkBtn.classList.contains('active')) {
                    includedItems.push(row.dataset.itemId);
                }
            });

            if (includedItems.length === 0) {
                alert('Please select at least one item to checkout');
                return;
            }

            // Update the included items input
            const includedItemsInput = this.querySelector('#includedItems');
            includedItemsInput.value = JSON.stringify(includedItems);

            // Submit the form
            this.submit();
        });
    }

    // Initialize total amount on page load
    updateTotalAmount();
}); 