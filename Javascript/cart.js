// Cart Popup Functionality
function updateCartPopup() {
  fetch("../Includes/cart_operations.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "action=get_cart",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const cartItems = document.querySelector(".cart-items");
        if (cartItems) {
          if (data.cart_items && data.cart_items.length > 0) {
            cartItems.innerHTML = data.cart_items
              .map(
                (item) => `
                        <div class="cart-item">
                            <img src="${item.image_path}" alt="${
                  item.item_name
                }">
                            <div class="cart-item-details">
                                <div class="cart-item-name">${
                                  item.item_name
                                }</div>
                                <div class="cart-item-info">
                                    ${
                                      item.size
                                        ? `<div class="cart-item-size">Size: ${item.size}</div>`
                                        : ""
                                    }
                                    <div class="cart-item-price">₱${
                                      item.price
                                    } × ${item.quantity}</div>
                                </div>
                            </div>
                        </div>
                    `
              )
              .join("");
          } else {
            cartItems.innerHTML =
              '<p class="empty-cart-message">Your cart is empty</p>';
          }
        }

        // Update cart count
        const cartCount = document.querySelector(".cart-count");
        if (cartCount) {
          cartCount.textContent = data.cart_count;
        }
      }
    })
    .catch((error) => console.error("Error:", error));
}

// Initialize cart popup functionality
document.addEventListener("DOMContentLoaded", function () {
  // Setup cart icon click handler
  const cartIcon = document.querySelector(".cart-icon");
  const cartPopup = document.querySelector(".cart-popup");

  if (cartIcon && cartPopup) {
    cartIcon.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      cartPopup.classList.toggle("active");
      updateCartPopup(); // Update cart contents when opened
    });

    // Close cart popup when clicking outside
    document.addEventListener("click", function (e) {
      if (!cartPopup.contains(e.target) && !cartIcon.contains(e.target)) {
        cartPopup.classList.remove("active");
      }
    });
  }

  // Initial cart update
  updateCartPopup();
});
