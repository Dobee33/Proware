document.addEventListener("DOMContentLoaded", function () {
  // Function to update total amount and cart items
  function updateTotalAmount() {
    const rows = document.querySelectorAll(".products-table tbody tr");
    let total = 0;
    const selectedCartItems = [];

    rows.forEach((row) => {
      const checkbox = row.querySelector(".include-checkbox");
      if (checkbox && checkbox.checked) {
        const price = parseFloat(
          row
            .querySelector("td:nth-child(4)")
            .textContent.replace("₱", "")
            .replace(/,/g, "")
            .trim()
        );
        const quantity = parseInt(
          row.querySelector(".item-quantity").textContent
        );

        // Get item details for the cart
        const itemName = row.querySelector("td:nth-child(2)").textContent;
        const size = row.querySelector("td:nth-child(3)").textContent;
        const imagePath = row
          .querySelector("td:nth-child(1) img")
          .getAttribute("src");
        const itemId = row.dataset.itemId;
        const itemCode = row.dataset.itemCode;

        // Add to selected items array
        selectedCartItems.push({
          id: itemId,
          item_code: itemCode,
          item_name: itemName,
          size: size,
          price: price,
          quantity: quantity,
          image_path: imagePath,
        });

        total += price * quantity;
      }
    });

    // Update the total amount display with proper formatting
    const totalAmountCell = document.querySelector(".total-table td");
    if (totalAmountCell) {
      totalAmountCell.textContent =
        "₱" + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Update hidden inputs for form submission
    const totalAmountInput = document.getElementById("totalAmountInput");
    if (totalAmountInput) {
      totalAmountInput.value = total;
    }

    // Update included items and cart items for form submission
    const includedItems = selectedCartItems.map((item) => item.id);

    const includedItemsInput = document.getElementById("includedItems");
    if (includedItemsInput) {
      includedItemsInput.value = JSON.stringify(includedItems);
    }

    const cartItemsInput = document.getElementById("cartItemsInput");
    if (cartItemsInput) {
      cartItemsInput.value = JSON.stringify(selectedCartItems);
    }
  }

  // Add event listeners to checkboxes
  document.querySelectorAll(".include-checkbox").forEach((checkbox) => {
    checkbox.addEventListener("change", updateTotalAmount);
  });

  // Initial calculation
  updateTotalAmount();
});
