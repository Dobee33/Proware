document.addEventListener("DOMContentLoaded", function () {
  // Function to update total amount
  function updateTotalAmount() {
    let total = 0;
    document.querySelectorAll(".products-table tbody tr").forEach((row) => {
      const price = parseFloat(
        row
          .querySelector("td:nth-child(4)")
          .textContent.replace("₱", "")
          .replace(",", "")
      );
      const quantity = parseInt(row.querySelector(".qty-input").value);
      const isIncluded = row
        .querySelector(".toggle-checkout-btn.check")
        .classList.contains("active");

      if (isIncluded) {
        total += price * quantity;
      }
    });

    // Update total amount display
    document.querySelector(".total-table td").textContent =
      "₱" + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    // Update hidden input
    document.querySelector('input[name="total_amount"]').value = total;
  }

  // Initialize active states
  document.querySelectorAll(".toggle-container").forEach((container) => {
    const checkBtn = container.querySelector(".check");
    const xBtn = container.querySelector(".x");

    checkBtn.classList.add("active");
    xBtn.classList.remove("active");

    // Add click handlers
    checkBtn.addEventListener("click", function () {
      checkBtn.classList.add("active");
      xBtn.classList.remove("active");
      updateTotalAmount();
    });

    xBtn.addEventListener("click", function () {
      xBtn.classList.add("active");
      checkBtn.classList.remove("active");
      updateTotalAmount();
    });
  });

  // Handle quantity changes
  document.querySelectorAll(".qty-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const input = this.parentElement.querySelector(".qty-input");
      const currentValue = parseInt(input.value);

      if (this.classList.contains("plus")) {
        input.value = currentValue + 1;
      } else if (this.classList.contains("minus") && currentValue > 1) {
        input.value = currentValue - 1;
      }

      updateTotalAmount();

      // Update cart in database
      const itemId = input.dataset.itemId;
      updateCartItem(itemId, input.value);
    });
  });

  // Handle direct quantity input
  document.querySelectorAll(".qty-input").forEach((input) => {
    input.addEventListener("change", function () {
      if (this.value < 1) this.value = 1;
      updateTotalAmount();

      // Update cart in database
      const itemId = this.dataset.itemId;
      updateCartItem(itemId, this.value);
    });
  });

  // Function to update cart item
  async function updateCartItem(itemId, quantity) {
    try {
      const response = await fetch("../Includes/cart_operations.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=update&item_id=${itemId}&quantity=${quantity}`,
      });

      const data = await response.json();
      if (!data.success) {
        console.error("Failed to update cart:", data.message);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  // Update included items before form submission
  document
    .getElementById("checkoutForm")
    .addEventListener("submit", function (e) {
      e.preventDefault(); // Prevent default form submission

      const includedItems = [];
      document.querySelectorAll(".products-table tbody tr").forEach((row) => {
        if (
          row
            .querySelector(".toggle-checkout-btn.check")
            .classList.contains("active")
        ) {
          includedItems.push(row.dataset.itemId);
        }
      });

      if (includedItems.length === 0) {
        alert("Please include at least one item for checkout");
        return;
      }

      document.getElementById("includedItems").value =
        JSON.stringify(includedItems);
      this.submit(); // Submit the form if we have included items
    });

  // Initial total calculation
  updateTotalAmount();
});
