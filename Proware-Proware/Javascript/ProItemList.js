document.addEventListener("DOMContentLoaded", function () {
  // Initialize variables
  const productsGrid = document.querySelector(".products-grid");
  const filterInputs = document.querySelectorAll(".filter-group input");
  const applyFiltersBtn = document.querySelector(".apply-btn");
  const sortSelect = document.getElementById("sort-select");
  const searchInput = document.getElementById("search");
  const quantityInputs = document.querySelectorAll(".quantity input");

  // Initialize AOS
  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 800,
      offset: 100,
      once: true,
    });
  }

  // Add these variables at the top of your DOMContentLoaded event
  let activeMainCategories = new Set();
  let activeSubcategories = new Map();
  let currentSearchTerm = "";

  // Add search functionality
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      currentSearchTerm = this.value.toLowerCase().trim();
      applyAllFilters(); // New function to combine search and category filters
    });

    // Add search button functionality
    const searchBtn = document.querySelector(".search-btn");
    if (searchBtn) {
      searchBtn.addEventListener("click", function () {
        searchInput.focus();
        // Trigger the input event to perform the search
        const event = new Event("input");
        searchInput.dispatchEvent(event);
      });
    }
  }

  // Handle category checkboxes
  const categories = document.querySelectorAll(".category-checkbox");

  // Ensure all subcategories are hidden when page loads
  document.querySelectorAll(".subcategory").forEach((sub) => {
    sub.style.display = "none";
  });

  categories.forEach((category) => {
    category.addEventListener("change", function () {
      let subcategoryDiv = document.getElementById(this.id + "-sub");
      subcategoryDiv.style.display = this.checked ? "block" : "none";
    });
  });

  // Mobile sidebar toggle
  if (document.querySelector(".sort-container")) {
    const sidebarToggle = document.createElement("button");
    sidebarToggle.className = "sidebar-toggle";
    sidebarToggle.innerHTML = '<i class="fas fa-filter"></i>';
    document.querySelector(".sort-container").prepend(sidebarToggle);

    sidebarToggle.addEventListener("click", () => {
      document.querySelector(".sidebar").classList.toggle("active");
    });
  }

  // Quantity buttons functionality
  document.querySelectorAll(".qty-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const input = this.parentElement.querySelector("input");
      const currentValue = parseInt(input.value);
      const max = parseInt(input.getAttribute("max"));

      if (this.classList.contains("plus") && currentValue < max) {
        input.value = currentValue + 1;
      } else if (this.classList.contains("minus") && currentValue > 1) {
        input.value = currentValue - 1;
      }
    });
  });

  // Modal functionality
  const modal = document.getElementById("sizeModal");
  const closeBtn = document.getElementsByClassName("close")[0];
  let currentProduct = null;

  // Add event listeners for the close buttons
  document.querySelectorAll(".modal .close").forEach((closeBtn) => {
    closeBtn.addEventListener("click", function () {
      this.closest(".modal").style.display = "none";
    });
  });

  // Close modals when clicking outside
  window.onclick = function (event) {
    if (event.target.classList.contains("modal")) {
      event.target.style.display = "none";
    }
  };

  // Update the add to cart functionality - ONLY trigger on cart icon click
  document.querySelectorAll(".cart").forEach((btn) => {
    // Clear any existing event listeners
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);

    // Add the click event listener
    newBtn.addEventListener("click", function (e) {
      e.preventDefault();
      handleAddToCart(this);
    });
  });

  // Remove Quick View Modal functionality as it's interfering with UI
  document.querySelectorAll(".quick-view-btn").forEach((btn) => {
    // Disable the quick view functionality
    btn.style.display = "none";
  });

  // Get all main category checkboxes
  const mainCategories = document.querySelectorAll(".main-category-header");
  const courseHeaders = document.querySelectorAll(".course-header");

  // Handle main category toggles
  mainCategories.forEach((header) => {
    header.addEventListener("click", function () {
      const subcategories = this.nextElementSibling;
      const icon = this.querySelector("i");
      const category = this.dataset.category;

      // Toggle active class for subcategories
      this.classList.toggle("active");
      if (subcategories) {
        subcategories.classList.toggle("active");

        // Check if this category has direct items
        const directItems = subcategories.querySelector(
          ":scope > .course-items"
        );
        if (directItems) {
          directItems.classList.toggle("hidden");
        }
      }

      // Handle main category filtering
      if (this.classList.contains("active")) {
        icon.style.transform = "rotate(180deg)";
        activeMainCategories.add(category);
      } else {
        icon.style.transform = "rotate(0deg)";
        activeMainCategories.delete(category);
        // Remove any active subcategories for this main category
        activeSubcategories.delete(category);
        // Hide all course items when main category is collapsed
        if (subcategories) {
          const courseItems = subcategories.querySelectorAll(".course-items");
          const courseIcons =
            subcategories.querySelectorAll(".course-header i");
          courseItems.forEach((item) => item.classList.add("hidden"));
          courseIcons.forEach(
            (icon) => (icon.style.transform = "rotate(0deg)")
          );
          // Uncheck all subcategory checkboxes when main category is closed
          subcategories
            .querySelectorAll(".course-filter-checkbox")
            .forEach((cb) => {
              cb.checked = false;
            });
        }
      }

      // Apply all filters
      applyAllFilters();
    });
  });

  document.querySelectorAll(".size-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const selectedSize = this.textContent;
      const card = this.closest(".product-container");
      const prices = card.dataset.prices.split(",");
      const sizes = card.dataset.sizes.split(",");

      const index = sizes.indexOf(selectedSize);
      if (index !== -1) {
        const price = prices[index];
        card.querySelector(".price-range").textContent = `Price: ₱${parseFloat(
          price
        ).toFixed(2)}`;
        card.querySelector(
          ".selected-size"
        ).textContent = `Selected Size: ${selectedSize}`;
      } else {
        card.querySelector(".price-range").textContent = "Price not available";
      }
    });
  });

  // Initial cart popup update
  updateCartPopup();

  // Make sure all close buttons for modals work correctly
  document.querySelectorAll(".modal .close").forEach((closeBtn) => {
    closeBtn.addEventListener("click", function () {
      this.closest(".modal").style.display = "none";
    });
  });

  // Close the accessory modal
  const accessoryModalClose = document.querySelector("#accessoryModal .close");
  if (accessoryModalClose) {
    accessoryModalClose.addEventListener("click", closeAccessoryModal);
  }

  // Add category filtering functionality
  const categoryLinks = document.querySelectorAll(".category-link");
  const productContainers = document.querySelectorAll(".product-container");
  const noResultsMessage = document.getElementById("no-results-message");
  let activeCategory = null;

  categoryLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const category = this.dataset.category;

      // Toggle active state of links
      categoryLinks.forEach((l) => l.classList.remove("active"));

      if (activeCategory === category) {
        // If clicking the same category, show all products
        activeCategory = null;
        showAllProducts();
      } else {
        // Set new active category and filter
        activeCategory = category;
        this.classList.add("active");
        filterProducts(category);
      }
    });
  });

  function filterProducts(category) {
    let visibleCount = 0;

    // Remove animation and directly show/hide products
    productContainers.forEach((container) => {
      const productCategory = container.dataset.category;
      if (productCategory === category) {
        // Will be shown
        container.style.display = "block";
        visibleCount++;
      } else {
        // Will be hidden
        container.style.display = "none";
      }
    });

    // Show/hide no results message without animation
    if (noResultsMessage) {
      if (visibleCount === 0) {
        noResultsMessage.style.display = "flex";
      } else {
        noResultsMessage.style.display = "none";
      }
    }
  }

  function showAllProducts() {
    // Show all products without animation
    productContainers.forEach((container) => {
      container.style.display = "block";
    });

    // Hide no results message without animation
    if (noResultsMessage) {
      noResultsMessage.style.display = "none";
    }
  }

  // Remove previous course-header click logic
  // Add multi-select course filter logic
  document.querySelectorAll(".course-filter-checkbox").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      // Find the main category for this checkbox
      const mainCategoryDiv = this.closest(".category-item");
      const mainCategoryHeader = mainCategoryDiv.querySelector(
        ".main-category-header"
      );
      const mainCategory = mainCategoryHeader.dataset.category;

      // Get all checked subcategories for this main category
      const checked = Array.from(
        mainCategoryDiv.querySelectorAll(".course-filter-checkbox:checked")
      ).map((cb) => cb.value);

      if (checked.length > 0) {
        activeSubcategories.set(mainCategory, checked);
      } else {
        activeSubcategories.delete(mainCategory);
      }

      applyAllFilters();
    });
  });

  // Add multi-select shirt type filter logic for STI Shirt
  document
    .querySelectorAll(".shirt-type-filter-checkbox")
    .forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const mainCategory = "sti-shirt";
        const checked = Array.from(
          document.querySelectorAll(".shirt-type-filter-checkbox:checked")
        ).map((cb) => cb.value);
        if (checked.length > 0) {
          activeSubcategories.set(mainCategory, checked);
          activeMainCategories.add(mainCategory);
        } else {
          activeSubcategories.delete(mainCategory);
        }
        applyAllFilters();
      });
    });

  // Add these helper functions before applyAllFilters
  function normalizeText(text) {
    return text.toLowerCase().trim();
  }

  function containsPartialMatch(source, searchTerm) {
    const words = searchTerm.split(/\s+/);
    return words.every((word) => {
      const normalizedWord = normalizeText(word);
      return normalizeText(source).includes(normalizedWord);
    });
  }

  // Add this new function to combine all filters
  function applyAllFilters() {
    const productContainers = document.querySelectorAll(".product-container");
    let visibleCount = 0;
    const normalizedSearchTerm = normalizeText(currentSearchTerm);
    productContainers.forEach((container) => {
      const productCategory = container.dataset.category.toLowerCase();
      const itemName = container.dataset.itemName.toLowerCase();
      const itemCode = container.dataset.itemCode.toLowerCase();
      const productCourses = container.dataset.courses
        ? container.dataset.courses.toLowerCase().split(",")
        : [];
      const productShirtTypeId = container.dataset.shirtTypeId;
      let matchesCategory = false;
      if (activeMainCategories.size > 0) {
        for (const mainCategory of activeMainCategories) {
          if (productCategory.includes(mainCategory.toLowerCase())) {
            // STI Shirt: filter by shirt type
            if (mainCategory === "sti-shirt") {
              if (
                activeSubcategories.has(mainCategory) &&
                activeSubcategories.get(mainCategory).length > 0
              ) {
                for (const shirtTypeId of activeSubcategories.get(
                  mainCategory
                )) {
                  if (productShirtTypeId === shirtTypeId) {
                    matchesCategory = true;
                    break;
                  }
                }
              } else {
                matchesCategory = true;
              }
            } else {
              // Other categories: filter by course
              if (
                activeSubcategories.has(mainCategory) &&
                activeSubcategories.get(mainCategory).length > 0
              ) {
                for (const courseValue of activeSubcategories.get(
                  mainCategory
                )) {
                  if (
                    productCourses.includes(courseValue.toLowerCase()) ||
                    isProductInCourse(itemName, courseValue.toLowerCase()) ||
                    itemCode.includes(courseValue.toLowerCase())
                  ) {
                    matchesCategory = true;
                    break;
                  }
                }
              } else {
                matchesCategory = true;
              }
            }
          }
          if (matchesCategory) break;
        }
      } else {
        matchesCategory = true;
      }
      const matchesSearch =
        normalizedSearchTerm === "" ||
        containsPartialMatch(itemName, normalizedSearchTerm) ||
        containsPartialMatch(itemCode, normalizedSearchTerm) ||
        containsPartialMatch(productCategory, normalizedSearchTerm);
      const shouldShow = matchesCategory && matchesSearch;
      container.style.display = shouldShow ? "block" : "none";
      if (shouldShow) visibleCount++;
    });
    const noResultsMessage = document.getElementById("no-results-message");
    if (noResultsMessage) {
      noResultsMessage.style.display = visibleCount === 0 ? "flex" : "none";
    }
  }

  // Function to determine if a product belongs to a specific course
  function isProductInCourse(productName, courseValue) {
    // Normalize: lowercase, replace dashes/underscores with spaces, trim
    const normalize = (str) =>
      str.toLowerCase().replace(/[-_]/g, " ").replace(/\s+/g, " ").trim();
    const normalizedProductName = normalize(productName);
    const normalizedCourseValue = normalize(courseValue);

    // Special handling for STI Shirt subcategories
    if (
      normalizedCourseValue === "Anniversary Shirt" ||
      normalizedCourseValue === "T SHIRT WASHDAY" ||
      normalizedCourseValue === "NSTP Shirt"
    ) {
      return normalizedProductName.includes(normalizedCourseValue);
    }

    // Only match if the normalized product name or item code matches the normalized course value
    return normalizedProductName === normalizedCourseValue;
  }

  // Remove clear filter logic for checkboxes
  const clearFiltersBtn = document.getElementById("clearFiltersBtn");
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", function () {
      // Uncheck all course checkboxes
      document
        .querySelectorAll(".course-filter-checkbox")
        .forEach((cb) => (cb.checked = false));
      // Collapse all main categories
      document
        .querySelectorAll(".main-category-header.active")
        .forEach((header) => {
          header.classList.remove("active");
          const subcategories = header.nextElementSibling;
          if (subcategories) subcategories.classList.remove("active");
          const icon = header.querySelector("i");
          if (icon) icon.style.transform = "rotate(0deg)";
        });
      // Clear activeMainCategories and activeSubcategories
      activeMainCategories.clear();
      activeSubcategories.clear();
      // Reset search input if you have one
      if (searchInput) searchInput.value = "";
      currentSearchTerm = "";
      // Apply all filters
      applyAllFilters();
    });
  }
});

// Handle cart interaction - ONLY processing cart icon clicks
function handleAddToCart(element) {
  // Verify this is a cart element
  if (!element.classList.contains("cart")) {
    return;
  }

  // Check if user is logged in
  if (typeof window.isLoggedIn !== "undefined" && !window.isLoggedIn) {
    window.location.href = "login.php?redirect=ProItemList.php";
    return;
  }

  const productContainer = element.closest(".product-container");
  if (!productContainer) {
    console.error("Product container not found");
    return;
  }

  const category = productContainer.dataset.category;

  if (
    category &&
    (category.toLowerCase().includes("accessories") ||
      category.toLowerCase().includes("sti-accessories"))
  ) {
    showAccessoryModal(element);
  } else {
    showSizeModal(element);
  }
}

function showAccessoryModal(element) {
  const productContainer = element.closest(".product-container");
  const price = productContainer.dataset.prices.split(",")[0];
  const stock = productContainer.dataset.stock;

  currentProduct = {
    itemCode: productContainer.dataset.itemCode,
    name: productContainer.dataset.itemName,
    price: price,
    stock: stock,
    image: productContainer.querySelector("img").src,
    category: productContainer.dataset.category,
  };

  // Update modal content
  document.getElementById("accessoryModalImage").src = currentProduct.image;
  document.getElementById("accessoryModalName").textContent =
    currentProduct.name;
  document.getElementById(
    "accessoryModalPrice"
  ).textContent = `Price: ₱${parseFloat(currentProduct.price).toFixed(2)}`;
  document.getElementById(
    "accessoryModalStock"
  ).textContent = `Stock: ${currentProduct.stock}`;

  // Set max quantity
  const accessoryQuantityInput = document.getElementById("accessoryQuantity");
  accessoryQuantityInput.max = currentProduct.stock;
  accessoryQuantityInput.value = ""; // Start with empty value
  accessoryQuantityInput.placeholder = "0"; // Add placeholder text

  // Add input validation
  accessoryQuantityInput.addEventListener("input", function () {
    validateAccessoryQuantity(this);
  });

  accessoryQuantityInput.addEventListener("blur", function () {
    validateAccessoryQuantity(this, true);
  });

  // Clear previous event listeners
  const oldInput = accessoryQuantityInput.cloneNode(true);
  accessoryQuantityInput.parentNode.replaceChild(
    oldInput,
    accessoryQuantityInput
  );

  // Add event listeners to the new input
  const newInput = document.getElementById("accessoryQuantity");
  newInput.addEventListener("input", function () {
    validateAccessoryQuantity(this);
  });

  newInput.addEventListener("blur", function () {
    validateAccessoryQuantity(this, true);
  });

  // Show the modal
  document.getElementById("accessoryModal").style.display = "block";
}

function validateAccessoryQuantity(input, enforceMax = false) {
  // Make sure it's a positive integer or empty
  input.value = input.value.replace(/[^0-9]/g, "");

  // If it's empty, that's fine - allow user to type
  if (input.value === "") {
    return;
  }

  // Check against max stock
  if (enforceMax) {
    const maxStock = parseInt(currentProduct.stock);
    if (parseInt(input.value) > maxStock) {
      input.value = maxStock;
      alert(`Maximum available stock is ${maxStock}.`);
    }
  }
}

function closeAccessoryModal() {
  document.getElementById("accessoryModal").style.display = "none";
  document.getElementById("accessoryQuantity").value = "";
}

function incrementAccessoryQuantity() {
  const input = document.getElementById("accessoryQuantity");
  const max = parseInt(currentProduct.stock);
  const currentValue = parseInt(input.value) || 0; // Use 0 if no value or NaN

  if (currentValue < max) {
    input.value = currentValue + 1;
  } else {
    input.value = max; // Ensure it doesn't exceed max
  }
}

function decrementAccessoryQuantity() {
  const input = document.getElementById("accessoryQuantity");
  const currentValue = parseInt(input.value) || 0;
  if (currentValue > 0) {
    input.value = currentValue - 1;
  }
}

function addAccessoryToCart() {
  const quantityInput = document.getElementById("accessoryQuantity");

  // Check if quantity is empty or zero
  if (!quantityInput.value || quantityInput.value === "0") {
    alert("Please enter a valid quantity");
    return;
  }

  const quantity = parseInt(quantityInput.value);
  const availableStock = parseInt(currentProduct.stock);

  // Validate quantity against stock
  if (quantity <= 0) {
    alert("Please enter a valid quantity");
    return;
  }

  if (quantity > availableStock) {
    alert(`Sorry, only ${availableStock} items are available in stock.`);
    quantityInput.value = availableStock; // Set to maximum available
    return;
  }

  addToCart(null, {
    itemCode: currentProduct.itemCode,
    quantity: quantity,
    size: "One Size",
  });

  closeAccessoryModal();
}

function showSizeModal(element) {
  const productContainer = element.closest(".product-container");
  const category = productContainer.dataset.category;

  currentProduct = {
    itemCode: productContainer.dataset.itemCode,
    name: productContainer.dataset.itemName,
    sizes: productContainer.dataset.sizes.split(","),
    prices: productContainer.dataset.prices.split(","),
    stocks: productContainer.dataset.stocks.split(","),
    itemCodes: productContainer.dataset.itemCodes
      ? productContainer.dataset.itemCodes.split(",")
      : [],
    image: productContainer.querySelector("img").src,
    category: category,
    stock: productContainer.dataset.stock,
  };

  // Update modal content
  document.getElementById("modalProductImage").src = currentProduct.image;
  document.getElementById("modalProductName").textContent = currentProduct.name;
  document.getElementById(
    "modalProductPrice"
  ).textContent = `Price Range: ₱${Math.min(
    ...currentProduct.prices.map(Number)
  ).toFixed(2)} - ₱${Math.max(...currentProduct.prices.map(Number)).toFixed(
    2
  )}`;
  document.getElementById(
    "modalProductStock"
  ).textContent = `Total Stock: ${currentProduct.stock}`;

  // Generate size options - display all sizes XS to 7XL, and One Size if present
  const sizeOptionsContainer = document.querySelector(".size-options");
  sizeOptionsContainer.innerHTML = "";

  let allSizes = [
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

  // If 'One Size' is present in the product's sizes, add it to the front
  if (currentProduct.sizes.some((s) => s.trim().toLowerCase() === "one size")) {
    allSizes = ["One Size", ...allSizes];
  }

  allSizes.forEach((size) => {
    const sizeBtn = document.createElement("div");
    sizeBtn.className = "size-option";
    sizeBtn.textContent = size;

    const idx = currentProduct.sizes.findIndex(
      (s) => s.trim().toLowerCase() === size.trim().toLowerCase()
    );
    const stock = idx >= 0 ? parseInt(currentProduct.stocks[idx]) || 0 : 0;
    const itemCode =
      idx >= 0 && currentProduct.itemCodes[idx]
        ? currentProduct.itemCodes[idx]
        : currentProduct.itemCode;
    const price =
      idx >= 0 && currentProduct.prices[idx] ? currentProduct.prices[idx] : "";

    sizeBtn.dataset.stock = stock;
    sizeBtn.dataset.itemCode = itemCode;
    sizeBtn.dataset.price = price;

    if (stock > 0) {
      sizeBtn.classList.add("available");
      sizeBtn.onclick = () => selectSize(sizeBtn);
    } else {
      sizeBtn.classList.add("unavailable");
    }

    sizeOptionsContainer.appendChild(sizeBtn);
  });

  // Get the quantity input
  const quantityInput = document.getElementById("quantity");
  quantityInput.value = ""; // Start with empty value instead of 1
  quantityInput.placeholder = "0"; // Add placeholder text

  // Clear previous event listeners
  const oldInput = quantityInput.cloneNode(true);
  quantityInput.parentNode.replaceChild(oldInput, quantityInput);

  // Add event listeners to the new input
  const newInput = document.getElementById("quantity");
  newInput.addEventListener("input", function () {
    validateQuantityInput(this);
  });

  newInput.addEventListener("blur", function () {
    validateQuantityInput(this, true);
  });

  document.getElementById("sizeModal").style.display = "block";
}

function validateQuantityInput(input, enforceMax = false) {
  // Make sure it's a positive integer or empty
  input.value = input.value.replace(/[^0-9]/g, "");

  // If it's empty, that's fine - allow user to type
  if (input.value === "") {
    return;
  }

  // If a size is selected, check against max stock
  const selectedSize = document.querySelector(".size-option.selected");
  if (selectedSize && enforceMax) {
    const maxStock = parseInt(selectedSize.dataset.stock);
    if (parseInt(input.value) > maxStock) {
      input.value = maxStock;
      alert(`Maximum available stock for this size is ${maxStock}.`);
    }
  }
}

function selectSize(element) {
  // If the clicked element is already selected, deselect it
  if (element.classList.contains("selected")) {
    element.classList.remove("selected");
    // Reset stock and price display
    document.getElementById(
      "modalProductStock"
    ).textContent = `Total Stock: ${currentProduct.stock}`;
    document.getElementById(
      "modalProductPrice"
    ).textContent = `Price Range: ₱${Math.min(
      ...currentProduct.prices.map(Number)
    ).toFixed(2)} - ₱${Math.max(...currentProduct.prices.map(Number)).toFixed(
      2
    )}`;
    // Reset quantity input
    const quantityInput = document.getElementById("quantity");
    quantityInput.value = "";
    quantityInput.placeholder = "0";
    return;
  }

  // Only allow selection if size is available
  if (element.classList.contains("unavailable")) {
    return;
  }

  document
    .querySelectorAll(".size-option")
    .forEach((btn) => btn.classList.remove("selected"));
  element.classList.add("selected");

  // Update stock display for the selected size
  const stock = element.dataset.stock;
  const price = element.dataset.price;

  document.getElementById("modalProductStock").textContent = `Stock: ${stock}`;
  document.getElementById(
    "modalProductPrice"
  ).textContent = `Price: ₱${parseFloat(price).toFixed(2)}`;

  // Update max quantity
  const quantityInput = document.getElementById("quantity");
  const maxStock = parseInt(stock);
  quantityInput.max = maxStock;

  // Adjust quantity if it exceeds the available stock for the selected size
  const currentQty = parseInt(quantityInput.value);
  if (currentQty > maxStock) {
    quantityInput.value = maxStock;
    alert(
      `Quantity has been adjusted to ${maxStock}, the maximum available stock for this size.`
    );
  }
}

function incrementQuantity() {
  const input = document.getElementById("quantity");
  const selectedSize = document.querySelector(".size-option.selected");

  if (!selectedSize) {
    return; // Don't increment if no size is selected
  }

  const max = parseInt(selectedSize.dataset.stock);
  const currentValue = parseInt(input.value) || 0; // Use 0 if no value or NaN

  if (currentValue < max) {
    input.value = currentValue + 1;
  } else {
    input.value = max; // Ensure it doesn't exceed max
  }
}

function decrementQuantity() {
  const input = document.getElementById("quantity");
  const currentValue = parseInt(input.value) || 0;
  if (currentValue > 0) {
    input.value = currentValue - 1;
  }
}

function addToCartWithSize() {
  const selectedSize = document.querySelector(".size-option.selected");
  if (
    !selectedSize &&
    !currentProduct.category?.toLowerCase().includes("accessories")
  ) {
    alert("Please select a size");
    return;
  }

  const quantityInput = document.getElementById("quantity");

  // Check if quantity is empty or zero
  if (!quantityInput.value || quantityInput.value === "0") {
    alert("Please enter a valid quantity");
    return;
  }

  const quantity = parseInt(quantityInput.value);
  const size = currentProduct.category?.toLowerCase().includes("accessories")
    ? "One Size"
    : selectedSize.textContent;

  // Get the specific item code for the selected size
  const itemCode = selectedSize
    ? selectedSize.dataset.itemCode
    : currentProduct.itemCode;

  // Get the stock for validation
  const availableStock = selectedSize
    ? parseInt(selectedSize.dataset.stock)
    : parseInt(currentProduct.stock);

  // Validate quantity against stock (final check)
  if (quantity <= 0) {
    alert("Please enter a valid quantity");
    return;
  }

  if (quantity > availableStock) {
    alert(
      `Sorry, only ${availableStock} items are available in stock for size ${size}.`
    );
    quantityInput.value = availableStock; // Set to maximum available
    return;
  }

  // Add to cart with size and quantity
  addToCart(null, {
    itemCode: itemCode,
    size: size,
    quantity: quantity,
  });

  // Close modal
  const modal = document.getElementById("sizeModal");
  modal.style.display = "none";
  // Reset quantity
  document.getElementById("quantity").value = "";
}

function createFlyingElement(startElement) {
  const flyingElement = document.createElement("div");
  flyingElement.className = "cart-item-flying";
  flyingElement.innerHTML = '<i class="fa fa-shopping-bag"></i>';

  // Get start position (from the clicked button)
  const startRect = startElement.getBoundingClientRect();

  // Get end position (cart icon)
  const cartIcon = document.querySelector(".cart-icon");
  const endRect = cartIcon.getBoundingClientRect();

  // Set initial position
  flyingElement.style.top = `${startRect.top}px`;
  flyingElement.style.left = `${startRect.left}px`;
  flyingElement.style.opacity = "1";

  document.body.appendChild(flyingElement);

  // Trigger animation
  requestAnimationFrame(() => {
    flyingElement.style.transform = `translate(${
      endRect.left - startRect.left
    }px, ${endRect.top - startRect.top}px) scale(0.5)`;
    flyingElement.style.opacity = "0";
  });

  // Remove element after animation
  setTimeout(() => {
    document.body.removeChild(flyingElement);
    // Add shake animation to cart icon
    cartIcon.classList.add("cart-shake");
    setTimeout(() => cartIcon.classList.remove("cart-shake"), 500);
  }, 800);
}

async function addToCart(element, customData = null) {
  try {
    let itemCode, quantity, size;

    if (customData) {
      // Data coming from the size selection modal or direct accessory add
      itemCode = customData.itemCode;
      quantity = customData.quantity;
      size = customData.size;
      console.log("Using custom data:", customData); // Debug log
    } else {
      // Direct add to cart (for accessories)
      const productContainer = element.closest(".product-container");
      const category = productContainer.dataset.category;
      itemCode = productContainer.dataset.itemCode;
      quantity = 1;
      size =
        category &&
        (category.toLowerCase().includes("accessories") ||
          category.toLowerCase().includes("sti-accessories"))
          ? "One Size"
          : null;
      console.log("Direct add data:", { itemCode, quantity, size, category }); // Debug log
    }

    // Create the form data
    const formData = new URLSearchParams();
    formData.append("action", "add");
    formData.append("item_code", itemCode);
    formData.append("quantity", quantity);
    formData.append("size", size || ""); // Ensure size is always sent, even if empty string

    console.log("Sending to server:", formData.toString()); // Debug log

    const response = await fetch("../Includes/cart_operations.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: formData.toString(),
    });

    const data = await response.json();
    console.log("Server response:", data); // Debug log

    if (data.success) {
      // Create flying animation from the clicked button to cart icon
      if (element) {
        createFlyingElement(element);
      }

      // Update cart count in header
      const cartCount = document.querySelector(".cart-count");
      if (cartCount) {
        cartCount.textContent = data.cart_count;
      }

      // Show success message
      alert("Item added to cart successfully!");

      // Update cart popup content
      updateCartPopup();
    } else {
      alert(data.message || "Error adding item to cart");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Error adding item to cart");
  }
}

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
      }
    })
    .catch((error) => console.error("Error:", error));
}
