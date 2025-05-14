document.addEventListener("DOMContentLoaded", function () {
  // Function to update total amount and cart items
  function updateTotalAmount() {
    // Detect which layout is visible
    const isMobile = window.matchMedia("(max-width: 768px)").matches;
    let total = 0;
    const selectedCartItems = [];

    if (!isMobile) {
      // Table layout
      const tableRows = document.querySelectorAll(".products-table tbody tr");
      tableRows.forEach((row) => {
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
          const itemName = row.querySelector("td:nth-child(2)").textContent;
          const size = row.querySelector("td:nth-child(3)").textContent;
          const imagePath = row
            .querySelector("td:nth-child(1) img")
            .getAttribute("src");
          const itemId = row.dataset.itemId;
          const itemCode = row.dataset.itemCode;
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
    } else {
      // Mobile card layout
      const cardRows = document.querySelectorAll(".cart-item-card");
      cardRows.forEach((card) => {
        const checkbox = card.querySelector(".include-checkbox-mobile");
        if (checkbox && checkbox.checked) {
          const price = parseFloat(
            card
              .querySelector(".card-item-price")
              .textContent.replace("₱", "")
              .replace(/,/g, "")
              .trim()
          );
          const quantity = parseInt(
            card
              .querySelector(".card-item-quantity")
              .textContent.replace("Qty:", "")
              .trim()
          );
          const itemName = card.querySelector(".card-item-name").textContent;
          const size = card.querySelector(".card-item-size").textContent;
          const imagePath = card
            .querySelector(".card-img-section img")
            .getAttribute("src");
          const itemId = card.dataset.itemId;
          const itemCode = card.dataset.itemCode;
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
    }

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

    // Update "All" checkbox state
    updateSelectAllCheckbox();
  }

  // Function to update "All" checkbox state
  function updateSelectAllCheckbox() {
    const selectAllCheckbox = document.getElementById("selectAllCheckbox");
    const individualCheckboxes = document.querySelectorAll(
      ".products-table tbody .include-checkbox"
    );
    const allChecked = Array.from(individualCheckboxes).every(
      (checkbox) => checkbox.checked
    );

    if (selectAllCheckbox) {
      selectAllCheckbox.checked = allChecked;
    }
  }

  // Add event listeners to checkboxes (table)
  document
    .querySelectorAll(".products-table tbody .include-checkbox")
    .forEach((checkbox) => {
      checkbox.addEventListener("change", updateTotalAmount);
    });
  // Add event listeners to checkboxes (mobile)
  document
    .querySelectorAll(".cart-items-mobile .include-checkbox-mobile")
    .forEach((checkbox) => {
      checkbox.addEventListener("change", updateTotalAmount);
    });

  // Handle select all checkbox (table and mobile)
  const selectAllCheckbox = document.getElementById("selectAllCheckbox");
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      const isChecked = this.checked;
      document
        .querySelectorAll(".products-table tbody .include-checkbox")
        .forEach((checkbox) => {
          checkbox.checked = isChecked;
        });
      document
        .querySelectorAll(".cart-items-mobile .include-checkbox-mobile")
        .forEach((checkbox) => {
          checkbox.checked = isChecked;
        });
      updateTotalAmount();
    });
  }

  // Ensure all checkboxes are checked by default
  document
    .querySelectorAll(".include-checkbox, .include-checkbox-mobile")
    .forEach((checkbox) => {
      checkbox.checked = true;
    });

  // Initial calculation
  updateTotalAmount();
});
