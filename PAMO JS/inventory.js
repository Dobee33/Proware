let selectedItemCode = null;
let selectedPrice = null;

function selectRow(row, itemCode, price) {
    if (selectedItemCode === itemCode) {
        row.classList.remove('selected');
        selectedItemCode = null;
        selectedPrice = null;

        document.getElementById('editPriceBtn').disabled = true;
        document.getElementById('addQuantityBtn').disabled = true;
        return;
    }

    document.querySelectorAll('.inventory-table tbody tr').forEach(tr => {
        tr.classList.remove('selected');
    });

    row.classList.add('selected');

    selectedItemCode = itemCode;
    selectedPrice = price;

    document.getElementById('editPriceBtn').disabled = false;
    document.getElementById('addQuantityBtn').disabled = false;
}

function handleEditPrice() {
    if (!selectedItemCode) {
        alert('Please select an item first');
        return;
    }
    editPrice(selectedItemCode, selectedPrice);
}

function handleAddQuantity() {
    if (!selectedItemCode) {
        alert('Please select an item first');
        return;
    }
    addQuantity(selectedItemCode);
}

function editPrice(itemCode, currentPrice) {
    console.log('Edit Price clicked:', itemCode, currentPrice); // Debug log
    document.getElementById('itemId').value = itemCode;
    document.getElementById('newPrice').value = currentPrice;
    document.getElementById('editPriceModal').style.display = 'block';
}

function addQuantity(itemCode) {
    document.getElementById('quantityItemId').value = itemCode;
    document.getElementById('quantityToAdd').value = '';
    document.getElementById('addQuantityModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function updatePrice() {
    const itemCode = document.getElementById('itemId').value;
    const newPrice = document.getElementById('newPrice').value;

    console.log('Updating price:', itemCode, newPrice); // Debug log

    if (!newPrice || newPrice <= 0) {
        alert('Please enter a valid price');
        return;
    }

    fetch('update_price.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_code=${encodeURIComponent(itemCode)}&price=${encodeURIComponent(newPrice)}`
    })
        .then(response => {
            console.log('Response received'); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Data:', data); // Debug log
            if (data.success) {
                alert('Price updated successfully!');
                location.reload();
            } else {
                alert('Error updating price: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error); // Debug log
            alert('Error updating price: ' + error);
        });

    closeModal('editPriceModal');
}

function updateQuantity() {
    const itemCode = document.getElementById('quantityItemId').value;
    const quantityToAdd = document.getElementById('quantityToAdd').value;

    // Send AJAX request to update quantity
    fetch('update_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_code=${itemCode}&quantity=${quantityToAdd}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Quantity updated successfully!');
                // Update the status immediately without reloading
                const row = document.querySelector(`tr[data-item-code="${itemCode}"]`);
                if (row) {
                    updateStockStatus(row);
                }
                location.reload(); // Keep this for now to ensure all data is fresh
            } else {
                alert('Error updating quantity: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error updating quantity: ' + error);
        });

    closeModal('addQuantityModal');
}

function showAddItemModal() {
    document.getElementById('addItemModal').style.display = 'flex';
    document.getElementById('addItemForm').reset();
}

function submitNewItem(event) {
    event.preventDefault();

    const quantity = parseInt(document.getElementById('newItemQuantity').value);
    const damage = parseInt(document.getElementById('newItemDamage').value) || 0;

    // Get form values and validate
    const formData = {
        item_code: document.getElementById('newItemCode').value.trim(),
        category: document.getElementById('newCategory').value.trim(),
        item_name: document.getElementById('newItemName').value.trim(),
        sizes: document.getElementById('newSize').value.trim(),
        price: parseFloat(document.getElementById('newItemPrice').value),
        quantity: quantity,
        actual_quantity: quantity - damage,
        beginning_quantity: quantity,
        new_delivery: 0,
        damage: damage,
        status: 'In Stock'
    };

    // Debug log
    console.log('Sending data:', formData);

    // Validation
    if (!formData.item_code || !formData.category || !formData.item_name ||
        !formData.sizes || isNaN(formData.price) || isNaN(formData.quantity)) {
        alert('Please fill in all required fields with valid values');
        return;
    }

    fetch('add_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
        .then(async response => {
            const text = await response.text();
            console.log('Raw server response:', text);

            try {
                // Try to parse the response as JSON
                const data = JSON.parse(text);
                if (data.success) {
                    alert('Item added successfully!');
                    location.reload();
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            } catch (e) {
                console.error('Parse error:', e);
                console.error('Response text:', text);
                throw new Error('Server response was not valid JSON: ' + text);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
}

// Add this new function to handle size field visibility
document.getElementById('newCategory').addEventListener('change', function () {
    const sizeGroup = document.querySelector('.input-group:has(#newSize)');
    if (this.value === 'STI-Accessories') {
        sizeGroup.style.display = 'none';
        document.getElementById('newSize').value = 'One Size';
        document.getElementById('newSize').removeAttribute('required');
    } else {
        sizeGroup.style.display = 'block';
        document.getElementById('newSize').setAttribute('required', 'required');
    }
});

function searchItems() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.toLowerCase();
    const tableRows = document.querySelectorAll('.inventory-table tbody tr');

    tableRows.forEach(row => {
        const itemName = row.querySelector('td:nth-child(2)').textContent.toLowerCase(); // Item Name
        const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Category
        const itemCode = row.querySelector('td:nth-child(1)').textContent.toLowerCase(); // Item Code

        // Check if search term matches either item name or category
        if (itemName.includes(searchTerm) || category.includes(searchTerm) || itemCode.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function updateStockStatus(row) {
    const actualQuantity = parseInt(row.querySelector('td:nth-child(4)').textContent);
    const statusCell = row.querySelector('td:nth-child(11)');

    let status, statusClass;

    if (actualQuantity <= 0) {
        status = 'Out of Stock';
        statusClass = 'status-out-of-stock';
    } else if (actualQuantity <= 20) {
        status = 'Low Stock';
        statusClass = 'status-low-stock';
    } else {
        status = 'In Stock';
        statusClass = 'status-in-stock';
    }

    // Remove any existing status classes
    statusCell.classList.remove('status-in-stock', 'status-low-stock', 'status-out-of-stock');
    // Add new status class
    statusCell.classList.add(statusClass);
    statusCell.textContent = status;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    const sizeFilter = document.getElementById('sizeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    const tableRows = document.querySelectorAll('.inventory-table tbody tr');

    tableRows.forEach(row => {
        const itemName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Category column
        const size = row.querySelector('td:nth-child(8)').textContent;
        const status = row.querySelector('td:nth-child(11)').textContent.trim();

        const matchesSearch = searchTerm === '' ||
            itemName.includes(searchTerm) ||
            category.includes(searchTerm);
        const matchesCategory = categoryFilter === '' || category === categoryFilter;
        const matchesSize = sizeFilter === '' || size === sizeFilter;
        const matchesStatus = statusFilter.toLowerCase() === '' || status.toLowerCase() === statusFilter.toLowerCase();

        if (matchesSearch && matchesCategory && matchesSize && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Populate size filter dropdown dynamically
function populateFilters() {
    const sizeFilter = document.getElementById('sizeFilter');
    const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL', '6XL', '7XL'];
    
    // Get all unique sizes from the table
    const tableRows = document.querySelectorAll('.inventory-table tbody tr');
    const uniqueSizes = new Set();
    tableRows.forEach(row => {
        const size = row.querySelector('td:nth-child(8)').textContent.trim();
        if (size) uniqueSizes.add(size);
    });

    // Keep the "All Sizes" option
    const allSizesOption = sizeFilter.querySelector('option[value=""]');
    sizeFilter.innerHTML = '';
    sizeFilter.appendChild(allSizesOption);

    // Add unique sizes from the table
    Array.from(uniqueSizes).sort().forEach(size => {
        const option = document.createElement('option');
        option.value = size;
        option.textContent = size;
        sizeFilter.appendChild(option);
    });
}

// Call populateFilters when page loads
document.addEventListener('DOMContentLoaded', populateFilters);

// Add event listeners for all filters
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('categoryFilter').addEventListener('change', applyFilters);
document.getElementById('sizeFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

// Call applyFilters on page load
document.addEventListener('DOMContentLoaded', function () {
    applyFilters();
});

function logout() {
    // Redirect to logout.php
    window.location.href = '../Pages/login.php';
}
