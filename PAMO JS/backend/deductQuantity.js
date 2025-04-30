// Sales Entry Modal Backend Functions

function showDeductQuantityModal() {
  document.getElementById("deductQuantityModal").style.display = "block";
  document.getElementById("deductQuantityForm").reset();
  // Reset all select elements to their default state
  document.querySelectorAll('select[name="itemId[]"]').forEach((select) => {
    select.value = "";
  });
}

function addSalesItem() {
  const salesItems = document.getElementById("salesItems");
  const originalItem = salesItems.querySelector(".sales-item");

  // Destroy Select2 on the original select before cloning
  const originalSelect = originalItem.querySelector('select[name="itemId[]"]');
  if (window.jQuery && $(originalSelect).data("select2")) {
    $(originalSelect).select2("destroy");
  }

  const newItem = originalItem.cloneNode(true);

  // Reset the values in the cloned item
  const select = newItem.querySelector('select[name="itemId[]"]');
  const sizeSelect = newItem.querySelector('select[name="size[]"]');
  const quantityInput = newItem.querySelector(
    'input[name="quantityToDeduct[]"]'
  );
  const priceInput = newItem.querySelector('input[name="pricePerItem[]"]');
  const totalInput = newItem.querySelector('input[name="itemTotal[]"]');

  if (select) select.value = "";
  if (sizeSelect) {
    sizeSelect.innerHTML = '<option value="">Select Size</option>';
    sizeSelect.value = "";
  }
  if (quantityInput) quantityInput.value = "";
  if (priceInput) priceInput.value = "";
  if (totalInput) totalInput.value = "";

  // Add or show close button for the new item
  let closeBtn = newItem.querySelector(".item-close");
  if (!closeBtn) {
    closeBtn = document.createElement("div");
    closeBtn.className = "item-close";
    closeBtn.innerHTML = "&times;";
    newItem.appendChild(closeBtn);
  }
  closeBtn.style.display = "block";
  closeBtn.onclick = function () {
    removeSalesItem(this);
  };

  // Add change event listeners to the new item
  if (select) {
    select.addEventListener("change", function () {
      validateProductSelection(this);
      updateAvailableSizes(this);
      updateSalesProductOptions();
    });
    // Re-initialize Select2 for the new select
    if (window.jQuery && $(select).length) {
      $(select).select2({
        placeholder: "Select Product",
        allowClear: true,
        width: "100%",
      });
      // Attach event after Select2
      $(select).on("change", function () {
        updateAvailableSizes(this);
        updateSalesProductOptions();
      });
    }
  }

  if (sizeSelect) {
    sizeSelect.addEventListener("change", function () {
      updateItemPrice(this);
    });
  }

  salesItems.appendChild(newItem);

  // Re-initialize Select2 for the original select
  if (window.jQuery && $(originalSelect).length) {
    $(originalSelect).select2({
      placeholder: "Select Product",
      allowClear: true,
      width: "100%",
    });
    // Attach event after Select2
    $(originalSelect).on("change", function () {
      updateAvailableSizes(this);
      updateSalesProductOptions();
    });
  }

  // Show close buttons for all but the first item
  document.querySelectorAll(".sales-item .item-close").forEach((btn, idx) => {
    btn.style.display = idx === 0 ? "none" : "block";
  });

  updateSalesProductOptions();
}

function removeSalesItem(closeButton) {
  const salesItems = document.getElementById("salesItems");
  const items = salesItems.querySelectorAll(".sales-item");

  if (items.length > 1) {
    const salesItem = closeButton.closest(".sales-item");
    salesItem.remove();
    calculateTotalAmount();
  }

  // Hide close button for the first remaining item
  document.querySelectorAll(".sales-item .item-close").forEach((btn, idx) => {
    btn.style.display = idx === 0 ? "none" : "block";
  });
}

function updateItemPrice(sizeSelect) {
  const itemContainer = sizeSelect.closest(".sales-item");
  const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
  const itemCode = selectedOption.getAttribute("data-item-code");
  const priceInput = itemContainer.querySelector(
    'input[name="pricePerItem[]"]'
  );
  const quantityInput = itemContainer.querySelector(
    'input[name="quantityToDeduct[]"]'
  );
  const totalInput = itemContainer.querySelector('input[name="itemTotal[]"]');

  if (!itemCode || !sizeSelect.value) {
    priceInput.value = "";
    totalInput.value = "";
    return;
  }

  // Fetch the price from the server using the correct item_code
  fetch(
    `../PAMO Inventory backend/get_item_price.php?item_code=${itemCode}&size=${sizeSelect.value}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        priceInput.value = data.price;
        if (quantityInput.value) {
          calculateItemTotal(quantityInput);
        }
      } else {
        alert("Error getting price: " + data.message);
        priceInput.value = "";
        totalInput.value = "";
      }
    })
    .catch((error) => {
      console.error("Error getting price:", error);
      alert("Error getting price");
      priceInput.value = "";
      totalInput.value = "";
    });
}

function calculateItemTotal(inputElement) {
  const itemContainer = inputElement.closest(".sales-item");
  const quantityInput = itemContainer.querySelector(
    'input[name="quantityToDeduct[]"]'
  );
  const priceInput = itemContainer.querySelector(
    'input[name="pricePerItem[]"]'
  );
  const totalInput = itemContainer.querySelector('input[name="itemTotal[]"]');

  const quantity = parseFloat(quantityInput.value) || 0;
  const price = parseFloat(priceInput.value) || 0;
  const total = quantity * price;

  totalInput.value = total.toFixed(2);
  calculateTotalAmount();
}

function calculateTotalAmount() {
  const itemTotals = document.querySelectorAll('input[name="itemTotal[]"]');
  let total = 0;

  itemTotals.forEach((input) => {
    total += parseFloat(input.value) || 0;
  });

  document.getElementById("totalAmount").value = total.toFixed(2);
}

function validateProductSelection(selectElement) {
  const selectedValue = selectElement.value;
  if (!selectedValue) return;

  const allSelects = document.querySelectorAll('select[name="itemId[]"]');
  let duplicateFound = false;

  allSelects.forEach((select) => {
    if (select !== selectElement && select.value === selectedValue) {
      duplicateFound = true;
    }
  });

  if (duplicateFound) {
    alert(
      "This product has already been selected. Please choose a different product."
    );
    selectElement.value = "";
    const itemContainer = selectElement.closest(".sales-item");
    const priceInput = itemContainer.querySelector(
      'input[name="pricePerItem[]"]'
    );
    const totalInput = itemContainer.querySelector('input[name="itemTotal[]"]');
    if (priceInput) priceInput.value = "";
    if (totalInput) totalInput.value = "";
    calculateTotalAmount();
  }
}

function updateAvailableSizes(itemSelect) {
  const itemContainer = itemSelect.closest(".sales-item");
  const sizeSelect = itemContainer.querySelector('select[name="size[]"]');
  const itemCode = itemSelect.value;

  // Clear previous options
  sizeSelect.innerHTML = '<option value="">Select Size</option>';

  if (!itemCode) return;

  // Use item_code param for backend
  fetch(`../PAMO Inventory backend/get_item_sizes.php?item_code=${itemCode}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        data.sizes.forEach((obj) => {
          const option = document.createElement("option");
          option.value = obj.size;
          option.textContent = obj.size;
          option.setAttribute("data-item-code", obj.item_code);
          sizeSelect.appendChild(option);
        });
      }
    })
    .catch((error) => {
      console.error("Error fetching sizes:", error);
    });
}

function showSalesReceipt(formData) {
  let html = `<p><strong>Transaction Number:</strong> ${formData.transactionNumber}</p>`;
  html += `<p><strong>Name:</strong> ${formData.studentName}</p>`;
  html += `<p><strong>ID Number:</strong> ${formData.studentIdNumber}</p>`;
  html += `<table style="width:100%;border-collapse:collapse;" border="1">
    <thead>
      <tr>
        <th>Product</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Price per Item</th>
        <th>SubTotal</th>
      </tr>
    </thead>
    <tbody>`;
  for (let i = 0; i < formData.itemIds.length; i++) {
    html += `<tr>
      <td>${formData.itemNames[i] || formData.itemIds[i]}</td>
      <td>${formData.sizes[i]}</td>
      <td>${formData.quantities[i]}</td>
      <td>${formData.prices[i]}</td>
      <td>${formData.itemTotals[i]}</td>
    </tr>`;
  }
  html += `</tbody></table>`;
  html += `<p style="text-align:right;"><strong>Total Amount:</strong> ${formData.totalAmount}</p>`;

  document.getElementById("salesReceiptBody").innerHTML = html;
  document.getElementById("salesReceiptModal").style.display = "block";
}

function printSalesReceipt() {
  const printContents = document.getElementById(
    "salesReceiptContent"
  ).innerHTML;
  const originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
  window.location.reload();
}

function submitDeductQuantity(event) {
  event.preventDefault();

  const transactionNumber = document.getElementById("transactionNumber").value;
  const studentNameSelect = document.getElementById("studentName");
  const studentName =
    studentNameSelect.options[studentNameSelect.selectedIndex].text;
  const studentIdNumber = document.getElementById("studentIdNumber").value;
  const salesItems = document.querySelectorAll(".sales-item");

  if (
    !transactionNumber ||
    !studentNameSelect.value ||
    !studentIdNumber ||
    salesItems.length === 0
  ) {
    alert("Please fill in all required fields");
    return;
  }

  const formData = new FormData();
  formData.append("transactionNumber", transactionNumber);
  formData.append("studentName", studentName);
  formData.append("studentIdNumber", studentIdNumber);

  // Arrays to store multiple items
  const itemIds = [];
  const itemNames = [];
  const sizes = [];
  const quantities = [];
  const prices = [];
  const itemTotals = [];

  // Validate and collect all items data
  let hasErrors = false;
  salesItems.forEach((item, index) => {
    const itemSelect = item.querySelector('select[name="itemId[]"]');
    const itemName = itemSelect
      ? itemSelect.options[itemSelect.selectedIndex].text
      : "";
    const sizeSelect = item.querySelector('select[name="size[]"]');
    const quantityInput = item.querySelector(
      'input[name="quantityToDeduct[]"]'
    );
    const priceInput = item.querySelector('input[name="pricePerItem[]"]');
    const totalInput = item.querySelector('input[name="itemTotal[]"]');

    if (
      !itemSelect ||
      !sizeSelect ||
      !quantityInput ||
      !priceInput ||
      !itemSelect.value ||
      !sizeSelect.value ||
      !quantityInput.value ||
      !priceInput.value
    ) {
      alert(`Please fill in all fields for item ${index + 1}`);
      hasErrors = true;
      return;
    }

    if (parseInt(quantityInput.value) <= 0) {
      alert(`Quantity must be greater than 0 for item ${index + 1}`);
      hasErrors = true;
      return;
    }

    itemIds.push(itemSelect.value);
    itemNames.push(itemName);
    sizes.push(sizeSelect.value);
    quantities.push(quantityInput.value);
    prices.push(priceInput.value);
    itemTotals.push(totalInput.value);
  });

  if (hasErrors) {
    return;
  }

  // Append arrays to FormData
  itemIds.forEach((id, index) => {
    formData.append("itemId[]", id);
    formData.append("size[]", sizes[index]);
    formData.append("quantityToDeduct[]", quantities[index]);
    formData.append("pricePerItem[]", prices[index]);
    formData.append("itemTotal[]", itemTotals[index]);
  });

  formData.append("totalAmount", document.getElementById("totalAmount").value);

  // Debug log
  console.log("Sending data:", {
    transactionNumber,
    studentName,
    studentIdNumber,
    itemIds,
    sizes,
    quantities,
    prices,
    itemTotals,
    totalAmount: document.getElementById("totalAmount").value,
  });

  fetch("../PAMO Inventory backend/process_deduct_quantity.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      console.log("Response status:", response.status);
      return response.text().then((text) => {
        console.log("Raw response:", text);
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("Error parsing JSON:", e);
          throw new Error("Invalid JSON response from server");
        }
      });
    })
    .then((data) => {
      if (data.success) {
        closeModal("deductQuantityModal");
        showSalesReceipt({
          transactionNumber: transactionNumber,
          studentName,
          studentIdNumber,
          itemIds,
          itemNames,
          sizes,
          quantities,
          prices,
          itemTotals,
          totalAmount: document.getElementById("totalAmount").value,
        });
      } else {
        alert("Error: " + (data.message || "Unknown error occurred"));
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(
        "An error occurred while processing your request. Check console for details."
      );
    });
}

function populateNamesByRole(role) {
  const nameSelect = document.getElementById("studentName");
  nameSelect.innerHTML = '<option value="">Select Name</option>';
  document.getElementById("studentIdNumber").value = "";
  if (!role) return;
  fetch(
    "../PAMO Inventory backend/get_students.php?role=" +
      encodeURIComponent(role)
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        data.students.forEach((student) => {
          const option = document.createElement("option");
          option.value = student.id;
          option.textContent = student.name;
          option.setAttribute("data-id-number", student.id_number);
          nameSelect.appendChild(option);
        });
      }
    });
}

// Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // When a role is selected, update the names dropdown
  document
    .getElementById("roleCategory")
    .addEventListener("change", function () {
      populateNamesByRole(this.value);
    });

  // When a name is selected, autofill the ID number
  document
    .getElementById("studentName")
    .addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const idNumber = selectedOption.getAttribute("data-id-number") || "";
      document.getElementById("studentIdNumber").value = idNumber;
    });

  // Add click handlers to existing close buttons
  const closeButtons = document.querySelectorAll(".item-close");
  closeButtons.forEach((btn) => {
    btn.onclick = function () {
      removeSalesItem(this);
    };
  });

  // Add change event listeners to all select elements
  document.querySelectorAll('select[name="itemId[]"]').forEach((select) => {
    select.addEventListener("change", function () {
      validateProductSelection(this);
      updateAvailableSizes(this);
      updateSalesProductOptions();
    });
    // Attach event after Select2
    if (window.jQuery && $(select).data("select2")) {
      $(select).on("change", function () {
        updateAvailableSizes(this);
        updateSalesProductOptions();
      });
    }
  });

  document.querySelectorAll('select[name="size[]"]').forEach((select) => {
    select.addEventListener("change", function () {
      updateItemPrice(this);
    });
  });

  // Add resetDeductQuantityModal to reset the modal on close
  const deductQuantityModal = document.getElementById("deductQuantityModal");
  if (deductQuantityModal) {
    const closeBtns = deductQuantityModal.querySelectorAll(
      ".close, .cancel-btn"
    );
    closeBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        resetDeductQuantityModal();
      });
    });
  }

  updateSalesProductOptions();
});

function resetDeductQuantityModal() {
  const form = document.getElementById("deductQuantityForm");
  if (form) form.reset();

  // Remove all sales-item divs except the first one
  const salesItems = document.getElementById("salesItems");
  if (salesItems) {
    const items = salesItems.querySelectorAll(".sales-item");
    items.forEach((item, idx) => {
      if (idx > 0) item.remove();
    });
  }

  // Reset Select2 for all product selects
  if (window.jQuery && $('select[name="itemId[]"]').length) {
    $('select[name="itemId[]"]').val(null).trigger("change");
  }
}

function updateSalesProductOptions() {
  const allSelects = document.querySelectorAll('select[name="itemId[]"]');
  const selectedValues = Array.from(allSelects)
    .map((select) => select.value)
    .filter((val) => val);

  allSelects.forEach((select) => {
    const currentValue = select.value;
    // Store all option values and text
    const allOptions = Array.from(select.querySelectorAll("option")).map(
      (opt) => ({
        value: opt.value,
        text: opt.text,
        selected: opt.selected,
        dataset: opt.dataset,
      })
    );

    // Remove all options except the placeholder and the current value
    select.innerHTML = "";
    // Add placeholder
    const placeholderOption = document.createElement("option");
    placeholderOption.value = "";
    placeholderOption.textContent = "Select Product";
    select.appendChild(placeholderOption);

    // Add back only options that are not selected in other selects, or the current value
    allOptions.forEach((opt) => {
      if (
        opt.value === "" ||
        opt.value === currentValue ||
        !selectedValues.includes(opt.value)
      ) {
        const option = document.createElement("option");
        option.value = opt.value;
        option.text = opt.text;
        if (opt.selected) option.selected = true;
        // Copy data attributes (for price, etc.)
        if (opt.dataset) {
          for (const key in opt.dataset) {
            option.dataset[key] = opt.dataset[key];
          }
        }
        select.appendChild(option);
      }
    });

    // Refresh Select2
    if (window.jQuery && $(select).data("select2")) {
      $(select).trigger("change.select2");
    }
  });
}
