$(document).ready(function() {
    $(document).on('click', '.product-card', function() {
        const barcode = $(this).data('barcode');
        addOrUpdateProduct(barcode);
    });

    const $input = $('#productSearch');
    let lastInputTime = Date.now();
    const scanThreshold = 100; // Time in ms to distinguish between scan and manual input
    let typingTimer;

    // Handle input events
    $input.on('input', function() {
        const currentTime = Date.now();
        const timeDiff = currentTime - lastInputTime;
        lastInputTime = currentTime;

        clearTimeout(typingTimer);

        if (timeDiff < scanThreshold) {
            // Treat this as part of a barcode scan
            processInput();
        } else {
            // Treat this as manual input
            typingTimer = setTimeout(function() {
                filterProducts('search', $input.val());
            }, scanThreshold);
        }
    });

    // Handle Enter key press specifically for barcode input
    $("#productSearch").on("keypress", function(event) {
        if (event.which === 13) {
            const barcode = $(this).val();
            $('#productSearch').val('').focus();
            addOrUpdateProduct(barcode, function() {
                filterProducts("category", "");
            });
        }
    });

    function addOrUpdateProduct(barcode, callback) {
        $.ajax({
            url: 'fetch_p.php',
            type: 'GET',
            data: { Barcode: barcode },
            dataType: 'json',
            success: function(productDetails) {
                if (productDetails.error) {
                    console.error("Product not found");
                    return;
                }

                const productName = productDetails.productName;
                const unitPrice = parseFloat(productDetails.unitPrice);
                const discount = parseFloat(productDetails.discount) || 0;
                const productid = productDetails.productid;
                const productimages = productDetails.productimages;

                const existingProductRow = $(`input[name="Barcode[]"][value="${barcode}"]`).closest('.product-item');

                if (existingProductRow.length > 0) {
                    const quantityInput = existingProductRow.find('input[name="quantity[]"]');
                    const newQuantity = parseInt(quantityInput.val()) + 1;
                    quantityInput.val(newQuantity);
                    existingProductRow.find('.quantity-display').text(newQuantity); // Update the quantity display
                    updateSubtotal(quantityInput[0]);
                } else {
                    addProductRow(barcode, productName, unitPrice, discount, productid, productimages);
                }

                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching product details:", error);
            }
        });
    }

    function addProductRow(Barcode, productName, unitPrice, discount, productID, productimages) {
        const productsContainer = document.getElementById('cart');
        const productRow = document.createElement('div');
        productRow.classList.add('product-item', 'row', 'mb-3');

        const quantity = 1;
        const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);
        var imgurl = '../../public/img/' + productimages;
        productRow.innerHTML = `
            <div class="row p-0">
                <div class="col-md-12">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="col-2 list-img">
                            <img src="${imgurl}" class="img-cover" alt="Product Image">
                        </div>
                        <div class="col-2 text-center">${productName}</div>
                        <div class="col-2 text-center">${unitPrice}</div>
                        <div class="col-1">${discount} %</div>
                        <div class="col-3 quantity-group text-center">
                            <button type="button" class="btn-sub" onclick="updateQuantity(this, -1)">-</button>
                            <span class="quantity-display mb-1 px-3">${quantity}</span>
                            <input type="hidden" class="form-control text-center" name="quantity[]" value="${quantity}" min="1" oninput="updateSubtotal(this)">
                            <button type="button" class="btn-add" onclick="updateQuantity(this, 1)">+</button>
                        </div>
                        <div class="col-2 text-end">${subtotal}</div>
                        <button type="button" class="btn fs-3 " onclick="removeProductRow(this)"><i class="fa-solid fa-square-xmark text-primary"></i></button>
                        <input type="hidden" name="unitprice[]" value="${unitPrice}">
                        <input type="hidden" name="Barcode[]" value="${Barcode}">
                        <input type="hidden" name="productid[]" value="${productID}">
                        <input type="hidden" name="productdiscount[]" value="${discount}">
                        <input type="hidden" name="subtotal[]" value="${subtotal}">
                    </div>
                </div>
            </div>
            <hr>
        `;

        productsContainer.appendChild(productRow);
        updateTotalAmount();
    }

    window.updateSubtotal = function(quantityInput) {
        const productItem = quantityInput.closest('.product-item');
        const unitPrice = parseFloat(productItem.querySelector('input[name="unitprice[]"]').value);
        const discount = parseFloat(productItem.querySelector('input[name="productdiscount[]"]').value);
        const quantity = parseInt(quantityInput.value);

        const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);
        productItem.querySelector('input[name="subtotal[]"]').value = subtotal;
        productItem.querySelector('.quantity-display').textContent = quantity; // Update the quantity display
        updateTotalAmount();
    }

    window.updateTotalAmount = function() {
        const subtotals = document.querySelectorAll('input[name="subtotal[]"]');
        const unitPrices = document.querySelectorAll('input[name="unitprice[]"]');
        const discounts = document.querySelectorAll('input[name="productdiscount[]"]');
        let totalAmountBeforeDiscount = 0;
        let productDiscountAmount = 0;

        subtotals.forEach((subtotal, index) => {
            const unitPrice = parseFloat(unitPrices[index].value);
            const discount = parseFloat(discounts[index].value);
            const quantity = parseInt(subtotal.closest('.product-item').querySelector('input[name="quantity[]"]').value);
            const productTotalBeforeDiscount = unitPrice * quantity;
            totalAmountBeforeDiscount += productTotalBeforeDiscount;
            productDiscountAmount += productTotalBeforeDiscount * (discount / 100);
        });

        document.getElementById('totalBeforeDiscount').value = totalAmountBeforeDiscount.toFixed(2);
        document.getElementById('productDiscountAmount').value = productDiscountAmount.toFixed(2);

        // Apply customer discount
        const customerDiscount = parseFloat(document.getElementById('addDiscount').value) || 0;
        const customerDiscountAmount = (totalAmountBeforeDiscount - productDiscountAmount) * (customerDiscount / 100);
        const totalDiscountAmount = productDiscountAmount + customerDiscountAmount;
        const totalAmount = totalAmountBeforeDiscount - totalDiscountAmount;

        document.getElementById('customerDiscountAmount').value = customerDiscountAmount.toFixed(2);
        document.getElementById('totalDiscountAmount').value = totalDiscountAmount.toFixed(2);
        document.getElementById('totalAmount').value = totalAmount.toFixed(2);
    }

    $('#addDiscount').on('input', function() {
        updateTotalAmount();
    });

    $('#addDiscount').on('input', function() {
        updateTotalAmount();
    });

    window.removeProductRow = function(button) {
        const productRow = button.closest('.product-item');
        productRow.remove();
        updateTotalAmount();
    }

    window.updateQuantity = function(button, change) {
        const quantityDisplay = button.closest('.quantity-group').querySelector('.quantity-display');
        const quantityInput = button.closest('.quantity-group').querySelector('input[name="quantity[]"]');
        let newQuantity = parseInt(quantityInput.value) + change;
        if (newQuantity >= 1) {
            quantityInput.value = newQuantity;
            quantityDisplay.textContent = newQuantity; 
            updateSubtotal(quantityInput);
        }
    }

    function filterProducts(type, value) {
        var data = {};
        data[type] = value;

        $.ajax({
            url: 'fetch_products_by_category.php',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function(response) {
                $('#productList').empty();
                response.forEach(function(product) {
                    var imageUrl = '../../public/img/' + product.productimage;
                    var cardHtml = `
                        <div class="col-md-3 p-3 product-card" data-barcode="${product.barcode}">
                            <div class="card">
                                <div class="card-top">
                                    <img src="${imageUrl}" class="img-cover" alt="Product Image">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">${product.productname}</h5>
                                    <p class="card-text">Price: ${product.priceout}</p>
                                </div>
                            </div>
                        </div>`;
                    $('#productList').append(cardHtml);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching products:", error);
            }
        });
    }

    // Initial category load and click handling
    $('.category-btn[data-category="All Categories"]').addClass('active');
    $('.category-btn').on('click', function() {
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        var category = $(this).data('category');
        filterProducts('category', category === 'All Categories' ? '' : category);
    });

    // Initial product list load
    filterProducts('category', '');
    // new order
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'add_record.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                alert('Order completed successfully.');
                $('#addForm')[0].reset();
                $('#cart').empty();
                updateTotalAmount();
            },
            error: function(xhr, status, error) {
                console.error("Error adding record:", status, error);
                alert('Error adding record: ' + error);
            }
        });
    });


        // Add customer record
        $('#addForm_customer').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '../customer/add_record.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    alert('Insert Customer successfully.');
                    $('#addModal').modal('hide');
                    table.ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error("Error adding record:", status, error);
                }
            });
        });

    // option select
    function loadOptions() {
        return $.ajax({
            url: '../customer/fetch_options.php', // Adjust this path as necessary
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("Options loaded:", data);
            },
            error: function(xhr, status, error) {
                console.error("Failed to load options:", status, error);
            }
        });
    }

    $.ajax({
        url: '../customer/fetch_options.php',
        type: 'GET',
        success: function(data) {
            var options = JSON.parse(data);
            $.each(options.genders, function(index, value) {
                $('#addGender, #editGender').append('<option value="' + value
                    .sexid +
                    '">' + value.sexen + '</option>');
            });
            $.each(options.statuses, function(index, value) {
                $('#addStatus, #editStatus').append('<option value="' + value
                    .statusid +
                    '">' + value.statusname + '</option>');
            });
        }
    });

    function loadOptions() {
        return $.ajax({
            url: '../customer/fetch_options.php',
            type: 'GET',
            dataType: 'json'
        });
    }
});
