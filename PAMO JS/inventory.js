function handleAddQuantity() {
  if (!selectedItemCode) {
    alert("Please select an item first");
    return;
  }
  addQuantity(selectedItemCode);
}

function editPrice(itemCode, currentPrice) {
  console.log("Edit Price clicked:", itemCode, currentPrice);
  document.getElementById("itemId").value = itemCode;
  document.getElementById("newPrice").value = currentPrice;
  document.getElementById("editPriceModal").style.display = "block";
}

function addQuantity(itemCode) {
  document.getElementById("quantityItemId").value = itemCode;
  document.getElementById("quantityToAdd").value = "";
  document.getElementById("addQuantityModal").style.display = "block";
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = "none";
}

function updatePrice() {
  const itemCode = document.getElementById("itemId").value;
  const newPrice = document.getElementById("newPrice").value;

  console.log("Updating price:", itemCode, newPrice);

  if (!newPrice || newPrice <= 0) {
    alert("Please enter a valid price");
    return;
  }

  fetch("../PAMO Inventory backend/update_price.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `item_code=${encodeURIComponent(itemCode)}&price=${encodeURIComponent(
      newPrice
    )}`,
  })
    .then((response) => {
      console.log("Response received");
      return response.json();
    })
    .then((data) => {
      console.log("Data:", data);
      if (data.success) {
        alert("Price updated successfully!");
        location.reload();
      } else {
        alert("Error updating price: " + (data.message || "Unknown error"));
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error updating price: " + error);
    });

  closeModal("editPriceModal");
}

function updateQuantity() {
  const itemCode = document.getElementById("quantityItemId").value;
  const quantityToAdd = document.getElementById("quantityToAdd").value;

  fetch("../PAMO Inventory backend/update_quantity.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `item_code=${itemCode}&quantity=${quantityToAdd}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Quantity updated successfully!");
        const row = document.querySelector(`tr[data-item-code="${itemCode}"]`);
        if (row) {
          updateStockStatus(row);
        }
        location.reload();
      } else {
        alert("Error updating quantity: " + data.message);
      }
    })
    .catch((error) => {
      alert("Error updating quantity: " + error);
    });

  closeModal("addQuantityModal");
}

function searchItems() {
  const searchInput = document.getElementById("searchInput");
  const searchTerm = searchInput.value.toLowerCase();
  const tableRows = document.querySelectorAll(".inventory-table tbody tr");

  tableRows.forEach((row) => {
    const itemName = row
      .querySelector("td:nth-child(2)")
      .textContent.toLowerCase();
    const category = row
      .querySelector("td:nth-child(3)")
      .textContent.toLowerCase();
    const itemCode = row
      .querySelector("td:nth-child(1)")
      .textContent.toLowerCase();

    if (
      itemName.includes(searchTerm) ||
      category.includes(searchTerm) ||
      itemCode.includes(searchTerm)
    ) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

function updateStockStatus(row) {
  const actualQuantity = parseInt(
    row.querySelector("td:nth-child(4)").textContent
  );
  const statusCell = row.querySelector("td:nth-child(7)");

  let status, statusClass;

  if (actualQuantity <= 0) {
    status = "Out of Stock";
    statusClass = "status-out-of-stock";
  } else if (actualQuantity <= 20) {
    status = "Low Stock";
    statusClass = "status-low-stock";
  } else {
    status = "In Stock";
    statusClass = "status-in-stock";
  }

  statusCell.classList.remove(
    "status-in-stock",
    "status-low-stock",
    "status-out-of-stock"
  );
  statusCell.classList.add(statusClass);
  statusCell.textContent = status;
}

function applyFilters() {
  const searchTerm = document.getElementById("searchInput").value.toLowerCase();
  const categoryFilter = document.getElementById("categoryFilter").value;
  const sizeFilter = document.getElementById("sizeFilter").value;
  const statusFilter = document.getElementById("statusFilter").value;

  const rows = document.querySelectorAll(".inventory-table tbody tr");
  const sizeHeader = document.querySelector(
    ".inventory-table thead th:nth-child(5)"
  );

  if (categoryFilter === "STI-Accessories") {
    if (sizeHeader) sizeHeader.style.display = "none";
    rows.forEach((row) => {
      const sizeCell = row.querySelector("td:nth-child(5)");
      if (sizeCell) sizeCell.style.display = "none";
    });
  } else {
    if (sizeHeader) sizeHeader.style.display = "";
    rows.forEach((row) => {
      const sizeCell = row.querySelector("td:nth-child(5)");
      if (sizeCell) sizeCell.style.display = "";
    });
  }

  rows.forEach((row) => {
    const itemName = row
      .querySelector("td:nth-child(2)")
      .textContent.toLowerCase();
    const category = row.querySelector("td:nth-child(3)").textContent;
    const size = row.querySelector("td:nth-child(5)").textContent;
    const status = row.querySelector("td:nth-child(7)").textContent.trim();

    const matchesSearch =
      searchTerm === "" ||
      itemName.includes(searchTerm) ||
      category.toLowerCase().includes(searchTerm);
    const matchesCategory =
      categoryFilter === "" || category === categoryFilter;
    const matchesSize = sizeFilter === "" || size === sizeFilter;
    const matchesStatus =
      statusFilter === "" ||
      status.toLowerCase() === statusFilter.toLowerCase();

    if (matchesSearch && matchesCategory && matchesSize && matchesStatus) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

function clearAllFilters() {
  document.getElementById("categoryFilter").value = "";
  document.getElementById("sizeFilter").value = "";
  document.getElementById("statusFilter").value = "";
  document.getElementById("searchInput").value = "";

  const sizeHeader = document.querySelector(
    ".inventory-table thead th:nth-child(5)"
  );
  if (sizeHeader) sizeHeader.style.display = "";

  const rows = document.querySelectorAll(".inventory-table tbody tr");
  rows.forEach((row) => {
    const sizeCell = row.querySelector("td:nth-child(5)");
    if (sizeCell) sizeCell.style.display = "";
  });

  applyFilters();
}

function populateFilters() {
  const sizeFilter = document.getElementById("sizeFilter");
  const sizeOrder = [
    "XS",
    "S",
    "M",
    "L",
    "XL",
    "XXL",
    "3XL",
    "4XL",
    "5XL",
    "6XL",
    "7XL",
    "One Size",
  ];

  // Get all unique sizes from the table
  const tableRows = document.querySelectorAll(".inventory-table tbody tr");
  const uniqueSizes = new Set();
  tableRows.forEach((row) => {
    const size = row.querySelector("td:nth-child(5)").textContent.trim();
    if (size) uniqueSizes.add(size);
  });

  // Keep the "All Sizes" option
  const allSizesOption = sizeFilter.querySelector('option[value=""]');
  sizeFilter.innerHTML = "";
  if (allSizesOption) sizeFilter.appendChild(allSizesOption);

  // Add sizes in the defined order if they exist in the inventory
  sizeOrder.forEach((size) => {
    if (uniqueSizes.has(size)) {
      const option = document.createElement("option");
      option.value = size;
      option.textContent = size;
      sizeFilter.appendChild(option);
    }
  });
}

// Call populateFilters when page loads
document.addEventListener("DOMContentLoaded", populateFilters);

// Add event listeners for all filters
document.getElementById("searchInput").addEventListener("input", applyFilters);
document
  .getElementById("categoryFilter")
  .addEventListener("change", applyFilters);
document.getElementById("sizeFilter").addEventListener("change", applyFilters);
document
  .getElementById("statusFilter")
  .addEventListener("change", applyFilters);

// Call applyFilters on page load
document.addEventListener("DOMContentLoaded", function () {
  applyFilters();
});

// Add this function to initialize the size column visibility
function initializeSizeColumnVisibility() {
  const categoryFilter = document.getElementById("categoryFilter").value;
  if (categoryFilter === "STI-Accessories") {
    applyFilters();
  }
}

function logout() {
  // Show confirmation dialog before logging out
  if (confirm("Are you sure you want to log out?")) {
    window.location.href = "../Pages/login.php";
  }
}

function saveEdit() {
  const itemCode = document.getElementById("editItemId").value;
  const newPrice = document.getElementById("editPrice").value;

  // Add logic to save the edited item details
  // You can send the updated data to the server using fetch or AJAX

  closeModal("editItemModal");
}

function editImage() {
  // Logic to edit image (e.g., open a file input or modal)
  console.log("Edit image functionality to be implemented.");
}

function showEditPriceModal() {
  document.getElementById("priceItemId").value =
    document.getElementById("editItemId").value; // Set the item ID
  document.getElementById("newPrice").value =
    document.getElementById("editPrice").value; // Set current price
  document.getElementById("editPriceModal").style.display = "block"; // Show the modal
}

function showEditImageModal() {
  document.getElementById("imageItemId").value =
    document.getElementById("editItemId").value; // Set the item ID
  document.getElementById("editImageModal").style.display = "block"; // Show the modal
}

function submitEditPrice() {
  const itemId = document.getElementById("priceItemId").value;
  const newPrice = document.getElementById("newPrice").value;

  fetch("../PAMO Inventory backend/edit_price.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ itemId, newPrice }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update the UI
        updatePriceDisplay(itemId, newPrice);
        showMessage(`Updated price for item ${itemId} to ${newPrice}.`);
        closeModal("editPriceModal");
        // Clear input field
        document.getElementById("newPrice").value = "";
      } else {
        alert("Error updating price");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function submitEditImage() {
  const itemId = document.getElementById("imageItemId").value;
  const newImage = document.getElementById("newImage").files[0];

  const formData = new FormData();
  formData.append("itemId", itemId);
  formData.append("newImage", newImage);

  fetch("../PAMO Inventory backend/edit_image.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showMessage(`Updated image for item ${itemId}.`);
        closeModal("editImageModal");
        // Clear input field
        document.getElementById("newImage").value = "";
      } else {
        alert("Error updating image");
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Function to show a message
function showMessage(message) {
  const messageBox = document.createElement("div");
  messageBox.className = "message-box";
  messageBox.innerText = message;
  document.body.appendChild(messageBox);

  // Automatically remove the message after a few seconds
  setTimeout(() => {
    messageBox.remove();
  }, 3000);
}

// Function to update inventory display
function updateInventoryDisplay(itemId, quantity, action) {
  const row = document.querySelector(`tr[data-item-code="${itemId}"]`);
  if (row) {
    const quantityCell = row.cells[3]; // 4th cell is Actual Quantity
    let currentQuantity = parseInt(quantityCell.textContent);
    if (action === "add") {
      quantityCell.textContent = currentQuantity + parseInt(quantity);
    } else if (action === "deduct") {
      quantityCell.textContent = currentQuantity - parseInt(quantity);
    }
    // Update the stock status immediately after changing the quantity
    updateStockStatus(row);
  }
}

// Function to update price display
function updatePriceDisplay(itemId, newPrice) {
  const row = document.querySelector(`tr[data-item-code="${itemId}"]`);
  if (row) {
    const priceCell = row.cells[5]; // 6th cell is Price
    priceCell.textContent = `₱${parseFloat(newPrice).toFixed(2)}`;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("editBtn").addEventListener("click", handleEdit);
  initializeSizeColumnVisibility();
});
