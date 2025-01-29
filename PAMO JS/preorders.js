// Sample pre-orders data
const preOrders = [
    {
        id: 1,
        studentName: "John Smith",
        studentId: "STU001",
        studentPhoto: "default-avatar.png",
        orderDate: "2024-02-20T10:30:00",
        status: "pending",
        items: [
            { name: "Mathematics Textbook", quantity: 1, price: 45.99 },
            { name: "Scientific Calculator", quantity: 1, price: 29.99 }
        ],
        totalAmount: 75.98
    },
    // Add more pre-orders as needed
];

// Initialize pre-orders display
function initializePreOrders() {
    const container = document.querySelector('.preorders-container');
    container.innerHTML = '';

    const activeTab = document.querySelector('.tab.active').dataset.status;
    const filteredOrders = preOrders.filter(order => order.status === activeTab);

    filteredOrders.forEach(order => {
        const orderElement = createPreOrderCard(order);
        container.appendChild(orderElement);
    });
}

// Create pre-order card
function createPreOrderCard(order) {
    const div = document.createElement('div');
    div.className = 'preorder-card';
    
    div.innerHTML = `
        <div class="preorder-header">
            <div class="student-info">
                <img src="${order.studentPhoto}" alt="Student Photo" class="student-photo">
                <div>
                    <h3>${order.studentName}</h3>
                    <p>Student ID: ${order.studentId}</p>
                    <p>Ordered: ${formatDate(order.orderDate)}</p>
                </div>
            </div>
            <span class="order-status status-${order.status}">${capitalizeFirst(order.status)}</span>
        </div>
        <div class="order-items-list">
            ${order.items.map(item => `
                <div class="item-card">
                    <span>${item.name} x${item.quantity}</span>
                    <span>$${item.price.toFixed(2)}</span>
                </div>
            `).join('')}
        </div>
        <div class="total-section">
            <span>Total Amount:</span>
            <span>$${order.totalAmount.toFixed(2)}</span>
        </div>
        ${order.status === 'pending' ? `
            <div class="action-buttons">
                <button class="btn-reject" onclick="openReviewModal(${order.id}, 'reject')">Reject</button>
                <button class="btn-approve" onclick="openReviewModal(${order.id}, 'approve')">Approve</button>
            </div>
        ` : ''}
    `;
    return div;
}

// Tab switching
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        initializePreOrders();
    });
});

// Modal functions
function openReviewModal(orderId, action) {
    const order = preOrders.find(o => o.id === orderId);
    if (!order) return;

    const modal = document.getElementById('reviewModal');
    document.getElementById('studentName').textContent = order.studentName;
    document.getElementById('studentId').textContent = `Student ID: ${order.studentId}`;
    document.getElementById('orderDate').textContent = `Ordered: ${formatDate(order.orderDate)}`;
    
    const itemsList = document.getElementById('itemsList');
    itemsList.innerHTML = order.items.map(item => `
        <div class="item-card">
            <span>${item.name} x${item.quantity}</span>
            <span>$${item.price.toFixed(2)}</span>
        </div>
    `).join('');
    
    document.getElementById('totalAmount').textContent = `$${order.totalAmount.toFixed(2)}`;
    
    modal.style.display = 'block';
}

function approveOrder() {
    const pickupDate = document.getElementById('pickupDate').value;
    if (!pickupDate) {
        alert('Please set a pickup date');
        return;
    }
    
    // Here you would typically send this to your backend
    showNotification('Pre-order approved! Student will be notified.');
    closeModal();
    initializePreOrders();
}

function rejectOrder() {
    const notes = document.getElementById('notes').value;
    // Here you would typically send this to your backend
    showNotification('Pre-order rejected. Student will be notified.');
    closeModal();
    initializePreOrders();
}

function closeModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

function capitalizeFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    notification.style.display = 'block';
    setTimeout(() => {
        notification.style.display = 'none';
        notification.remove();
    }, 3000);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', initializePreOrders);

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reviewModal');
    if (event.target == modal) {
        closeModal();
    }
} 