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
    // Initialize date inputs
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    
    // Disable end date until start date is selected
    endDate.disabled = true;
    
    startDate.addEventListener('change', function() {
        endDate.disabled = false;
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = '';
        }
    });

    // Add search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        searchReports(this.value);
    });

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

function clearDates() {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    
    startDate.value = '';
    endDate.value = '';
    endDate.disabled = true;
    
    // Reset table display
    const reportType = document.getElementById('reportType').value;
    const table = document.querySelector(`#${reportType}Report table`);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = '';
    }
}

function changeReportType() {
    const reportType = document.getElementById('reportType').value;
    
    // Hide all reports
    document.getElementById('inventoryReport').style.display = 'none';
    document.getElementById('salesReport').style.display = 'none';
    document.getElementById('auditReport').style.display = 'none';
    
    // Show selected report
    document.getElementById(reportType + 'Report').style.display = 'block';
    
    // Clear dates when changing report type
    clearDates();
}

function searchReports(query) {
    query = query.toLowerCase();
    const reportType = document.getElementById('reportType').value;
    const table = document.querySelector(`#${reportType}Report table`);
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let cell of cells) {
            if (cell.textContent.toLowerCase().includes(query)) {
                found = true;
                break;
            }
        }

        row.style.display = found ? '' : 'none';
    }
}

function generateReport() {
    const reportType = document.getElementById('reportType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    // Filter table rows based on date range
    const table = document.querySelector(`#${reportType}Report table`);
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
        const row = rows[i];
        const dateCell = row.cells[row.cells.length - 1]; // Assuming date is in last column
        const rowDate = new Date(dateCell.textContent);
        const startDateTime = new Date(startDate);
        const endDateTime = new Date(endDate);

        if (rowDate >= startDateTime && rowDate <= endDateTime) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function exportToExcel() {
    const reportType = document.getElementById('reportType').value;
    const table = document.querySelector(`#${reportType}Report table`);
    
    // Create a workbook
    let csv = [];
    const rows = table.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName(i === 0 ? 'th' : 'td');
        let rowData = [];

        if (row.style.display !== 'none') { // Only export visible rows
            for (let cell of cells) {
                rowData.push(cell.textContent);
            }
            csv.push(rowData.join(','));
        }
    }

    // Create and download the CSV file
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `${reportType}_report_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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