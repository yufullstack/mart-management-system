$(document).ready(function() {
    var table = $('#example').DataTable({
        "ajax": "fetch_data.php",
        "columns": [
            {"data": "productid"},
            {"data": "productname", "className": "text-truncate"},
            {"data": "categoryname", "className": "text-truncate"},
            {"data": "suppliername", "className": "text-truncate"},
            {"data": "pricein"},
            {"data": "priceout"},
            {
                "data": null,
                "className": "center",
                "defaultContent": '<div class="dropdown">' +
                    '<button class="btn btn-primary dropdown-toggle actionIcon" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-expanded="false">' +
                    'Action' +
                    '</button>' +
                    '<ul class="dropdown-menu" aria-labelledby="actionDropdown">' +
                    '<li><button type="button" class="dropdown-item editBtn text-warning"><i class="fas fa-edit"></i> Edit</button></li>' +
                    '<li><button type="button" class="dropdown-item viewBtn text-secondary"><i class="fas fa-eye"></i> View</button></li>' +
                    '<li><button type="button" class="dropdown-item deleteBtn text-danger"><i class="fas fa-trash-alt"></i> Delete</button></li>' +
                    '</ul>' +
                    '</div>'
            }
        ],
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        "language": {
            "search": "Filter rows:",
            "lengthMenu": "Number of rows: _MENU_",
        },
        "columnDefs": [{
            "targets": -1,
            "orderable": false
        }]
    });

    function loadOptions() {
        return $.ajax({
            url: 'fetch_options.php',
            type: 'GET',
            dataType: 'json'
        });
    }

    loadOptions().done(function(options) {
        // Populate category options
        $.each(options.categories, function(index, value) {
            $('#addCategory, #editCategory').append('<option value="' + value.categoryid + '">' + value.categoryname + '</option>');
        });

        // Populate supplier options
        $.each(options.suppliers, function(index, value) {
            $('#addSupplier, #editSupplier').append('<option value="' + value.supplierid + '">' + value.suppliername + '</option>');
        });

        // Populate status options
        $.each(options.statuses, function(index, value) {
            $('#addStatus, #editStatus').append('<option value="' + value.statusid + '">' + value.statusname + '</option>');
        });
    });

    $('#example tbody').on('click', 'button.editBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        var productId = data.productid;

        $.ajax({
            url: 'get_product_details.php',
            type: 'GET',
            data: { productid: productId },
            dataType: 'json',
            success: function(productData) {
                $('#editProductId').val(productData.productid);
                $('#editName').val(productData.productname);
                $('#editCategory').val(productData.categoryid);
                $('#editSupplier').val(productData.supplierid);
                $('#editQuantity').val(productData.quantity);
                $('#editPriceIn').val(productData.pricein);
                $('#editPriceOut').val(productData.priceout);
                $('#editInStock').val(productData.instock);
                $('#editStatus').val(productData.statusid);

                $('#editModal').modal('show');
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'edit_record.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                $('#editModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    });


    $('#example tbody').on('click', 'button.viewBtn', function() {
        var data = table.row($(this).parents('tr')).data();

        var imageUrl = '../../public/img/' + data.productimage;
        $('#productImage').attr('src', imageUrl);

        $('#productId').val(data.productid);
        $('#productName').val(data.productname);
        $('#productCategory').val(data.categoryname);
        $('#productSupplier').val(data.suppliername);
        $('#productQuantity').val(data.quantity);
        $('#productPriceIn').val(data.pricein);
        $('#productPriceOut').val(data.priceout);
        $('#productInStock').val(data.instock);
        $('#productDate').val(data.productdate);
        $('#productStatus').val(data.statusname);

        var productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
    });



    // Add new record
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
                $('#addModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    });


    // Delete record
    $('#example tbody').on('click', 'button.deleteBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: 'delete_record.php',
                type: 'POST',
                data: {
                    productid: data.productid
                },
                success: function(data) {
                    table.ajax.reload();
                },
               
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                }
            });
        }
    });
});

 // Import and export functions
 function exportData() {
    $.ajax({
        url: 'export_data.php',
        type: 'GET',
        success: function(data) {
            var csvContent = "data:text/csv;charset=utf-8," + encodeURIComponent(data);
            var link = document.createElement("a");
            link.setAttribute("href", csvContent);
            link.setAttribute("download", "product_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            alert("Export Data Successfully");
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
            alert("Error: " + error);
        }
    });
}

function importData(file) {
    if (!file) {
        alert("Please select a file to upload.");
        return;
    }

    var formData = new FormData();
    formData.append('file', file);

    $.ajax({
        url: 'import_data.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    alert("Error parsing response: " + e.message);
                    return;
                }
            }

            if (response.success) {
                alert("Data imported successfully");
            } else {
                alert(response.error);
            }
        },
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}


