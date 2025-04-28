// Add Item Size Modal Backend Functions

function showAddItemSizeModal() {
  document.getElementById("addItemSizeModal").style.display = "block";
}

function updateItemCodePrefix() {
  const select = document.getElementById("existingItem");
  const selectedOption = select.options[select.selectedIndex];
  const prefix = selectedOption.value;
  const itemCodeField = document.getElementById("newItemCode");
  // Only set the prefix if the field is empty or if the prefix changed
  if (!itemCodeField.value || !itemCodeField.value.startsWith(prefix + "-")) {
    itemCodeField.value = prefix + "-";
  }
}

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
