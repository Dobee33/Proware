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

  // If shirt type is visible and selected, add it to course_id[]
  const shirtTypeGroup = document.getElementById("shirtTypeGroup");
  if (shirtTypeGroup && shirtTypeGroup.style.display !== "none") {
    const shirtTypeSelect = document.getElementById("shirtTypeSelect");
    const shirtTypeValue = shirtTypeSelect.value;
    if (shirtTypeValue) {
      // Find the option in the course select with the same text/value
      const courseSelect = document.getElementById("courseSelect");
      if (courseSelect) {
        for (let i = 0; i < courseSelect.options.length; i++) {
          if (courseSelect.options[i].text === shirtTypeValue) {
            formData.append("course_id[]", courseSelect.options[i].value);
            break;
          }
        }
      }
    }
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
  // Initialize Select2 for course and shirt type
  if (window.jQuery && $("#courseSelect").length) {
    $("#courseSelect").select2({
      placeholder: "Select course(s)",
      allowClear: true,
      width: "100%",
    });
  }
  if (window.jQuery && $("#shirtTypeSelect").length) {
    $("#shirtTypeSelect").select2({
      placeholder: "Select shirt type",
      allowClear: true,
    });
  }

  // Only declare categorySelect once
  const categorySelect = document.getElementById("newCategory");
  if (categorySelect) {
    categorySelect.addEventListener("change", function () {
      const courseGroup = document.getElementById("courseGroup");
      const shirtTypeGroup = document.getElementById("shirtTypeGroup");
      if (this.value === "Tertiary-Uniform") {
        courseGroup.style.display = "block";
        shirtTypeGroup.style.display = "none";
      } else if (this.value === "STI-Shirts") {
        courseGroup.style.display = "none";
        shirtTypeGroup.style.display = "block";
      } else {
        courseGroup.style.display = "none";
        shirtTypeGroup.style.display = "none";
      }
    });
  }
});
