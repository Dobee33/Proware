// Sample data for the table
const reportData = [
    {
        date: '2024-02-20',
        item: 'Printer Paper A4',
        category: 'Stationery',
        quantity: 50,
        amount: 299.50,
        status: 'completed'
    },
    {
        date: '2024-02-19',
        item: 'Scientific Calculators',
        category: 'Electronics',
        quantity: 20,
        amount: 599.80,
        status: 'pending'
    },
    {
        date: '2024-02-18',
        item: 'Sports Equipment',
        category: 'Sports',
        quantity: 15,
        amount: 899.99,
        status: 'completed'
    },
    {
        date: '2024-02-17',
        item: 'Library Books',
        category: 'Books',
        quantity: 100,
        amount: 1499.99,
        status: 'pending'
    },
    {
        date: '2024-02-16',
        item: 'Lab Equipment',
        category: 'Electronics',
        quantity: 5,
        amount: 2999.99,
        status: 'cancelled'
    }
];

// Initialize table when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    loadTableData();
});

function loadTableData() {
    const tbody = document.querySelector('#reportTable tbody');
    tbody.innerHTML = '';

    reportData.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${formatDate(row.date)}</td>
            <td>${row.item}</td>
            <td>${row.category}</td>
            <td>${row.quantity}</td>
            <td>$${row.amount.toFixed(2)}</td>
            <td><span class="status-cell status-${row.status}">${capitalizeFirst(row.status)}</span></td>
        `;
        tbody.appendChild(tr);
    });
}

function generateReport() {
    const reportType = document.getElementById('reportType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    showLoading();
    
    // Simulate API call
    setTimeout(() => {
        hideLoading();
        loadTableData();
        showNotification('Report generated successfully!');
    }, 1500);
}

function exportData(format) {
    showNotification(`Report exported as ${format.toUpperCase()} successfully!`);
}

function changePage(direction) {
    // Implement pagination logic here
    const currentPage = document.getElementById('currentPage');
    const pageNum = parseInt(currentPage.textContent.split(' ')[1]);
    const totalPages = parseInt(currentPage.textContent.split(' ')[3]);
    
    if (direction === 'next' && pageNum < totalPages) {
        currentPage.textContent = `Page ${pageNum + 1} of ${totalPages}`;
    } else if (direction === 'prev' && pageNum > 1) {
        currentPage.textContent = `Page ${pageNum - 1} of ${totalPages}`;
    }
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function capitalizeFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function showLoading() {
    document.querySelector('.report-container').style.opacity = '0.5';
}

function hideLoading() {
    document.querySelector('.report-container').style.opacity = '1';
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