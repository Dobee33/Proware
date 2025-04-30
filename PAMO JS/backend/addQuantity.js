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

  try {
    const orderNumber = document.getElementById("orderNumber").value;
    const deliveryItems = document.querySelectorAll(".delivery-item");

    if (!orderNumber || orderNumber.trim() === "") {
      alert("Please enter a delivery order number");
      return false;
    }

    if (!deliveryItems || deliveryItems.length === 0) {
      alert("Please add at least one item");
      return false;
    }

    const formData = new FormData();
    formData.append("orderNumber", orderNumber);

    const itemIds = [];
    const quantities = [];

    for (let i = 0; i < deliveryItems.length; i++) {
      const item = deliveryItems[i];
      const itemSelect = item.querySelector('select[name="itemId[]"]');
      const quantityInput = item.querySelector('input[name="quantityToAdd[]"]');

      if (!itemSelect || !quantityInput) {
        alert(`Invalid form elements for item ${i + 1}`);
        return false;
      }

      if (!itemSelect.value) {
        alert(`Please select a product for item ${i + 1}`);
        itemSelect.focus();
        return false;
      }

      const quantity = parseInt(quantityInput.value);
      if (!quantity || quantity <= 0) {
        alert(`Please enter a valid quantity greater than 0 for item ${i + 1}`);
        quantityInput.focus();
        return false;
      }

      if (itemIds.includes(itemSelect.value)) {
        alert(
          `Duplicate product selected for item ${
            i + 1
          }. Please select different products.`
        );
        itemSelect.focus();
        return false;
      }

      itemIds.push(itemSelect.value);
      quantities.push(quantity);
    }

    itemIds.forEach((id, index) => {
      formData.append("itemId[]", id);
      formData.append("quantityToAdd[]", quantities[index]);
    });

    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "Processing...";
    }

    fetch("../PAMO Inventory backend/process_add_quantity.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Failed to process request");
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          alert("Delivery recorded successfully!");
          closeModal("addQuantityModal");
          location.reload();
        } else {
          throw new Error(data.message || "Failed to record delivery");
        }
      })
      .catch((error) => {
        alert(error.message);
      })
      .finally(() => {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = "Record Delivery";
        }
      });
  } catch (error) {
    alert("An error occurred while processing your request");
    return false;
  }
}

// Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // Add click handlers to existing close buttons
  const closeButtons = document.querySelectorAll(".item-close");
  if (closeButtons) {
    closeButtons.forEach((btn) => {
      if (btn) {
        btn.onclick = function () {
          removeDeliveryItem(this);
        };
      }
    });
  }

  // Add change event listeners to all select elements
  const selectElements = document.querySelectorAll('select[name="itemId[]"]');
  if (selectElements) {
    selectElements.forEach((select) => {
      if (select) {
        select.addEventListener("change", function () {
          validateProductSelection(this);
        });
      }
    });
  }
});
