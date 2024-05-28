$(document).ready(function() {
    var table = $('#example').DataTable({
        "ajax": "fetch_data.php", // Update this to fetch category data
        "columns": [
            {"data": "categoryid"},
            {"data": "categoryname", "className": "text-truncate"},
            {"data": "created", "className": "text-truncate"},
            {"data": "statusname", "className": "text-truncate"},
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
            // Customize other language options here if needed
        },
        "columnDefs": [{"targets": -1, "orderable": false}]
    });

    function loadOptions() {
        return $.ajax({
            url: 'fetch_options.php', // Adjust this path as necessary
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
        url: 'fetch_options.php',
        type: 'GET',
        success: function(data) {
            var options = JSON.parse(data);
            $.each(options.statuses, function(index, value) {
                $('#addStatus, #editStatus').append('<option value="' + value
                    .statusid +
                    '">' + value.statusname + '</option>');
            });
        }
    });

    function loadOptions() {
        return $.ajax({
            url: 'fetch_options.php', // Adjust this path as necessary
            type: 'GET',
            dataType: 'json'
        });
    }

    // Event listener for edit button click
    $('#example tbody').on('click', 'button.editBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        var categoryId = data.categoryid;

        // Fetch category details based on categoryId
        $.ajax({
            url: 'get_category_details.php', // Adjust this path as necessary
            type: 'GET',
            data: { categoryid: categoryId },
            dataType: 'json',
            success: function(categoryData) {
                console.log("Category data fetched:", categoryData);

                // Set the hidden input field for category ID
                $('#editId').val(categoryData.categoryid);
                // Set the values for other fields
                $('#editName').val(categoryData.categoryname);
                $('#editCreated').val(categoryData.created);

                // Load options and then set the selected values
                loadOptions().done(function(options) {
                    // Clear existing options
                    $('#editStatus').empty();

                    // Populate status options
                    $.each(options.statuses, function(index, value) {
                        $('#editStatus').append('<option value="' +
                            value.statusid + '">' + value
                            .statusname + '</option>');
                    });

                    // Check if the desired value exists in the options and set it as selected
                    $('#editStatus').val(categoryData.statusid);

                    // Show the modal
                    $('#editModal').modal('show');
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching category details:", status, error);
            }
        });
    });

    // Add new record
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'add_record.php', // Adjust this path as necessary
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
                console.error("Error editing record:", status, error);
            }
        });
    });

    // Delete record
    $('#example tbody').on('click', 'button.deleteBtn', function() {
        var data = table.row($(this).parents('tr')).data();
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: 'delete_record.php', // Adjust this path as necessary
                type: 'POST',
                data: { categoryid: data.categoryid },
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
        $('#categoryId').val(data.categoryid);
        $('#categoryName').val(data.categoryname);
        $('#categoryCreated').val(data.created);
        $('#categoryStatus').val(data.statusname);

        // Show the modal
        $('#categoryModal').modal('show');
    });
});

// Import and export functions
function exportData() {
    $.ajax({
        url: 'export_data.php', // Adjust this path as necessary
        type: 'GET',
        success: function(data) {
            var csvContent = "data:text/csv;charset=utf-8," + encodeURIComponent(data);
            var link = document.createElement("a");
            link.setAttribute("href", csvContent);
            link.setAttribute("download", "category_data.csv");
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
        url: 'import_data.php', // Adjust this path as necessary
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
                // Optionally reload table to reflect imported data
                $('#example').DataTable().ajax.reload();
            } else {
                alert(response.error);
            }
        },
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}
