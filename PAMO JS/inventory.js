let selectedItemCode = null;
let selectedPrice = null;

function selectRow(row, itemCode, price) {
  if (selectedItemCode === itemCode) {
    row.classList.remove("selected");
    selectedItemCode = null;
    selectedPrice = null;
    document.getElementById("editBtn").disabled = true;
    return;
  }

  document.querySelectorAll(".inventory-table tbody tr").forEach((tr) => {
    tr.classList.remove("selected");
  });

  row.classList.add("selected");
  selectedItemCode = itemCode;
  selectedPrice = price;
  document.getElementById("editBtn").disabled = false;
}

function handleEdit() {
  console.log("Edit button clicked");
  console.log("Selected Item Code:", selectedItemCode);

  if (!selectedItemCode) {
    alert("Please select an item first");
    return;
  }

  const row = document.querySelector(
    `tr[data-item-code="${selectedItemCode}"]`
  );

  if (!row) {
    alert("Selected item not found in the table.");
    return;
  }

  document.getElementById("editItemId").value = selectedItemCode;
  document.getElementById("editItemCode").value = row.cells[0].textContent;
  document.getElementById("editItemName").value = row.cells[1].textContent;
  document.getElementById("editCategory").value = row.cells[2].textContent;
  document.getElementById("editActualQuantity").value =
    row.cells[3].textContent;
  document.getElementById("editSize").value = row.cells[7].textContent;
  document.getElementById("editPrice").value = row.cells[9].textContent;
  document.getElementById("editItemModal").style.display = "block";
}

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

function showAddItemModal() {
  document.getElementById("addItemModal").style.display = "flex";
  document.getElementById("addItemForm").reset();
}

function submitNewItem(event) {
  event.preventDefault();

  const quantity = parseInt(document.getElementById("newItemQuantity").value);
  const damage = parseInt(document.getElementById("newItemDamage").value) || 0;
  const form = document.getElementById("addItemForm");
  const formData = new FormData(form);

  if (
    !formData.get("newItemCode") ||
    !formData.get("newCategory") ||
    !formData.get("newItemName") ||
    !formData.get("newSize") ||
    isNaN(formData.get("newItemPrice")) ||
    isNaN(formData.get("newItemQuantity"))
  ) {
    alert("Please fill in all required fields with valid values");
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../PAMO Inventory backend/add_item.php", true);
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          const data = JSON.parse(xhr.responseText);
          if (data.success) {
            alert("New product added successfully!");
            location.reload();
          } else {
            throw new Error(data.message || "Unknown error");
          }
        } catch (e) {
          console.error("Parse error:", e);
          alert("Error adding product: " + xhr.responseText);
        }
      } else {
        alert("Error: " + xhr.statusText);
      }
    }
  };

  xhr.send(formData);
}

document.getElementById("newCategory").addEventListener("change", function () {
  const sizeGroup = document.querySelector(".input-group:has(#newSize)");
  if (this.value === "STI-Accessories") {
    sizeGroup.style.display = "none";
    document.getElementById("newSize").value = "One Size";
    document.getElementById("newSize").removeAttribute("required");
  } else {
    sizeGroup.style.display = "block";
    document.getElementById("newSize").setAttribute("required", "required");
  }
});

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
  const statusCell = row.querySelector("td:nth-child(11)");

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
    ".inventory-table thead th:nth-child(8)"
  );

  if (categoryFilter === "STI-Accessories") {
    if (sizeHeader) sizeHeader.style.display = "none";
    rows.forEach((row) => {
      const sizeCell = row.querySelector("td:nth-child(8)");
      if (sizeCell) sizeCell.style.display = "none";
    });
  } else {
    if (sizeHeader) sizeHeader.style.display = "";
    rows.forEach((row) => {
      const sizeCell = row.querySelector("td:nth-child(8)");
      if (sizeCell) sizeCell.style.display = "";
    });
  }

  rows.forEach((row) => {
    const itemName = row
      .querySelector("td:nth-child(2)")
      .textContent.toLowerCase();
    const category = row.querySelector("td:nth-child(3)").textContent;
    const size = row.querySelector("td:nth-child(8)").textContent;
    const status = row.querySelector("td:nth-child(11)").textContent.trim();

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
    ".inventory-table thead th:nth-child(8)"
  );
  if (sizeHeader) sizeHeader.style.display = "";

  const rows = document.querySelectorAll(".inventory-table tbody tr");
  rows.forEach((row) => {
    const sizeCell = row.querySelector("td:nth-child(8)");
    if (sizeCell) sizeCell.style.display = "";
  });

  applyFilters();
}

function populateFilters() {
  const sizeFilter = document.getElementById("sizeFilter");
  const sizes = [
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
  ];
  const categories = [
    "Tertiary-Uniform",
    "SHS-Uniform",
    "STI-Shirts",
    "STI-Accessories",
    "SHS-PE",
    "Tertiary-PE",
  ];

  // Get all unique sizes from the table
  const tableRows = document.querySelectorAll(".inventory-table tbody tr");
  const uniqueSizes = new Set();
  tableRows.forEach((row) => {
    const size = row.querySelector("td:nth-child(8)").textContent.trim();
    if (size) uniqueSizes.add(size);
  });

  // Keep the "All Sizes" option
  const allSizesOption = sizeFilter.querySelector('option[value=""]');
  sizeFilter.innerHTML = "";
  sizeFilter.appendChild(allSizesOption);

  // Add unique sizes from the table
  Array.from(uniqueSizes)
    .sort()
    .forEach((size) => {
      const option = document.createElement("option");
      option.value = size;
      option.textContent = size;
      sizeFilter.appendChild(option);
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
  // Redirect to logout.php
  window.location.href = "../Pages/login.php";
}

function saveEdit() {
  const itemCode = document.getElementById("editItemId").value;
  const newPrice = document.getElementById("editPrice").value;

  // Add logic to save the edited item details
  // You can send the updated data to the server using fetch or AJAX

  closeModal("editItemModal");
}

function addQuantity() {
  const itemCode = document.getElementById("editItemId").value;
  const quantityToAdd = prompt("Enter quantity to add:");
  if (quantityToAdd) {
    // Logic to add quantity
    console.log(`Adding ${quantityToAdd} to item ${itemCode}`);
    // You can implement the AJAX call to update the quantity in the database
  }
}

function deductQuantity() {
  const itemCode = document.getElementById("editItemId").value;
  const quantityToDeduct = prompt("Enter quantity to deduct:");
  if (quantityToDeduct) {
    // Logic to deduct quantity
    console.log(`Deducting ${quantityToDeduct} from item ${itemCode}`);
    // You can implement the AJAX call to update the quantity in the database
  }
}

function editPrice() {
  const newPrice = prompt("Enter new price:");
  if (newPrice) {
    document.getElementById("editPrice").value = newPrice;
    console.log(`New price set to ${newPrice}`);
    // You can implement the AJAX call to update the price in the database
  }
}

function editImage() {
  // Logic to edit image (e.g., open a file input or modal)
  console.log("Edit image functionality to be implemented.");
}

function showAddQuantityModal() {
  document.getElementById("addQuantityModal").style.display = "block";
  document.getElementById("addQuantityForm").reset();
}

function submitAddQuantity(event) {
  event.preventDefault();

  const orderNumber = document.getElementById("orderNumber").value;
  const deliveryItems = document.querySelectorAll(".delivery-item");

  if (!orderNumber || deliveryItems.length === 0) {
    alert("Please fill in all required fields");
    return;
  }

  const formData = new FormData();
  formData.append("orderNumber", orderNumber);

  // Arrays to store multiple items
  const itemIds = [];
  const quantities = [];

  // Validate and collect all items data
  let hasErrors = false;
  deliveryItems.forEach((item, index) => {
    const itemSelect = item.querySelector('select[name="itemId[]"]');
    const quantityInput = item.querySelector('input[name="quantityToAdd[]"]');

    if (
      !itemSelect ||
      !quantityInput ||
      !itemSelect.value ||
      !quantityInput.value
    ) {
      alert(`Please fill in all fields for item ${index + 1}`);
      hasErrors = true;
      return;
    }

    if (parseInt(quantityInput.value) <= 0) {
      alert(`Quantity must be greater than 0 for item ${index + 1}`);
      hasErrors = true;
      return;
    }

    itemIds.push(itemSelect.value);
    quantities.push(quantityInput.value);
  });

  if (hasErrors) {
    return;
  }

  // Append arrays to FormData
  itemIds.forEach((id, index) => {
    formData.append("itemId[]", id);
    formData.append("quantityToAdd[]", quantities[index]);
  });

  // Debug: Log the data being sent
  console.log("Form Data Contents:");
  for (let pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }

  fetch("../PAMO Inventory backend/process_add_quantity.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text()) // First get the raw response
    .then((text) => {
      console.log("Raw server response:", text); // Log the raw response
      return JSON.parse(text); // Then parse it as JSON
    })
    .then((data) => {
      if (data.success) {
        alert("Delivery recorded successfully!");
        closeModal("addQuantityModal");
        location.reload();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(
        "An error occurred while processing your request. Check console for details."
      );
    });
}

function addDeliveryItem() {
  const deliveryItems = document.getElementById("deliveryItems");
  const newItem = deliveryItems.querySelector(".delivery-item").cloneNode(true);

  // Reset the values in the cloned item
  const select = newItem.querySelector('select[name="itemId[]"]');
  const input = newItem.querySelector('input[name="quantityToAdd[]"]');
  if (select) select.value = "";
  if (input) input.value = "";

  // Show close button for the new item
  const closeBtn = newItem.querySelector(".item-close");
  if (closeBtn) {
    closeBtn.style.display = "block";
    closeBtn.onclick = function () {
      removeDeliveryItem(this);
    };
  }

  deliveryItems.appendChild(newItem);
}

function removeDeliveryItem(closeButton) {
  const deliveryItems = document.getElementById("deliveryItems");
  const items = deliveryItems.querySelectorAll(".delivery-item");

  if (items.length > 1) {
    const deliveryItem = closeButton.closest(".delivery-item");
    deliveryItem.remove();
  }
}

// Add click handlers to existing close buttons
document.addEventListener("DOMContentLoaded", function () {
  const closeButtons = document.querySelectorAll(".item-close");
  closeButtons.forEach((btn) => {
    btn.onclick = function () {
      removeDeliveryItem(this);
    };
  });
});

function showDeductQuantityModal() {
  document.getElementById("deductItemId").value =
    document.getElementById("editItemId").value; // Set the item ID
  document.getElementById("deductQuantityModal").style.display = "block"; // Show the modal
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

function submitDeductQuantity() {
  const itemId = document.getElementById("deductItemId").value;
  const quantityToDeduct = document.getElementById("quantityToDeduct").value;

  fetch("../PAMO Inventory backend/deduct_quantity.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ itemId, quantityToDeduct }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update the UI
        updateInventoryDisplay(itemId, quantityToDeduct, "deduct");
        showMessage(`Deducted ${quantityToDeduct} from item ${itemId}.`);
        closeModal("deductQuantityModal");
        // Clear input field
        document.getElementById("quantityToDeduct").value = "";
      } else {
        alert("Error deducting quantity");
      }
    })
    .catch((error) => console.error("Error:", error));
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
    const quantityCell = row.cells[3]; // Assuming the quantity is in the 4th cell
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
    const priceCell = row.cells[8]; // Assuming the price is in the 9th cell
    priceCell.textContent = `₱${parseFloat(newPrice).toFixed(2)}`; // Format the price
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("editBtn").addEventListener("click", handleEdit);
  initializeSizeColumnVisibility();
});
