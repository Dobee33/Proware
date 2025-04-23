// Sample data for the table
const reportData = [
  {
    date: "2024-02-20",
    item: "Printer Paper A4",
    category: "Stationery",
    quantity: 50,
    amount: 299.5,
    status: "completed",
  },
  {
    date: "2024-02-19",
    item: "Scientific Calculators",
    category: "Electronics",
    quantity: 20,
    amount: 599.8,
    status: "pending",
  },
  {
    date: "2024-02-18",
    item: "Sports Equipment",
    category: "Sports",
    quantity: 15,
    amount: 899.99,
    status: "completed",
  },
  {
    date: "2024-02-17",
    item: "Library Books",
    category: "Books",
    quantity: 100,
    amount: 1499.99,
    status: "pending",
  },
  {
    date: "2024-02-16",
    item: "Lab Equipment",
    category: "Electronics",
    quantity: 5,
    amount: 2999.99,
    status: "cancelled",
  },
];

// Initialize table when the document is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize date inputs
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");

  // Disable end date until start date is selected
  endDate.disabled = true;

  startDate.addEventListener("change", function () {
    endDate.disabled = false;
    endDate.min = this.value;
    if (endDate.value && endDate.value < this.value) {
      endDate.value = "";
    }
  });

  // Add search functionality
  document.getElementById("searchInput").addEventListener("input", function () {
    searchReports(this.value);
  });

  loadTableData();
});

function loadTableData() {
  const tbody = document.querySelector("#reportTable tbody");
  tbody.innerHTML = "";

  reportData.forEach((row) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td>${formatDate(row.date)}</td>
            <td>${row.item}</td>
            <td>${row.category}</td>
            <td>${row.quantity}</td>
            <td>$${row.amount.toFixed(2)}</td>
            <td><span class="status-cell status-${
              row.status
            }">${capitalizeFirst(row.status)}</span></td>
        `;
    tbody.appendChild(tr);
  });
}

function applyDailyFilter() {
  const dailyButton = document.querySelector(".daily-filter-btn");
  const monthlyButton = document.querySelector(".monthly-filter-btn");

  // If daily button is already active, deselect it
  if (dailyButton.classList.contains("active")) {
    dailyButton.classList.remove("active");
    clearDates();
    return;
  }

  const today = new Date();
  const formattedDate = today.toISOString().split("T")[0];

  document.getElementById("startDate").value = formattedDate;
  document.getElementById("endDate").value = formattedDate;

  // Update active state of filter buttons
  dailyButton.classList.add("active");
  monthlyButton.classList.remove("active");

  // Apply filters and update total
  const reportType = document.getElementById("reportType").value;
  if (reportType === "sales") {
    applyFilters(true);
  } else {
    applyFilters();
  }
}

function applyMonthlyFilter() {
  const dailyButton = document.querySelector(".daily-filter-btn");
  const monthlyButton = document.querySelector(".monthly-filter-btn");

  // If monthly button is already active, deselect it
  if (monthlyButton.classList.contains("active")) {
    monthlyButton.classList.remove("active");
    clearDates();
    return;
  }

  const today = new Date();
  const currentYear = today.getFullYear();
  const currentMonth = today.getMonth(); // 0-11 for Jan-Dec

  // Set to first day of current month
  const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
  // Set to last day of current month
  const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

  // Format dates as YYYY-MM-DD
  const formatDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  };

  document.getElementById("startDate").value = formatDate(firstDayOfMonth);
  document.getElementById("endDate").value = formatDate(lastDayOfMonth);

  // Update active state of filter buttons
  monthlyButton.classList.add("active");
  dailyButton.classList.remove("active");

  // Apply filters and update total
  const reportType = document.getElementById("reportType").value;
  if (reportType === "sales") {
    applyFilters(true);
  } else {
    applyFilters();
  }
}

function clearDates() {
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");

  startDate.value = "";
  endDate.value = "";
  endDate.disabled = true;

  // Remove active state from filter buttons
  document.querySelector(".daily-filter-btn").classList.remove("active");
  document.querySelector(".monthly-filter-btn").classList.remove("active");

  // Hide total amount display
  const totalDisplay = document.querySelector(".total-amount-display");
  if (totalDisplay) {
    totalDisplay.style.display = "none";
  }

  // Reset total amount display
  document.getElementById("totalSalesAmount").textContent = "₱0.00";

  // Refresh the current report view
  const reportType = document.getElementById("reportType").value;
  const formData = new FormData();
  formData.append("reportType", reportType);
  formData.append("clearFilters", "true");

  showLoading();

  fetch("../PAMO PAGES/includes/filter_reports.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      const reportDiv = document.getElementById(reportType + "Report");
      if (reportDiv) {
        reportDiv.innerHTML = data;
        // Ensure total amount display is hidden after clearing
        const newTotalDisplay = reportDiv.querySelector(
          ".total-amount-display"
        );
        if (newTotalDisplay) {
          newTotalDisplay.style.display = "none";
        }
      }
      hideLoading();
    })
    .catch((error) => {
      console.error("Error:", error);
      hideLoading();
      showNotification("Error clearing filters: " + error.message);
    });
}

function applyFilters(shouldUpdateTotal = false) {
  const startDate = document.getElementById("startDate").value;
  const endDate = document.getElementById("endDate").value;
  const reportType = document.getElementById("reportType").value;

  if (!startDate || !endDate) {
    alert("Please select both start and end dates");
    return;
  }

  showLoading();

  // Create form data
  const formData = new FormData();
  formData.append("reportType", reportType);
  formData.append("startDate", startDate);
  formData.append("endDate", endDate);

  // Send AJAX request to fetch filtered data
  fetch("../PAMO PAGES/includes/filter_reports.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      // Update the table content with the filtered data
      const reportDiv = document.getElementById(reportType + "Report");
      if (reportDiv) {
        reportDiv.innerHTML = data;

        // Update total amount for sales report
        if (reportType === "sales" || shouldUpdateTotal) {
          setTimeout(updateTotalSalesAmount, 100); // Add slight delay to ensure DOM is updated
        }

        hideLoading();
      } else {
        console.error("Report div not found:", reportType + "Report");
        hideLoading();
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      hideLoading();
      showNotification("Error applying filters: " + error.message);
    });
}

function updateTotalSalesAmount() {
  const salesTable = document.querySelector("#salesReport table");
  if (!salesTable) return;

  const rows = salesTable.getElementsByTagName("tr");
  let total = 0;

  // Start from 1 to skip header row
  for (let i = 1; i < rows.length; i++) {
    const row = rows[i];
    if (row.style.display !== "none") {
      const amountCell = row.cells[6]; // Index of Total Amount column
      if (amountCell) {
        // Remove ₱ symbol and commas, then parse
        const amount = parseFloat(
          amountCell.textContent.replace("₱", "").replace(/,/g, "")
        );
        if (!isNaN(amount)) {
          total += amount;
        }
      }
    }
  }

  // Update the total amount display
  const totalDisplay = document.querySelector(".total-amount-display");
  if (totalDisplay) {
    totalDisplay.style.display = "block"; // Show the total amount display
    const totalAmountSpan = document.getElementById("totalSalesAmount");
    if (totalAmountSpan) {
      totalAmountSpan.textContent =
        "₱" +
        total.toLocaleString("en-US", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });
    }
  }
}

function searchReports(query) {
  query = query.toLowerCase();
  const reportType = document.getElementById("reportType").value;
  const table = document.querySelector(`#${reportType}Report table`);
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    // Start from 1 to skip header row
    const row = rows[i];
    const cells = row.getElementsByTagName("td");
    let found = false;

    for (let cell of cells) {
      if (cell.textContent.toLowerCase().includes(query)) {
        found = true;
        break;
      }
    }

    row.style.display = found ? "" : "none";
  }
}

function changeReportType() {
  const reportType = document.getElementById("reportType").value;

  // Hide all reports
  document.getElementById("inventoryReport").style.display = "none";
  document.getElementById("salesReport").style.display = "none";
  document.getElementById("auditReport").style.display = "none";

  // Show selected report
  document.getElementById(reportType + "Report").style.display = "block";

  // Hide total amount display when changing report type
  const totalDisplay = document.querySelector(".total-amount-display");
  if (totalDisplay) {
    totalDisplay.style.display = "none";
  }

  // Clear dates and filters
  clearDates();
}

function exportToExcel() {
  const reportType = document.getElementById("reportType").value;
  const table = document.querySelector(`#${reportType}Report table`);

  // Create a workbook
  let csv = [];
  const rows = table.getElementsByTagName("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const cells = row.getElementsByTagName(i === 0 ? "th" : "td");
    let rowData = [];

    if (row.style.display !== "none") {
      // Only export visible rows
      for (let cell of cells) {
        rowData.push(cell.textContent);
      }
      csv.push(rowData.join(","));
    }
  }

  // Create and download the CSV file
  const csvContent = csv.join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);

  link.setAttribute("href", url);
  link.setAttribute(
    "download",
    `${reportType}_report_${new Date().toISOString().split("T")[0]}.csv`
  );
  link.style.visibility = "hidden";

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function changePage(direction) {
  // Implement pagination logic here
  const currentPage = document.getElementById("currentPage");
  const pageNum = parseInt(currentPage.textContent.split(" ")[1]);
  const totalPages = parseInt(currentPage.textContent.split(" ")[3]);

  if (direction === "next" && pageNum < totalPages) {
    currentPage.textContent = `Page ${pageNum + 1} of ${totalPages}`;
  } else if (direction === "prev" && pageNum > 1) {
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
  const tables = document.querySelectorAll(".report-table");
  tables.forEach((table) => {
    table.style.opacity = "0.5";
  });
}

function hideLoading() {
  const tables = document.querySelectorAll(".report-table");
  tables.forEach((table) => {
    table.style.opacity = "1";
  });
}

function showNotification(message) {
  const notification = document.createElement("div");
  notification.className = "notification";
  notification.textContent = message;
  document.body.appendChild(notification);

  notification.style.display = "block";
  setTimeout(() => {
    notification.style.display = "none";
    notification.remove();
  }, 3000);
}
