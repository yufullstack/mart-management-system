$(document).ready(function() {
    $(document).on('click', '.product-card', function() {
        const barcode = $(this).data('barcode');
        addOrUpdateProduct(barcode);
    });

    const $input = $('#productSearch');
    let lastInputTime = Date.now();
    const scanThreshold = 100; // Time in ms to distinguish between scan and manual input
    let typingTimer;

    // Function to handle input processing
    // function processInput() {
    //     const barcode = $input.val();
    //     $input.val('').focus();
        
    //     addOrUpdateProduct(barcode, function() {
    //         // Reset the product display to show all categories
    //         filterProducts("category", "");
    //     });
    // }

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
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex align-items-center justify-content-between mb-3">
    
                        <span class="list-img">
                        <img src="${imgurl}" class="img-cover" alt="Product Image">
                        </span>
                        <span class="mb-1 px-3">${productName}</span>
                        <span class="mb-1 px-3">${unitPrice}</span>
                        <span class="mb-1">${discount}</span>
                        <span class="mb-1 quantity-group">
                            <button type="button" class="btn-sub" onclick="updateQuantity(this, -1)">-</button>
                            <span class="quantity-display mb-1 px-3">${quantity}</span>
                            <input type="hidden" class="form-control text-center" name="quantity[]" value="${quantity}" min="1" oninput="updateSubtotal(this)">
                            <button type="button" class="btn-add" onclick="updateQuantity(this, 1)">+</button>
                        </span>
                        <span class="mb-1 ">${subtotal}</span>
                        <button type="button" class="btn btn-link" onclick="removeProductRow(this)">x</button>
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

    window.updateSubtotal = function(quantityInput) {
        const productItem = quantityInput.closest('.product-item');
        const unitPrice = parseFloat(productItem.querySelector('input[name="unitprice[]"]').value);
        const discount = parseFloat(productItem.querySelector('input[name="productdiscount[]"]').value);
        const quantity = parseInt(quantityInput.value);

        const subtotal = (quantity * unitPrice * (1 - discount / 100)).toFixed(2);
        productItem.querySelector('input[name="subtotal[]"]').value = subtotal;
        updateTotalAmount();
    }


    window.updateTotalAmount = function() {
        const subtotals = document.querySelectorAll('input[name="subtotal[]"]');
        let totalAmount = 0;
        subtotals.forEach(subtotal => {
            totalAmount += parseFloat(subtotal.value);
        });
        document.getElementById('addTotalAmount').value = totalAmount.toFixed(2);

            // Apply customer discount
    const discount = parseFloat(document.getElementById('addDiscount').value) || 0;
    totalAmount = totalAmount * (1 - discount / 100);
    document.getElementById('addTotalAmount').value = totalAmount.toFixed(2);
    }

    $('#addDiscount').on('input', function() {
        updateTotalAmount();
    });

    window.removeProductRow = function(button) {
        const productRow = button.closest('.product-item');
        productRow.remove();
        updateTotalAmount();
    }

    // window.updateQuantity = function(button, change) {
    //     const quantityInput = button.parentElement.querySelector('input[name="quantity[]"]');
    //     const newQuantity = parseInt(quantityInput.value) + change;
    //     if (newQuantity >= 1) {
    //         quantityInput.value = newQuantity;
    //         updateSubtotal(quantityInput);
    //     }
    // }

    window.updateQuantity = function(button, change) {
        const quantityDisplay = button.closest('.quantity-group').querySelector('.quantity-display');
        const quantityInput = button.closest('.quantity-group').querySelector('input[name="quantity[]"]');
        let newQuantity = parseInt(quantityInput.value) + change;
        if (newQuantity >= 1) {
            quantityInput.value = newQuantity;
            quantityDisplay.textContent = newQuantity; // Update the display
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
                        <div class="col-md-3 mb-3 product-card" data-barcode="${product.barcode}">
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
});



    // window.updateQuantity = function(button, change) {
    //     const quantityInput = button.parentElement.querySelector('input[name="quantity[]"]');
    //     const newQuantity = parseInt(quantityInput.value) + change;
    //     if (newQuantity >= 1) {
    //         quantityInput.value = newQuantity;
    //         updateSubtotal(quantityInput);
    //     }
    // }

    productRow.innerHTML = `
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="mb-1 px-3">${productName}</span>
                <span class="mb-1 px-3">${unitPrice}</span>
                <span class="mb-1 quantity-group">
                    <button type="button" class="btn-sub" onclick="updateQuantity(this, -1)">-</button>
                <   span class="quantity-display mb-1 px-3">${quantity}</>
                    <input type="hidden" class="form-control text-center" name="quantity[]" value="${quantity}" min="1" oninput="updateSubtotal(this)">
                    <button type="button" class="btn-add" onclick="updateQuantity(this, 1)">+</button>
            </span>
                <span class="mb-1 px-3">${subtotal}</span>
                <button type="button" class="btn btn-link" onclick="removeProductRow(this)">x</button>
                <input type="hidden" name="unitprice[]" value="${unitPrice}">
                <input type="hidden" name="Barcode[]" value="${Barcode}">
                <input type="hidden" name="productid[]" value="${productID}">
                <input type="hidden" name="productdiscount[]" value="${discount}">
                <input type="hidden" name="subtotal[]" value="${subtotal}">
            </div>
        </div>
    </div>
`;