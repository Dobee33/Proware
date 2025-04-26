// Sales Entry Modal Backend Functions

function showDeductQuantityModal() {
  document.getElementById("deductItemId").value =
    document.getElementById("editItemId").value;
  document.getElementById("deductQuantityModal").style.display = "block";
}

function updateItemDetails() {
  const itemSelect = document.getElementById("deductItemId");
  const selectedOption = itemSelect.options[itemSelect.selectedIndex];
  const price = selectedOption.getAttribute("data-price");

  if (price) {
    document.getElementById("pricePerItem").value = price;
    calculateTotal();
  }
}

function calculateTotal() {
  const quantity =
    parseFloat(document.getElementById("quantityToDeduct").value) || 0;
  const price = parseFloat(document.getElementById("pricePerItem").value) || 0;
  const total = quantity * price;

  document.getElementById("totalAmount").value = total.toFixed(2);
}

function submitDeductQuantity(event) {
  event.preventDefault();

  const transactionNumber = document.getElementById("transactionNumber").value;
  const itemId = document.getElementById("deductItemId").value;
  const size = document.getElementById("size").value;
  const quantityToDeduct = document.getElementById("quantityToDeduct").value;
  const pricePerItem = document.getElementById("pricePerItem").value;
  const totalAmount = document.getElementById("totalAmount").value;

  if (
    !transactionNumber ||
    !itemId ||
    !size ||
    !quantityToDeduct ||
    !pricePerItem
  ) {
    alert("Please fill in all required fields");
    return;
  }

  const formData = new FormData();
  formData.append("transactionNumber", transactionNumber);
  formData.append("itemId", itemId);
  formData.append("size", size);
  formData.append("quantityToDeduct", quantityToDeduct);
  formData.append("pricePerItem", pricePerItem);
  formData.append("totalAmount", totalAmount);

  fetch("../PAMO Inventory backend/process_deduct_quantity.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Sale recorded successfully!");
        closeModal("deductQuantityModal");
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

// Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // Add event listeners for total calculation
  const quantityInput = document.getElementById("quantityToDeduct");
  const priceInput = document.getElementById("pricePerItem");

  if (quantityInput) {
    quantityInput.addEventListener("input", calculateTotal);
  }

  if (priceInput) {
    priceInput.addEventListener("input", calculateTotal);
  }

  // Add event listener for item selection
  const itemSelect = document.getElementById("deductItemId");
  if (itemSelect) {
    itemSelect.addEventListener("change", updateItemDetails);
  }
});
