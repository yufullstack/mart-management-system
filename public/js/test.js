$(document).ready(function () {
  $(document).on("click", ".product-card", function () {
    const barcode = $(this).data("barcode");
    addOrUpdateProduct(barcode);
  });

  const $input = $("#productSearch");
  let lastInputTime = Date.now();
  const scanThreshold = 100;
  let typingTimer;

  $input.on("input", function () {
    const currentTime = Date.now();
    const timeDiff = currentTime - lastInputTime;
    lastInputTime = currentTime;

    clearTimeout(typingTimer);

    if (timeDiff < scanThreshold) {
      processInput();
    } else {
      typingTimer = setTimeout(function () {
        filterProducts("search", $input.val());
      }, scanThreshold);
    }
  });

  $("#productSearch").on("keypress", function (event) {
    if (event.which === 13) {
      const barcode = $(this).val();
      $("#productSearch").val("").focus();
      addOrUpdateProduct(barcode, function () {
        filterProducts("category", "");
      });
    }
  });

  function addOrUpdateProduct(barcode, callback) {
    $.ajax({
      url: "fetch_p.php",
      type: "GET",
      data: { Barcode: barcode },
      dataType: "json",
      success: function (productDetails) {
        if (productDetails.error) {
          console.error("Product not found");
          return;
        }

        const productName = productDetails.productName;
        const unitPrice = parseFloat(productDetails.unitPrice);
        const discount = parseFloat(productDetails.discount) || 0;
        const productid = productDetails.productid;
        const productimages = productDetails.productimages;

        const existingProductRow = $(
          `input[name="Barcode[]"][value="${barcode}"]`
        ).closest(".product-item");

        if (existingProductRow.length > 0) {
          const quantityInput = existingProductRow.find(
            'input[name="quantity[]"]'
          );
          const newQuantity = parseInt(quantityInput.val()) + 1;
          quantityInput.val(newQuantity);
          updateSubtotal(quantityInput[0]);
        } else {
          addProductRow(
            barcode,
            productName,
            unitPrice,
            discount,
            productid,
            productimages
          );
        }

        if (typeof callback === "function") {
          callback();
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching product details:", error);
      },
    });
  }

  function addProductRow(
    Barcode,
    productName,
    unitPrice,
    discount,
    productID,
    productimages
  ) {
    const productsContainer = document.getElementById("cart");
    const productRow = document.createElement("div");
    productRow.classList.add("product-item", "row", "mb-3");

    const quantity = 1;
    const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);
    var imgurl = "../../public/img/" + productimages;
    productRow.innerHTML = `
            <div class="row p-0 m-0">
                <div class="col-md-12 m-0 p-0">
                    <div class="d-flex align-items-center justify-content-between m-0 p-0">
                        <div class="col-2 text-center">${productName}</div>
                        <div class="col-2 text-center">${unitPrice}</div>
                        <div class="col-1">${discount} %</div>
                        <div class="col-3">
                           <div class="input-group">
                            <button type="button" class="btn btn-outline-primary" onclick="updateQuantity(this, -1)">-</button>
                            <input type="text" class="form-control text-center border-primary" name="quantity[]" value="${quantity}" min="1" oninput="updateSubtotal(this)">
                            <button type="button" class="btn btn-outline-primary" onclick="updateQuantity(this, 1)">+</button>
                           </div>
                        </div>
                        <div class="col-2 text-end subtotal-display">${subtotal}</div>
                        <button type="button" class="btn fs-3" onclick="removeProductRow(this)"><i class="fa-solid fa-square-xmark text-primary"></i></button>
                        <input type="hidden" name="unitprice[]" value="${unitPrice}">
                        <input type="hidden" name="Barcode[]" value="${Barcode}">
                        <input type="hidden" name="productid[]" value="${productID}">
                        <input type="hidden" name="productdiscount[]" value="${discount}">
                        <input type="hidden" name="subtotal[]" value="${subtotal}">
                    </div>
                </div>
            </div>
        `;

    productsContainer.appendChild(productRow);
    updateTotalAmount();
  }

  window.onload = function () {
    document.getElementById("totalBeforeDiscount").value = "0.00";
    document.getElementById("productDiscountAmount").value = "0.00";
    document.getElementById("customerDiscountAmount").value = "0.00";
    document.getElementById("totalDiscountAmount").value = "0.00";
    document.getElementById("totalAmount").value = "0.00";

    document.getElementById("totalbeforediscountdisplay").textContent = "0.00";
    document.getElementById("productDiscountAmountdisplay").textContent =
      "0.00";
    document.getElementById("customerDiscountAmountdisplay").textContent =
      "0.00";
    document.getElementById("totalDiscountAmountdisplay").textContent = "0.00";
    document.getElementById("paymentAmount1").value = "0.00";
    document.getElementById("totalAmountdisplay").textContent = "0.00";
  };

  window.updateSubtotal = function (quantityInput) {
    const productItem = quantityInput.closest(".product-item");
    const unitPrice = parseFloat(
      productItem.querySelector('input[name="unitprice[]"]').value
    );
    const discount = parseFloat(
      productItem.querySelector('input[name="productdiscount[]"]').value
    );
    const quantity = parseInt(quantityInput.value);

    const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);
    productItem.querySelector('input[name="subtotal[]"]').value = subtotal;
    productItem.querySelector('.subtotal-display').textContent = subtotal;
    productItem.querySelector('input[name="subtotal[]"]').value = subtotal;
    updateTotalAmount();
  };

  window.updateTotalAmount = function () {
    const subtotals = document.querySelectorAll('input[name="subtotal[]"]');
    const unitPrices = document.querySelectorAll('input[name="unitprice[]"]');
    const discounts = document.querySelectorAll(
      'input[name="productdiscount[]"]'
    );
    let totalAmountBeforeDiscount = 0;
    let productDiscountAmount = 0;

    subtotals.forEach((subtotal, index) => {
      const unitPrice = parseFloat(unitPrices[index].value);
      const discount = parseFloat(discounts[index].value);
      const quantity = parseInt(
        subtotal
          .closest(".product-item")
          .querySelector('input[name="quantity[]"]').value
      );
      const productTotalBeforeDiscount = unitPrice * quantity;
      totalAmountBeforeDiscount += productTotalBeforeDiscount;
      productDiscountAmount += productTotalBeforeDiscount * (discount / 100);
    });

    document.getElementById("totalBeforeDiscount").value =
      totalAmountBeforeDiscount.toFixed(2);
    document.getElementById("totalbeforediscountdisplay").textContent =
      totalAmountBeforeDiscount.toFixed(2);
    document.getElementById("productDiscountAmount").value =
      productDiscountAmount.toFixed(2);
    document.getElementById("productDiscountAmountdisplay").textContent =
      productDiscountAmount.toFixed(2);

    const customerDiscount =
      parseFloat(document.getElementById("addDiscount").value) || 0;
    const customerDiscountAmount =
      (totalAmountBeforeDiscount - productDiscountAmount) *
      (customerDiscount / 100);
    const totalDiscountAmount = productDiscountAmount + customerDiscountAmount;
    const totalAmount = totalAmountBeforeDiscount - totalDiscountAmount;

    document.getElementById("customerDiscountAmount").value =
      customerDiscountAmount.toFixed(2);
    document.getElementById("customerDiscountAmountdisplay").textContent =
      customerDiscountAmount.toFixed(2);
    document.getElementById("totalDiscountAmount").value =
      totalDiscountAmount.toFixed(2);
    document.getElementById("totalDiscountAmountdisplay").textContent =
      totalDiscountAmount.toFixed(2);
    document.getElementById("totalAmount").value = totalAmount.toFixed(2);

    document.getElementById(
      "exactAmountButton"
    ).textContent = `${totalAmount.toFixed(2)}`;
    // document.getElementById("paymentAmount1").value = totalAmount.toFixed(2);
    document.getElementById("totalAmountdisplay").textContent =
      totalAmount.toFixed(2);
  };

  window.updateQuantity = function (button, delta) {
    const input = button.closest(".input-group").querySelector("input");
    let newValue = parseInt(input.value) + delta;
    if (newValue < 1) {
      newValue = 1;
    }
    input.value = newValue;
    updateSubtotal(input);
  };

  $("#addDiscount").on("input", function () {
    updateTotalAmount();
  });

  window.removeProductRow = function (button) {
    const productRow = button.closest(".product-item");
    productRow.remove();
    updateTotalAmount();
  };

  
  function filterProducts(type, value) {
    var data = {};
    data[type] = value;

    $.ajax({
        url: "fetch_products_by_category.php",
        type: "GET",
        data: data,
        dataType: "json",
        success: function(response) {
            $("#productList").empty();
            response.forEach(function(product) {
                var imageUrl = "../../public/img/" + product.productimage;
                var stockStatus = product.stocklevel > 0 ? "" : "<p class='bg-danger px-2 fs-6 fst-normal rounded-end text-white'>Out</p>";
                var cardClass = product.stocklevel > 0 ? '' : 'out-of-stock';
                var cardHtml = `
                    <div class="col-md-3 p-2 product-card ${cardClass}" data-barcode="${product.barcode}">
                        <div class="card position-relative">
                            <div class="card-top">
                                <span class="out-of-stock position-absolute top-0 start-0"> ${stockStatus}</span>
                                <img src="${imageUrl}" class="img-cover" alt="Product Image">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${product.productname}</h5>
                                <p class="card-text">Price: ${product.priceout}</p>
                                
                            </div>
                        </div>
                    </div>`;
                $("#productList").append(cardHtml);
            });

            // Make out-of-stock product cards unclickable
            $(".product-card.out-of-stock").each(function() {
                $(this).css('pointer-events', 'none');
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching products:", xhr.responseText || error);
        },
    });
}

  // Initial category load and click handling
  $('.category-btn[data-category="All Categories"]').addClass("active");
  $(".category-btn").on("click", function () {
    $(".category-btn").removeClass("active");
    $(this).addClass("active");
    var category = $(this).data("category");
    filterProducts("category", category === "All Categories" ? "" : category);
  });

  // Initial product list load
  filterProducts("category", "");

  $(document).ready(function () {
    $("#addForm").on("submit", function (e) {
      e.preventDefault();

      // Store order details temporarily (e.g., in session storage)
      const orderData = $(this).serializeArray();
      sessionStorage.setItem("orderData", JSON.stringify(orderData));

      // Open the payment modal
      populatePaymentMethods("#paymentMethod1");
      $("#paymentModal").modal("show");
    });
  });

  let paymentMethodIndex = 1; // Initialize the index

  $("#addPaymentMethod").on("click", function () {
    paymentMethodIndex++;
    const newPaymentMethod = `
        <div class="payment-entry" id="paymentEntry${paymentMethodIndex}">
            <div class="row">
                <div class="col-6">
                    <label for="paymentAmount${paymentMethodIndex}" class="form-label">Payment Amount ${paymentMethodIndex}</label>
                    <input type="number" class="form-control" id="paymentAmount${paymentMethodIndex}" name="paymentamount[]" required>
                </div>
                <div class="col-6">
                    <label for="paymentMethod${paymentMethodIndex}" class="form-label">Payment Method ${paymentMethodIndex}</label>
                    <select class="form-select" id="paymentMethod${paymentMethodIndex}" name="paymentmethodid[]" required></select>
                </div>
                <div class="col-12 text-end mt-2">
                    <button type="button" class="btn btn-danger removePaymentMethod" data-index="${paymentMethodIndex}">Remove</button>
                </div>
            </div>
        </div>`;
    $("#paymentMethodsContainer").append(newPaymentMethod);
    populatePaymentMethods(`#paymentMethod${paymentMethodIndex}`);
  });

  $("#paymentMethodsContainer").on(
    "click",
    ".removePaymentMethod",
    function () {
      const index = $(this).data("index");
      $(`#paymentEntry${index}`).remove();
    }
  );

  function populatePaymentMethods(selector) {
    $.ajax({
      url: "fetch_payment_methods.php",
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const options = response.methods.map(
            (method) =>
              `<option value="${method.paymentmethodid}">${method.paymentmethodname}</option>`
          );
          $(selector).empty().append(options);
        }
      },
    });
  }

  $("#paymentForm").on("input", 'input[name="paymentamount[]"]', function () {
    calculateRefund();
  });

  function calculateRefund() {
    const totalPayment = parseFloat($("#totalPayment").val()) || 0;
    let totalGiven = 0;

    $('input[name="paymentamount[]"]').each(function () {
      totalGiven += parseFloat($(this).val()) || 0;
    });

    const refundAmount = totalGiven - totalPayment;
    $("#refundAmount").val(refundAmount.toFixed(2));
    $("#totalrefund").text(refundAmount.toFixed(2));
  }

  $("#paymentForm").on("submit", async function (e) {
    e.preventDefault();
    const totalPayment = parseFloat($("#totalPayment").val()) || 0;
    let totalGiven = 0;

    $('input[name="paymentamount[]"]').each(function () {
      totalGiven += parseFloat($(this).val()) || 0;
    });

    const paymentData = $(this).serializeArray();
    paymentData.push({ name: "totalpayment", value: totalPayment });

    try {
      // Retrieve temporary order data from session storage
      const orderData = JSON.parse(sessionStorage.getItem("orderData"));

      // Insert order into tblorder
      const orderResponse = await $.ajax({
        url: "complete_order.php",
        type: "POST",
        data: orderData,
        dataType: "json",
      });

      if (orderResponse.success) {
        const orderId = orderResponse.orderid;

        // Add orderId to payment data
        paymentData.push({ name: "orderid", value: orderId });

        // Submit payment
        const paymentResponse = await $.ajax({
          url: "submit_payment.php",
          type: "POST",
          data: paymentData,
          dataType: "json",
        });

        if (paymentResponse.success) {
          const refundAmount = totalGiven - totalPayment;
          if (refundAmount > 0) {
            // Submit refund
            await $.ajax({
              url: "store_refund.php",
              type: "POST",
              data: {
                orderid: orderId,
                refundamount: refundAmount.toFixed(2),
              },
              dataType: "json",
            });
          }
          $("#paymentModal").modal("hide");
          // alert('Payment and order recorded successfully.');
          sessionStorage.removeItem("orderData"); 

          // Fetch and display the invoice
          fetchInvoice();
        } else {
          alert("Error submitting payment: " + paymentResponse.message);
        }
      } else {
        alert("Error submitting order: " + orderResponse.message);
      }
    } catch (error) {
      console.error("Error during payment and order submission:", error);
      alert(
        "Error during payment and order submission: " + JSON.stringify(error)
      );
    }
  });

  function fetchInvoice() {
    $.ajax({
      url: "fetch_invoice.php",
      type: "GET",
      dataType: "html",
      success: function (response) {
        $("#invoice-box").html(response);
        $("#invoiceModal").modal("show");
      },
      error: function (xhr, status, error) {
        console.error("Error fetching invoice:", error);
      },
    });
  }

  // print
  $("#printInvoice").on("click", function () {
    const printContent = document.getElementById("invoice-box").innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
  });

  $("#exactAmountButton").on("click", function () {
    const totalPayment = parseFloat($("#totalPayment").val()) || 0;
    $("#paymentAmount1").val(totalPayment.toFixed(2));
    calculateRefund(); // Recalculate the refund amount if necessary
  });

  $(document).on("click", ".quick-cash-btn", function () {
    const amount = parseFloat($(this).data("amount"));
    $("#paymentAmount1").val(amount.toFixed(2));
    calculateRefund();
  });

  $("#paymentModal").on("show.bs.modal", function () {
    // Fetch the order total and total items for display
    fetchOrderTotal();
  });

  function fetchOrderTotal() {
    const orderData = JSON.parse(sessionStorage.getItem("orderData"));
    const totalPayment = orderData.find(
      (item) => item.name === "totalamount"
    ).value;
    $("#totalPayment").val(totalPayment);
    $("#totalamount_display").text(totalPayment);

    // Calculate total items
    let totalItems = 0;
    orderData.forEach((item) => {
      if (item.name === "quantity[]") {
        totalItems += parseInt(item.value, 10);
      }
    });
    $("#totalItems").text(totalItems);
  }

  // Add customer record
  $("#addForm_customer").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: "../customer/add_record.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data) {
        alert("Insert Customer successfully.");
        $("#addModal").modal("hide");
        table.ajax.reload();
      },
      error: function (xhr, status, error) {
        console.error("Error adding record:", status, error);
      },
    });
  });

  // option select
  function loadOptions() {
    return $.ajax({
      url: "../customer/fetch_options.php", // Adjust this path as necessary
      type: "GET",
      dataType: "json",
      success: function (data) {
        console.log("Options loaded:", data);
      },
      error: function (xhr, status, error) {
        console.error("Failed to load options:", status, error);
      },
    });
  }

  $.ajax({
    url: "../customer/fetch_options.php",
    type: "GET",
    success: function (data) {
      var options = JSON.parse(data);
      $.each(options.genders, function (index, value) {
        $("#addGender, #editGender").append(
          '<option value="' + value.sexid + '">' + value.sexen + "</option>"
        );
      });
      $.each(options.statuses, function (index, value) {
        $("#addStatus, #editStatus").append(
          '<option value="' +
            value.statusid +
            '">' +
            value.statusname +
            "</option>"
        );
      });
    },
  });

  function loadOptions() {
    return $.ajax({
      url: "../customer/fetch_options.php",
      type: "GET",
      dataType: "json",
    });
  }
});
