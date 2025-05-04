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
  // Helper for the two copies
  function renderReceipt(copyLabel) {
    const dataRows = formData.itemIds
      .map(
        (id, i) => `
      <tr>
        <td>${formData.itemNames[i]} ${
          formData.sizes[i] ? "(" + formData.sizes[i] + ")" : ""
        }</td>
        <td>PROWARE</td>
        <td style="text-align:center;">${formData.quantities[i]}</td>
        <td style="text-align:right;">${parseFloat(formData.prices[i]).toFixed(
          2
        )}</td>
        <td style="text-align:right;">${parseFloat(
          formData.itemTotals[i]
        ).toFixed(2)}</td>
        ${
          i === 0
            ? `<td class="signature-col" rowspan="${formData.itemIds.length}">
          <div style="min-height:180px;display:flex;flex-direction:column;justify-content:space-between;height:100%;">
            <div><b>Prepared by:</b><br><span class="sign-line"></span><br><span class="sign-name">Jason C. Amparo</span></div>
            <div><b>OR Issued by:</b><br><span class="sign-line"></span><br><span class="sign-name">Agnes Eubion</span></div>
            <div><b>Cashier:</b><br><span class="sign-line"></span></div>
            <div><b>Released by & date:</b><br><span class="sign-line"></span></div>
            <div style="margin-top:10px;"><b>RECEIVED BY:</b><br><span class="sign-line"></span><br><span class="sign-name" style="font-weight:bold;text-decoration:underline;">${formData.studentName}</span></div>
          </div>
        </td>`
            : ""
        }
      </tr>
    `
      )
      .join("");
    return `
      <div class="receipt-header-flex">
        <div class="receipt-header-logo"><img src="../Images/STI-LOGO.png" alt="STI Logo" /></div>
        <div class="receipt-header-center">
          <div class="sti-lucena">STI LUCENA</div>
          <div class="sales-issuance-slip">SALES ISSUANCE SLIP</div>
        </div>
        <div class="receipt-header-copy">${copyLabel}</div>
      </div>
      <div class="receipt-section">
        <table class="receipt-header-table">
          <tr>
            <td style="width:22%;font-size:0.98em;"><b>Student Name:</b></td>
            <td style="width:22%;border-bottom:1px solid #222;">${
              formData.studentName
            }</td>
            <td style="width:13%;font-size:0.98em;"><b>Student No.:</b></td>
            <td style="width:15%;border-bottom:1px solid #222;">${
              formData.studentIdNumber
            }</td>
            <td style="width:8%;font-size:0.98em;"><b>DATE:</b></td>
            <td style="width:15%;border-bottom:1px solid #222;">${new Date().toLocaleDateString()}</td>
          </tr>
          <tr>
            <td style="font-size:0.98em;"><b>Issuance Slip No.:</b></td>
            <td style="border-bottom:1px solid #222;">${
              formData.transactionNumber
            }</td>
            <td style="font-size:0.98em;"><b>Invoice No.:</b></td>
            <td style="border-bottom:1px solid #222;"></td>
            <td colspan="2" style="text-align:right;font-size:1.1em;font-weight:bold;"></td>
          </tr>
        </table>
        <table class="receipt-main-table">
          <thead>
            <tr>
              <th style="width:32%;">Item Description</th>
              <th style="width:14%;">Item Type</th>
              <th style="width:8%;">Qty</th>
              <th style="width:12%;">SRP</th>
              <th style="width:14%;">Amount</th>
              <th style="width:20%;vertical-align:top;">Prepared by:</th>
            </tr>
          </thead>
          <tbody>
            ${dataRows}
          </tbody>
        </table>
        <div class="receipt-footer-flex">
          <div class="receipt-footer-policy">
            <b>ALL ITEMS ARE RECEIVED IN GOOD CONDITION</b><br>
            <span style="font-size:0.97em;">(Exchange is allowed only within 3 days from the invoice date. Strictly no refund)</span>
          </div>
          <div class="receipt-total-row">
            <b>TOTAL AMOUNT:</b> <span style="min-width:80px;display:inline-block;text-align:right;">${parseFloat(
              formData.totalAmount
            ).toFixed(2)}</span>
          </div>
        </div>
      </div>
    `;
  }

  // Combine both copies in one A4 page
  const html = `
    <!-- PRINTING NOTE: For best results, set print scale to 100% or Actual Size in your print dialog. -->
    <div class="receipt-a4">
      <div class="receipt-half">${renderReceipt("PAMO COPY")}</div>
      <div class="receipt-divider"></div>
      <div class="receipt-half">${renderReceipt("STUDENT COPY")}</div>
    </div>
    <style>
      .receipt-header-flex {
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 2px;
        margin-top: 2px;
        min-height: 60px;
      }
      .receipt-header-logo img {
        height: 60px;
        width: auto;
        display: block;
      }
      .receipt-header-logo {
        flex: 0 0 80px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
      }
      .receipt-header-center {
        flex: 1 1 auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 0;
      }
      .sti-lucena {
        font-size: 1.35em;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 0px;
      }
      .sales-issuance-slip {
        font-size: 1.1em;
        font-weight: bold;
        letter-spacing: 0.5px;
        margin-top: 0px;
      }
      .receipt-header-copy {
        flex: 0 0 100px;
        text-align: right;
        font-size: 1em;
        font-weight: bold;
        margin-top: 2px;
        margin-right: 2px;
      }
      .receipt-a4 {
        width: 210mm;
        height: 297mm;
        padding: 0;
        margin: 0 auto;
        background: #fff;
        font-family: Arial, sans-serif;
        position: relative;
      }
      .receipt-half {
        height: 148.5mm;
        box-sizing: border-box;
        padding: 10px 10px 6px 10px;
        border-bottom: 2.5px dashed #333;
        page-break-inside: avoid;
        background: #fff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
      }
      .receipt-divider {
        height: 2px;
        background: transparent;
      }
      .receipt-header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        font-size: 1em;
      }
      .receipt-header-table td {
        padding: 2px 6px 2px 0;
        vertical-align: bottom;
      }
      .receipt-main-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1em;
        margin-bottom: 0px;
        table-layout: fixed;
      }
      .receipt-main-table th, .receipt-main-table td {
        border: 1px solid #222;
        padding: 6px 8px;
        vertical-align: top;
        word-break: break-word;
      }
      .receipt-main-table th {
        background: #f2f2f2;
        text-align: center;
      }
      .receipt-main-table td {
        background: #fff;
      }
      .signature-col {
        background: #fff;
        vertical-align: top;
        text-align: left;
        min-width: 180px;
        max-width: 220px;
      }
      .sign-line {
        display: inline-block;
        border-bottom: 1px solid #222;
        width: 120px;
        height: 18px;
        margin-bottom: 2px;
      }
      .sign-name {
        font-size: 0.95em;
        color: #222;
      }
      .receipt-footer-flex {
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        margin-top: 0.5em;
      }
      .receipt-footer-policy {
        font-size: 0.98em;
        border-top: none;
        padding-top: 0px;
        text-align: left;
        margin-bottom: 0;
        max-width: 60%;
      }
      .receipt-total-row {
        font-size: 1.05em;
        font-weight: bold;
        margin: 0 0 0 0;
        text-align: right;
        min-width: 180px;
      }
      @media print {
        @page {
          size: A4;
          margin: 0;
        }
        html, body {
          width: 210mm;
          height: 297mm;
          margin: 0 !important;
          padding: 0 !important;
          background: #fff !important;
          overflow: visible !important;
        }
        .receipt-a4 {
          width: 210mm !important;
          height: 297mm !important;
          margin: 0 !important;
          padding: 0 !important;
        }
        body * { visibility: hidden !important; }
        #salesReceiptModal, #salesReceiptModal * { visibility: visible !important; }
        #salesReceiptModal {
          position: fixed !important;
          left: 0; top: 0; width: 210mm; height: 297mm;
          background: #fff !important;
          z-index: 9999;
          overflow: visible !important;
          box-shadow: none !important;
        }
        .receipt-half { height: 148.5mm !important; overflow: hidden !important; }
        .receipt-divider { display: none; }
        .modal-header, .modal-footer, .save-btn, .cancel-btn, .close { display: none !important; }
      }
    </style>
  `;

  document.getElementById("salesReceiptBody").innerHTML = html;
  document.getElementById("salesReceiptModal").style.display = "block";
}

function printSalesReceipt() {
  window.print();
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
