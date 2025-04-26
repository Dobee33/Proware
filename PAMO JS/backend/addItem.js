// Add Item Modal Backend Functions

function showAddItemModal() {
  document.getElementById("addItemModal").style.display = "flex";
  document.getElementById("addItemForm").reset();
}

function submitNewItem(event) {
  event.preventDefault();

  const quantity = parseInt(document.getElementById("newItemQuantity").value);
  const damage = parseInt(document.getElementById("newItemDamage").value) || 0;
  const form = document.getElementById("addItemForm");
  const formData = new FormData(form);

  if (
    !formData.get("newItemCode") ||
    !formData.get("newCategory") ||
    !formData.get("newItemName") ||
    !formData.get("newSize") ||
    isNaN(formData.get("newItemPrice")) ||
    isNaN(formData.get("newItemQuantity"))
  ) {
    alert("Please fill in all required fields with valid values");
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../PAMO Inventory backend/add_item.php", true);
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          const data = JSON.parse(xhr.responseText);
          if (data.success) {
            alert("New product added successfully!");
            location.reload();
          } else {
            throw new Error(data.message || "Unknown error");
          }
        } catch (e) {
          console.error("Parse error:", e);
          alert("Error adding product: " + xhr.responseText);
        }
      } else {
        alert("Error: " + xhr.statusText);
      }
    }
  };

  xhr.send(formData);
}

// Event listener for category change
document.addEventListener("DOMContentLoaded", function () {
  const categorySelect = document.getElementById("newCategory");
  if (categorySelect) {
    categorySelect.addEventListener("change", function () {
      const sizeGroup = document.querySelector(".input-group:has(#newSize)");
      if (this.value === "STI-Accessories") {
        sizeGroup.style.display = "none";
        document.getElementById("newSize").value = "One Size";
        document.getElementById("newSize").removeAttribute("required");
      } else {
        sizeGroup.style.display = "block";
        document.getElementById("newSize").setAttribute("required", "required");
      }
    });
  }
});
