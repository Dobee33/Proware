// New Delivery Modal Backend Functions

function showAddQuantityModal() {
  document.getElementById("addQuantityModal").style.display = "block";
  document.getElementById("addQuantityForm").reset();
  // Reset all select elements to their default state
  document.querySelectorAll('select[name="itemId[]"]').forEach((select) => {
    select.value = "";
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

  // Add change event listener to the new select element
  if (select) {
    select.addEventListener("change", function () {
      validateProductSelection(this);
    });
  }

  deliveryItems.appendChild(newItem);
}

function removeDeliveryItem(closeButton) {
  const deliveryItems = document.getElementById("deliveryItems");
  const items = deliveryItems.querySelectorAll(".delivery-item");

  if (items.length > 1) {
    const deliveryItem = closeButton.closest(".delivery-item");
    deliveryItem.remove();
    // Revalidate all remaining select elements
    document.querySelectorAll('select[name="itemId[]"]').forEach((select) => {
      validateProductSelection(select);
    });
  }
}

function validateProductSelection(selectElement) {
  const selectedValue = selectElement.value;
  if (!selectedValue) return;

  const allSelects = document.querySelectorAll('select[name="itemId[]"]');
  let duplicateFound = false;

  allSelects.forEach((select) => {
    if (select !== selectElement && select.value === selectedValue) {
      duplicateFound = true;
    }
  });

  if (duplicateFound) {
    alert(
      "This product has already been selected. Please choose a different product."
    );
    selectElement.value = "";
  }
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

  fetch("../PAMO Inventory backend/process_add_quantity.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
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
      alert("An error occurred while processing your request");
    });
}

// Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // Add click handlers to existing close buttons
  const closeButtons = document.querySelectorAll(".item-close");
  closeButtons.forEach((btn) => {
    btn.onclick = function () {
      removeDeliveryItem(this);
    };
  });

  // Add change event listeners to all select elements
  document.querySelectorAll('select[name="itemId[]"]').forEach((select) => {
    select.addEventListener("change", function () {
      validateProductSelection(this);
    });
  });
});
