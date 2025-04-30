// Add Item Size Modal Backend Functions

function showAddItemSizeModal() {
  document.getElementById("addItemSizeModal").style.display = "block";
}

// Size to suffix mapping
const sizeSuffixMap = {
  "One Size": "001",
  XS: "001",
  S: "002",
  M: "003",
  L: "004",
  XL: "005",
  XXL: "006",
  "3XL": "007",
  "4XL": "008",
  "5XL": "009",
  "6XL": "010",
  "7XL": "011",
};

function updateItemCodePrefix() {
  const select = document.getElementById("existingItem");
  const selectedOption = select.options[select.selectedIndex];
  const prefix = selectedOption.value;
  const sizeSelect = document.getElementById("newSize");
  const size = sizeSelect.value;
  const itemCodeField = document.getElementById("newItemCode");
  let suffix = sizeSuffixMap[size] || "";
  if (prefix && suffix) {
    itemCodeField.value = prefix + "-" + suffix;
  } else if (prefix) {
    itemCodeField.value = prefix + "-";
  } else {
    itemCodeField.value = "";
  }
}

const allSizes = [
  "One Size",
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

function updateSizeDropdown(existingSizes) {
  const sizeSelect = document.getElementById("newSize");
  sizeSelect.innerHTML = '<option value="">Select Size</option>';
  // Normalize for both string and object
  const normalizedExisting = existingSizes.map((s) =>
    typeof s === "string"
      ? s.trim().toLowerCase()
      : (s.size || "").trim().toLowerCase()
  );
  allSizes.forEach((size) => {
    if (!normalizedExisting.includes(size.toLowerCase())) {
      const option = document.createElement("option");
      option.value = size;
      option.textContent = size;
      sizeSelect.appendChild(option);
    }
  });
}

function fetchAndUpdateSizesForItem() {
  const select = document.getElementById("existingItem");
  const prefix = select.value;
  if (!prefix) {
    updateSizeDropdown([]); // Show all sizes if no item selected
    updateItemCodePrefix();
    return;
  }
  fetch(
    `../PAMO Inventory backend/get_item_sizes.php?prefix=${encodeURIComponent(
      prefix
    )}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateSizeDropdown(data.sizes);
        updateItemCodePrefix(); // Reset item code as well
      } else {
        alert("Error fetching sizes: " + data.message);
      }
    })
    .catch((err) => {
      alert("Error fetching sizes");
    });
}

document.addEventListener("DOMContentLoaded", function () {
  // Use jQuery event binding for Select2 compatibility
  if (window.jQuery && $("#existingItem").length) {
    $("#existingItem").on("change", fetchAndUpdateSizesForItem);
  } else {
    const existingItem = document.getElementById("existingItem");
    if (existingItem) {
      existingItem.addEventListener("change", fetchAndUpdateSizesForItem);
    }
  }
  // When size changes, update code
  const newSize = document.getElementById("newSize");
  if (newSize) {
    newSize.addEventListener("change", updateItemCodePrefix);
  }
});

function submitNewItemSize(event) {
  event.preventDefault();

  const size = document.getElementById("newSize").value;
  let itemCode = document.getElementById("newItemCode").value;
  itemCode = itemCode.trim();
  console.log("Item code value:", JSON.stringify(itemCode));

  if (!size) {
    alert("Please select a size.");
    return;
  }
  if (!itemCode || itemCode.endsWith("-")) {
    alert("Please enter a complete item code (with unique suffix).");
    return;
  }

  const formData = new FormData();
  formData.append(
    "existingItem",
    document.getElementById("existingItem").value
  );
  formData.append("newItemCode", itemCode);
  formData.append("newSize", size);
  formData.append("newQuantity", document.getElementById("newQuantity").value);
  formData.append("newDamage", document.getElementById("newDamage").value);
  formData.append(
    "deliveryOrderNumber",
    document.getElementById("deliveryOrderNumber").value
  );

  fetch("../PAMO Inventory backend/process_add_item_size.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("New size added successfully!");
        closeModal("addItemSizeModal");
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
