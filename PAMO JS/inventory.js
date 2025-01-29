// Sample inventory data
const inventoryItems = [
    {
        id: 1,
        name: "Printer Paper A4",
        category: "stationery",
        quantity: 500,
        price: 5.99,
        reorderLevel: 100,
        supplier: "ABC Stationers",
        status: "available"
    },
    {
        id: 2,
        name: "Whiteboard Markers",
        category: "stationery",
        quantity: 50,
        price: 2.99,
        reorderLevel: 20,
        supplier: "ABC Stationers",
        status: "low"
    },
    // Add more items as needed
];

// Initialize inventory grid
function initializeInventory() {
    const grid = document.querySelector('.inventory-grid');
    grid.innerHTML = '';

    inventoryItems.forEach(item => {
        const itemElement = createInventoryItem(item);
        grid.appendChild(itemElement);
    });
}

// Create inventory item card
function createInventoryItem(item) {
    const div = document.createElement('div');
    div.className = 'inventory-item';
    
    const status = getStockStatus(item.quantity, item.reorderLevel);
    
    div.innerHTML = `
        <div class="item-header">
            <h3 class="item-title">${item.name}</h3>
            <div class="item-actions">
                <i class="material-icons" onclick="editItem(${item.id})">edit</i>
                <i class="material-icons" onclick="deleteItem(${item.id})">delete</i>
            </div>
        </div>
        <div class="item-details">
            <div class="detail-group">
                <span class="detail-label">Category</span>
                <span class="detail-value">${item.category}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Quantity</span>
                <span class="detail-value">${item.quantity}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Unit Price</span>
                <span class="detail-value">$${item.price}</span>
            </div>
            <div class="detail-group">
                <span class="detail-label">Status</span>
                <span class="stock-status status-${status.toLowerCase()}">${status}</span>
            </div>
        </div>
    `;
    return div;
}

// Get stock status
function getStockStatus(quantity, reorderLevel) {
    if (quantity <= 0) return 'Out';
    if (quantity <= reorderLevel) return 'Low';
    return 'Available';
}

// Modal functions
function openModal() {
    document.getElementById('itemModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}

// Form submission
document.getElementById('itemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Handle form submission here
    closeModal();
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('itemModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Initialize the inventory when the page loads
document.addEventListener('DOMContentLoaded', initializeInventory);

// Search functionality
document.querySelector('.search-bar input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const filteredItems = inventoryItems.filter(item => 
        item.name.toLowerCase().includes(searchTerm) ||
        item.category.toLowerCase().includes(searchTerm)
    );
    
    const grid = document.querySelector('.inventory-grid');
    grid.innerHTML = '';
    filteredItems.forEach(item => {
        const itemElement = createInventoryItem(item);
        grid.appendChild(itemElement);
    });
});

// Filter functionality
document.getElementById('categoryFilter').addEventListener('change', filterItems);
document.getElementById('stockFilter').addEventListener('change', filterItems);

function filterItems() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    
    let filteredItems = inventoryItems;
    
    if (categoryFilter) {
        filteredItems = filteredItems.filter(item => item.category === categoryFilter);
    }
    
    if (stockFilter) {
        filteredItems = filteredItems.filter(item => {
            const status = getStockStatus(item.quantity, item.reorderLevel).toLowerCase();
            return status === stockFilter;
        });
    }
    
    const grid = document.querySelector('.inventory-grid');
    grid.innerHTML = '';
    filteredItems.forEach(item => {
        const itemElement = createInventoryItem(item);
        grid.appendChild(itemElement);
    });
} 