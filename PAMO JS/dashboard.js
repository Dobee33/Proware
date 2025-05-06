function logout() {
  if (confirm("Are you sure you want to log out?")) {
    window.location.href = "../Pages/logout.php";
  }
}

function clearActivities() {
  if (confirm("Are you sure you want to clear all activities?")) {
    fetch("../PAMO PAGES/clear_activities.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.querySelector(".activity-list").innerHTML =
            "<p class='no-activities'>No recent activities</p>";
        } else {
          alert(
            "Failed to clear activities: " + (data.error || "Unknown error")
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while clearing activities");
      });
  }
}

function redirectToLowStock() {
  sessionStorage.setItem("applyLowStockFilter", "true");
  window.location.href = "inventory.php";
}

let stockPieChart, salesLineChart;

async function fetchStockData(category = "", course = "") {
  const params = new URLSearchParams({ category, course });
  try {
    const res = await fetch(
      `../PAMO_DASHBOARD_BACKEND/api_inventory_stocks.php?${params}`
    );
    if (!res.ok) {
      console.error("Stock endpoint error:", res.status, res.statusText);
      return [];
    }
    const data = await res.json();
    console.log("Stock Data:", data);
    return data;
  } catch (error) {
    console.error("Fetch stock data error:", error);
    return [];
  }
}

async function fetchSalesData(category, course, period) {
  const params = new URLSearchParams({ category, course, period });
  try {
    const res = await fetch(
      `../PAMO_DASHBOARD_BACKEND/api_sales_performance.php?${params}`
    );
    if (!res.ok) {
      console.error("Sales endpoint error:", res.status, res.statusText);
      return [];
    }
    const data = await res.json();
    console.log("Sales Data:", data);
    return data;
  } catch (error) {
    console.error("Fetch sales data error:", error);
    return [];
  }
}

function renderStockPieChart(data) {
  const canvas = document.getElementById("stockPieChart");
  if (!canvas) {
    console.error("stockPieChart canvas not found");
    return;
  }
  const ctx = canvas.getContext("2d");
  const labels = data.map((d) => d.category);
  const quantities = data.map((d) => d.quantity);

  if (stockPieChart) stockPieChart.destroy();
  stockPieChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels,
      datasets: [
        {
          data: quantities,
          backgroundColor: [
            "#4caf50",
            "#ff9800",
            "#2196f3",
            "#e91e63",
            "#9c27b0",
          ],
        },
      ],
    },
    options: {
      plugins: {
        legend: {
          display: true,
          position: "bottom", // <-- This moves the legend under the chart
          labels: {
            boxWidth: 20,
            padding: 16,
          },
        },
      },
    },
  });
}

function renderSalesLineChart(data) {
  const canvas = document.getElementById("salesLineChart");
  if (!canvas) {
    console.error("salesLineChart canvas not found");
    return;
  }
  const ctx = canvas.getContext("2d");
  const labels = data.map((d) => d.date);
  const sales = data.map((d) => d.total_sales);

  if (salesLineChart) salesLineChart.destroy();
  salesLineChart = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Sales",
          data: sales,
          borderColor: "#4caf50",
          fill: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
  });
}

async function populateSalesCategoryDropdown() {
  const select = document.getElementById("salesCategoryFilter");
  try {
    const res = await fetch(
      "../PAMO_DASHBOARD_BACKEND/api_inventory_categories.php"
    );
    if (!res.ok) throw new Error("Failed to fetch categories");
    const categories = await res.json();
    select.innerHTML = '<option value="">All</option>';
    categories.forEach((cat) => {
      const opt = document.createElement("option");
      opt.value = cat;
      opt.textContent = cat;
      select.appendChild(opt);
    });
  } catch (e) {
    console.error("Error loading sales categories:", e);
  }
}

async function populateSalesCourseDropdown() {
  const select = document.getElementById("salesCourseFilter");
  try {
    const res = await fetch("../PAMO_DASHBOARD_BACKEND/api_courses.php");
    if (!res.ok) throw new Error("Failed to fetch courses");
    const courses = await res.json();
    select.innerHTML = '<option value="">All</option>';
    courses.forEach((course) => {
      const opt = document.createElement("option");
      opt.value = course;
      opt.textContent = course;
      select.appendChild(opt);
    });
  } catch (e) {
    console.error("Error loading sales courses:", e);
  }
}

// --- SALES ANALYTICS ---
async function updateSalesAnalytics() {
  const category = document.getElementById("salesCategoryFilter").value;
  const courseSelect = document.getElementById("salesCourseFilter");
  let course = "";
  if (category === "Tertiary-Uniform") {
    course = courseSelect.value;
    courseSelect.disabled = false;
  } else {
    courseSelect.value = "";
    courseSelect.disabled = true;
  }
  const period = document.getElementById("salesPeriodFilter").value;
  const salesData = await fetchSalesData(category, course, period);
  renderSalesLineChart(salesData);
}

// --- INVENTORY ANALYTICS (always overview) ---
async function updateInventoryAnalytics() {
  const stockData = await fetchStockData();
  renderStockPieChart(stockData);
}

function handleSalesCategoryChange() {
  const category = document.getElementById("salesCategoryFilter").value;
  const courseSelect = document.getElementById("salesCourseFilter");
  if (category === "Tertiary-Uniform") {
    courseSelect.disabled = false;
  } else {
    courseSelect.value = "";
    courseSelect.disabled = true;
  }
  updateSalesAnalytics();
}

window.addEventListener("DOMContentLoaded", async () => {
  await Promise.all([
    populateSalesCategoryDropdown(),
    populateSalesCourseDropdown(),
  ]);
  handleSalesCategoryChange();
  document
    .getElementById("salesCategoryFilter")
    .addEventListener("change", handleSalesCategoryChange);
  document
    .getElementById("salesCourseFilter")
    .addEventListener("change", updateSalesAnalytics);
  document
    .getElementById("salesPeriodFilter")
    .addEventListener("change", updateSalesAnalytics);
  updateSalesAnalytics();
  updateInventoryAnalytics();
});
