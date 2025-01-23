// Initialize charts when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Expenses Chart
    const expensesCtx = document.getElementById('expensesChart').getContext('2d');
    new Chart(expensesCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Monthly Expenses',
                data: [12000, 19000, 15000, 17000, 14000, 12450],
                backgroundColor: '#0072BC',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Stock Distribution Chart
    const stockCtx = document.getElementById('stockChart').getContext('2d');
    new Chart(stockCtx, {
        type: 'doughnut',
        data: {
            labels: ['Stationery', 'Books', 'Electronics', 'Sports', 'Others'],
            datasets: [{
                data: [30, 25, 15, 20, 10],
                backgroundColor: [
                    '#0072BC',
                    '#FDF005',
                    '#FF6B6B',
                    '#4ECDC4',
                    '#45B7D1'
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
});

// Add some sample recent activities
const activities = [
    'New order placed for office supplies',
    'Low stock alert: Printer paper',
    'Order #123 delivered',
    'New supplier added: ABC Stationers'
];

const activityList = document.querySelector('.activity-list');
activities.forEach(activity => {
    const div = document.createElement('div');
    div.className = 'activity-item';
    div.innerHTML = `
        <i class="material-icons">circle</i>
        <span>${activity}</span>
    `;
    activityList.appendChild(div);
}); 