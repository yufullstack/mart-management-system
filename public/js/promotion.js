$(document).ready(function() {
    var table = $('#example').DataTable({
        "ajax": "fetch_promotion_data.php",
        "columns": [
            {"data": "discountid"},
            {"data": "productname", "className": "text-truncate"},
            {"data": "discountvalue", "className": "text-truncate"},
            {"data": "startdate", "className": "text-truncate"},
            {"data": "enddate", "className": "text-truncate"},
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
        "columnDefs": [{"targets": -1, "orderable": false}]
    });

    // Event listener for edit button click
    $('#example tbody').on('click', 'button.editBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        var discountId = data.discountid;

        // Fetch promotion details based on discountId
        $.ajax({
            url: 'get_promotion_details.php', // Adjust this path as necessary
            type: 'GET',
            data: { discountid: discountId },
            dataType: 'json',
            success: function(promotionData) {
                console.log("Promotion data fetched:", promotionData);

                // Set the hidden input field for discount ID
                $('#editId').val(promotionData.discountid);
                // Set the values for other fields
                $('#editProductId').val(promotionData.productid);
                $('#editDiscountValue').val(promotionData.discountvalue);
                $('#editStartDate').val(promotionData.startdate);
                $('#editEndDate').val(promotionData.enddate);

                // Show the modal
                $('#editModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching promotion details:", status, error);
            }
        });
    });

    // Add new record
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'add_promotion_record.php', // Adjust this path as necessary
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                $('#addModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error adding record:", status, error);
            }
        });
    });

    // Edit record
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'edit_promotion_record.php', // Adjust this path as necessary
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                $('#editModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error editing record:", status, error);
            }
        });
    });

    // Delete record
    $('#example tbody').on('click', 'button.deleteBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: 'delete_promotion_record.php', // Adjust this path as necessary
                type: 'POST',
                data: { discountid: data.discountid },
                success: function(data) {
                    table.ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error("Error deleting record:", status, error);
                }
            });
        }
    });

    // View record
    $('#example tbody').on('click', 'button.viewBtn', function() {
        var data = table.row($(this).parents('tr')).data();

        // Set other form fields
        $('#promotionId').val(data.discountid);
        $('#promotionProductName').val(data.productname);
        $('#promotionDiscountValue').val(data.discountvalue);
        $('#promotionStartDate').val(data.startdate);
        $('#promotionEndDate').val(data.enddate);

        // Show the modal
        $('#promotionModal').modal('show');
    });
});
